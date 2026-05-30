import 'dotenv/config';
import express from 'express';
import fs from 'fs/promises';
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
const authDir = process.env.WA_AUTH_DIR || 'auth';

let sock = null;
let lastQr = null;
let lastQrDataUrl = null;
let connectionStatus = 'disconnected';
let connectedNumber = null;
let lastError = null;
let starting = false;

app.use(express.json({ limit: '1mb' }));

function requireApiKey(req, res, next) {
  if (req.header('x-api-key') !== apiKey) {
    return res.status(401).json({ ok: false, error: 'Unauthorized' });
  }

  return next();
}

function serializeStatus() {
  return {
    ok: true,
    status: connectionStatus,
    connected: connectionStatus === 'connected',
    connectedNumber,
    hasQr: Boolean(lastQrDataUrl),
    qrDataUrl: lastQrDataUrl,
    lastError,
  };
}

function normalizeNumber(value) {
  return String(value || '').replace(/[^\d]/g, '');
}

async function clearAuthState() {
  await fs.rm(authDir, { recursive: true, force: true });
  lastQr = null;
  lastQrDataUrl = null;
  connectedNumber = null;
}

async function startSocket() {
  if (starting) {
    return;
  }

  if (sock && ['connected', 'connecting', 'qr'].includes(connectionStatus)) {
    return;
  }

  starting = true;
  connectionStatus = 'connecting';
  lastError = null;

  try {
    const { state, saveCreds } = await useMultiFileAuthState(authDir);
    const { version } = await fetchLatestBaileysVersion();

    sock = makeWASocket({
      version,
      auth: state,
      logger,
      printQRInTerminal: false,
      browser: ['Cineworm Admin', 'Chrome', '1.0.0'],
      markOnlineOnConnect: false,
      syncFullHistory: false,
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
      const { connection, lastDisconnect, qr } = update;

      if (qr) {
        lastQr = qr;
        lastQrDataUrl = await QRCode.toDataURL(qr, { margin: 1, width: 320 });
        connectionStatus = 'qr';
      }

      if (connection === 'open') {
        connectionStatus = 'connected';
        connectedNumber = sock?.user?.id || null;
        lastQr = null;
        lastQrDataUrl = null;
        lastError = null;
      }

      if (connection === 'close') {
        const statusCode = lastDisconnect?.error?.output?.statusCode;
        const loggedOut = statusCode === DisconnectReason.loggedOut;

        connectionStatus = loggedOut ? 'logged_out' : 'disconnected';
        connectedNumber = null;
        lastQr = null;
        lastQrDataUrl = null;
        lastError = lastDisconnect?.error?.message || null;
        sock = null;

        if (!loggedOut) {
          setTimeout(() => {
            startSocket().catch((error) => {
              lastError = error.message;
              logger.error(error, 'Failed to reconnect WhatsApp socket');
            });
          }, 3000);
        }
      }
    });
  } catch (error) {
    connectionStatus = 'error';
    lastError = error.message;
    logger.error(error, 'Failed to start WhatsApp socket');
  } finally {
    starting = false;
  }
}

app.get('/health', (req, res) => {
  res.json({ ok: true, service: 'cineworm-whatsapp-server' });
});

app.get('/status', requireApiKey, (req, res) => {
  res.json(serializeStatus());
});

app.post('/connect', requireApiKey, async (req, res) => {
  await startSocket();
  res.json(serializeStatus());
});

app.get('/qr', requireApiKey, (req, res) => {
  res.json({
    ok: true,
    status: connectionStatus,
    qr: lastQr,
    qrDataUrl: lastQrDataUrl,
  });
});

app.post('/send', requireApiKey, async (req, res) => {
  const number = normalizeNumber(req.body.number);
  const message = String(req.body.message || '').trim();

  if (!number || number.length < 8) {
    return res.status(422).json({ ok: false, error: 'A valid phone number is required.' });
  }

  if (!message) {
    return res.status(422).json({ ok: false, error: 'Message is required.' });
  }

  if (!sock || connectionStatus !== 'connected') {
    return res.status(409).json({ ok: false, error: 'WhatsApp is not connected.' });
  }

  const jid = `${number}@s.whatsapp.net`;
  const response = await sock.sendMessage(jid, { text: message });

  return res.json({ ok: true, jid, response });
});

app.post('/logout', requireApiKey, async (req, res) => {
  if (sock) {
    try {
      await sock.logout();
    } catch (error) {
      logger.warn(error, 'WhatsApp logout raised an error; clearing local auth state anyway');
    }
  }

  sock = null;
  connectionStatus = 'connecting';
  await clearAuthState();
  await startSocket();

  res.json(serializeStatus());
});

app.listen(port, host, () => {
  logger.info(`WhatsApp server listening at http://${host}:${port}`);
});
