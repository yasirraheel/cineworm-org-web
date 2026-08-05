import 'dotenv/config';
import express from 'express';
import fs from 'fs/promises';
import path from 'path';
import pino from 'pino';
import QRCode from 'qrcode';
import makeWASocket, {
  DisconnectReason,
  fetchLatestBaileysVersion,
  useMultiFileAuthState,
} from '@whiskeysockets/baileys';

const app = express();
const logger = pino({ level: process.env.WA_LOG_LEVEL || 'info' });

const host = process.env.WA_SERVER_HOST || '127.0.0.1';
const port = Number(process.env.WA_SERVER_PORT || 3025);
const apiKey = process.env.WA_SERVER_API_KEY || 'change-this-long-random-key';
const baseAuthDir = process.env.WA_AUTH_DIR || 'auth';
const qrIdleTimeoutMs = Number(process.env.WA_QR_IDLE_TIMEOUT_MS || 120000);

const sessions = new Map();

app.use(express.json({ limit: '1mb' }));

function requireApiKey(req, res, next) {
  if (req.header('x-api-key') !== apiKey) {
    return res.status(401).json({ ok: false, error: 'Unauthorized' });
  }

  return next();
}

function getSessionId(req) {
  const raw = req.header('x-session-id') || req.query.sessionId || req.body?.sessionId || 'admin';
  return String(raw).trim().replace(/[^a-zA-Z0-9_-]/g, '_') || 'admin';
}

function getOrCreateSession(sessionId) {
  if (!sessions.has(sessionId)) {
    const authDir = sessionId === 'admin' ? baseAuthDir : path.join(baseAuthDir, `session_${sessionId}`);
    sessions.set(sessionId, {
      id: sessionId,
      authDir,
      sock: null,
      lastQr: null,
      lastQrDataUrl: null,
      connectionStatus: 'disconnected',
      connectedNumber: null,
      lastError: null,
      starting: false,
      manualStop: false,
      reconnectAllowed: false,
      qrIdleTimer: null,
      unopenedFailureCount: 0,
      sessionHadQr: false,
    });
  }

  return sessions.get(sessionId);
}

function formatCleanNumber(jidOrId) {
  if (!jidOrId) return null;
  const numOnly = String(jidOrId).split('@')[0].split(':')[0].replace(/[^\d]/g, '');
  return numOnly ? `+${numOnly}` : null;
}

function serializeStatus(session) {
  return {
    ok: true,
    sessionId: session.id,
    status: session.connectionStatus,
    connected: session.connectionStatus === 'connected',
    connectedNumber: formatCleanNumber(session.connectedNumber || session.sock?.user?.id),
    hasQr: Boolean(session.lastQrDataUrl),
    qrDataUrl: session.lastQrDataUrl,
    lastError: session.lastError,
  };
}

app.get('/qr', requireApiKey, (req, res) => {
  const session = getOrCreateSession(getSessionId(req));
  res.json(serializeStatus(session));
});

function normalizeNumber(value) {
  return String(value || '').replace(/[^\d]/g, '');
}

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function randomInt(min, max) {
  const safeMin = Math.max(0, Number(min) || 0);
  const safeMax = Math.max(safeMin, Number(max) || safeMin);
  return Math.floor(Math.random() * (safeMax - safeMin + 1)) + safeMin;
}

function clearQrIdleTimer(session) {
  if (session.qrIdleTimer) {
    clearTimeout(session.qrIdleTimer);
    session.qrIdleTimer = null;
  }
}

function scheduleQrIdleStop(session) {
  clearQrIdleTimer(session);

  session.qrIdleTimer = setTimeout(() => {
    stopSocket(session, 'disconnected', 'QR expired because no user scanned it in time.').catch((error) => {
      session.lastError = error.message;
      logger.error(error, `Failed to stop idle WhatsApp QR socket for ${session.id}`);
    });
  }, qrIdleTimeoutMs);
}

async function clearAuthState(session) {
  await fs.rm(session.authDir, { recursive: true, force: true });
  session.lastQr = null;
  session.lastQrDataUrl = null;
  session.connectedNumber = null;
}

async function stopSocket(session, status = 'disconnected', message = null) {
  clearQrIdleTimer(session);

  const activeSock = session.sock;
  session.manualStop = true;
  session.reconnectAllowed = false;
  session.sock = null;
  session.connectionStatus = status;
  session.lastError = message;
  session.lastQr = null;
  session.lastQrDataUrl = null;
  session.connectedNumber = null;

  if (activeSock && typeof activeSock.end === 'function') {
    activeSock.end(new Error(message || status));
  }
}

async function startSocket(session) {
  if (session.starting) {
    return;
  }

  if (session.sock && ['connected', 'connecting', 'qr'].includes(session.connectionStatus)) {
    return;
  }

  session.starting = true;
  session.manualStop = false;
  session.reconnectAllowed = false;
  session.sessionHadQr = false;
  session.connectionStatus = 'connecting';
  session.lastError = null;

  try {
    const { state, saveCreds } = await useMultiFileAuthState(session.authDir);
    const { version } = await fetchLatestBaileysVersion();

    session.sock = makeWASocket({
      version,
      auth: state,
      logger,
      printQRInTerminal: false,
      browser: [`Cineworm (${session.id})`, 'Chrome', '1.0.0'],
      markOnlineOnConnect: false,
      syncFullHistory: false,
    });

    session.sock.ev.on('creds.update', saveCreds);

    session.sock.ev.on('connection.update', async (update) => {
      const { connection, lastDisconnect, qr } = update;

      if (qr) {
        session.sessionHadQr = true;
        session.lastQr = qr;
        session.lastQrDataUrl = await QRCode.toDataURL(qr, { margin: 1, width: 320 });
        session.connectionStatus = 'qr';
        scheduleQrIdleStop(session);
      }

      if (connection === 'open') {
        clearQrIdleTimer(session);
        session.connectionStatus = 'connected';
        session.reconnectAllowed = true;
        session.unopenedFailureCount = 0;
        session.connectedNumber = session.sock?.user?.id || null;
        session.lastQr = null;
        session.lastQrDataUrl = null;
        session.lastError = null;
      }

      if (connection === 'close') {
        clearQrIdleTimer(session);
        const statusCode = lastDisconnect?.error?.output?.statusCode;
        const errorMessage = lastDisconnect?.error?.message || null;
        const loggedOut = statusCode === DisconnectReason.loggedOut;
        const restartRequired = statusCode === DisconnectReason.restartRequired;
        const deviceRemoved = loggedOut && /device_removed|conflict/i.test(errorMessage || '');
        const shouldReconnect = !session.manualStop && !loggedOut && (session.reconnectAllowed || session.sessionHadQr || restartRequired);

        const failedBeforeOpen = !session.manualStop && !session.reconnectAllowed && !loggedOut;
        const staleFailure = failedBeforeOpen && !session.sessionHadQr && !restartRequired;
        session.unopenedFailureCount = staleFailure ? session.unopenedFailureCount + 1 : 0;

        if (deviceRemoved || (staleFailure && session.unopenedFailureCount >= 1)) {
          await clearAuthState(session);
        }

        session.connectionStatus = session.manualStop ? session.connectionStatus : (loggedOut || staleFailure ? 'logged_out' : 'disconnected');
        session.connectedNumber = null;
        session.lastQr = null;
        session.lastQrDataUrl = null;
        session.lastError = session.manualStop ? session.lastError : (
          staleFailure
            ? 'Previous WhatsApp session was stale. Auth was cleared; click Connect / QR to generate a new QR code.'
            : (
              deviceRemoved
                ? 'WhatsApp removed this linked device. Open WhatsApp Linked Devices, remove old Cineworm sessions, wait a few minutes, then connect again.'
                : (shouldReconnect ? 'WhatsApp pairing completed. Reconnecting session...' : errorMessage)
            )
        );
        session.sock = null;
        session.manualStop = false;
        session.sessionHadQr = false;

        if (shouldReconnect) {
          setTimeout(() => {
            startSocket(session).catch((error) => {
              session.lastError = error.message;
              logger.error(error, `Failed to reconnect WhatsApp socket for ${session.id}`);
            });
          }, 3000);
        }
      }
    });
  } catch (error) {
    session.connectionStatus = 'error';
    session.lastError = error.message;
    logger.error(error, `Failed to start WhatsApp socket for ${session.id}`);
  } finally {
    session.starting = false;
  }
}

app.get('/health', (req, res) => {
  res.json({
    ok: true,
    service: 'cineworm-whatsapp-server',
    activeSessions: Array.from(sessions.keys()),
  });
});

app.get('/status', requireApiKey, (req, res) => {
  const session = getOrCreateSession(getSessionId(req));
  res.json(serializeStatus(session));
});

app.post('/connect', requireApiKey, async (req, res) => {
  const session = getOrCreateSession(getSessionId(req));
  await startSocket(session);
  res.json(serializeStatus(session));
});

app.get('/qr', requireApiKey, (req, res) => {
  const session = getOrCreateSession(getSessionId(req));
  res.json({
    ok: true,
    sessionId: session.id,
    status: session.connectionStatus,
    qr: session.lastQr,
    qrDataUrl: session.lastQrDataUrl,
  });
});

app.post('/send', requireApiKey, async (req, res) => {
  const session = getOrCreateSession(getSessionId(req));
  const number = normalizeNumber(req.body.number);
  const message = String(req.body.message || '').trim();
  const validateNumber = req.body.validateNumber !== false;
  const typingPresence = req.body.typingPresence !== false;

  if (!number || number.length < 8) {
    return res.status(422).json({ ok: false, error: 'A valid phone number is required.' });
  }

  if (!message) {
    return res.status(422).json({ ok: false, error: 'Message is required.' });
  }

  if (!session.sock || session.connectionStatus !== 'connected') {
    return res.status(409).json({ ok: false, error: `WhatsApp session (${session.id}) is not connected.` });
  }

  const jid = `${number}@s.whatsapp.net`;

  try {
    if (validateNumber && typeof session.sock.onWhatsApp === 'function') {
      const matches = await session.sock.onWhatsApp(jid);
      const isRegistered = Array.isArray(matches) && matches.some((item) => item?.exists);

      if (!isRegistered) {
        return res.status(422).json({ ok: false, error: 'This number is not registered on WhatsApp.', jid });
      }
    }

    if (typingPresence) {
      try {
        await session.sock.presenceSubscribe(jid);
        await session.sock.sendPresenceUpdate('composing', jid);
        await sleep(randomInt(900, 2400));
        await session.sock.sendPresenceUpdate('paused', jid);
      } catch (presenceError) {
        logger.debug(presenceError, 'Unable to publish typing presence');
      }
    }

    const response = await session.sock.sendMessage(jid, {
      text: message,
      linkPreview: false,
    });

    return res.json({
      ok: true,
      sessionId: session.id,
      jid,
      messageId: response?.key?.id || null,
      response,
    });
  } catch (error) {
    logger.warn(error, `WhatsApp send failed for session ${session.id}`);
    return res.status(500).json({ ok: false, error: error.message || 'WhatsApp send failed.', jid });
  }
});

app.post('/logout', requireApiKey, async (req, res) => {
  const session = getOrCreateSession(getSessionId(req));
  if (session.sock) {
    try {
      await session.sock.logout();
    } catch (error) {
      logger.warn(error, `WhatsApp logout raised an error for session ${session.id}; clearing local auth state anyway`);
    }
  }

  await stopSocket(session, 'logged_out', null);
  await clearAuthState(session);

  res.json(serializeStatus(session));
});

app.listen(port, host, () => {
  logger.info(`WhatsApp multi-session server listening at http://${host}:${port}`);
});
