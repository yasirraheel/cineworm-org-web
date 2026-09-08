{{-- Cineworm Native Live Chat Widget (Exclusive to Paid Subscribers) --}}
@if(Auth::check() && Auth::user()->hasPaidSubscription())
<div id="cw-livechat-widget">
    {{-- Floating Launcher Button --}}
    <div id="cw-chat-launcher" title="Chat with Cineworm Support">
        <span id="cw-chat-unread-badge" style="display: none;">0</span>
        {{-- Chat Bubble Icon --}}
        <svg id="cw-icon-open" class="cw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        {{-- Close (X) Icon when open --}}
        <svg id="cw-icon-close" class="cw-icon" style="display: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </div>

    {{-- Chat Window Popover --}}
    <div id="cw-chat-box" style="display: none;">
        {{-- Header --}}
        <div id="cw-chat-header">
            <div class="cw-header-left">
                <div class="cw-avatar-container">
                    <div class="cw-avatar">CW</div>
                    <span class="cw-online-dot"></span>
                </div>
                <div class="cw-header-info">
                    <div class="cw-header-title">{{ getcong('site_name') ?: 'Cineworm' }} Support</div>
                    <div class="cw-header-subtitle" id="cw-header-status"><span class="cw-pulse"></span> Online &bull; Ready to help</div>
                </div>
            </div>
            <div class="cw-header-actions">
                {{-- Sound Toggle Button --}}
                <button type="button" id="cw-sound-toggle" class="cw-btn-icon" title="Toggle Sound Tone">
                    {{-- Volume On SVG --}}
                    <svg id="cw-sound-on-icon" class="cw-action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                    </svg>
                    {{-- Volume Mute SVG --}}
                    <svg id="cw-sound-mute-icon" class="cw-action-icon" style="display: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                        <line x1="23" y1="9" x2="17" y2="15"></line>
                        <line x1="17" y1="9" x2="23" y2="15"></line>
                    </svg>
                </button>
                {{-- Minimize Button --}}
                <button type="button" id="cw-minimize-btn" class="cw-btn-icon" title="Minimize Chat">
                    <svg class="cw-action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Subscriber Identity Strip --}}
        <div id="cw-guest-identity-bar">
            <span>Chatting as: <strong>{{ Auth::user()->name }}</strong> <span style="background: rgba(0, 210, 133, 0.15); color: #00d285; border: 1px solid rgba(0, 210, 133, 0.3); font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 10px; margin-left: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Paid Member</span></span>
        </div>

        {{-- Message Area --}}
        <div id="cw-chat-messages">
            <div class="cw-welcome-banner">
                <div class="cw-welcome-logo">&#127916;</div>
                <div class="cw-welcome-title">How can we help you today?</div>
                <div class="cw-welcome-text">Our team is here to help with any questions about movies, series, or your account.</div>
            </div>
            <div id="cw-messages-stream"></div>
            {{-- Typing Indicator --}}
            <div id="cw-typing-indicator" style="display: none;">
                <div class="cw-typing-bubble">
                    <span class="cw-typing-dot"></span>
                    <span class="cw-typing-dot"></span>
                    <span class="cw-typing-dot"></span>
                </div>
                <span class="cw-typing-text">Support is typing...</span>
            </div>
        </div>

        {{-- Input Footer Area --}}
        <div id="cw-chat-footer">
            <form id="cw-chat-form" autocomplete="off">
                <textarea id="cw-chat-input" placeholder="Type your message here..." rows="1" maxlength="3000"></textarea>
                <button type="submit" id="cw-send-btn" title="Send message">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                    </svg>
                </button>
            </form>
            <div class="cw-footer-branding">Cineworm Live Support</div>
        </div>
    </div>
</div>

<style>
/* ─── LIVE CHAT WIDGET STYLES ───────────────────────────────────────── */
#cw-livechat-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 999999;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #f1f5f9;
}

/* Launcher Button */
#cw-chat-launcher {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff4d00 0%, #d83b01 100%);
    box-shadow: 0 8px 24px rgba(255, 77, 0, 0.45), 0 2px 6px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.25s ease;
    position: relative;
    user-select: none;
}
#cw-chat-launcher:hover {
    transform: scale(1.08);
    box-shadow: 0 10px 28px rgba(255, 77, 0, 0.6), 0 4px 10px rgba(0, 0, 0, 0.4);
}
#cw-chat-launcher .cw-icon {
    width: 28px;
    height: 28px;
    stroke: #ffffff;
}

/* Unread Badge */
#cw-chat-unread-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    min-width: 22px;
    height: 22px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.4);
    border: 2px solid #0f172a;
    animation: cw-bounce 0.4s ease infinite alternate;
}
@keyframes cw-bounce {
    from { transform: translateY(0); }
    to { transform: translateY(-3px); }
}

/* Chat Window Box */
#cw-chat-box {
    position: absolute;
    bottom: 75px;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 32px);
    height: 540px;
    max-height: calc(100vh - 110px);
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 18px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), 0 4px 16px rgba(0, 0, 0, 0.4);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
    animation: cw-popin 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes cw-popin {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

/* Header */
#cw-chat-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.cw-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.cw-avatar-container {
    position: relative;
}
.cw-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff4d00 0%, #ff7300 100%);
    color: #ffffff;
    font-weight: 800;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(255, 77, 0, 0.4);
}
.cw-online-dot {
    position: absolute;
    bottom: 1px;
    right: 1px;
    width: 10px;
    height: 10px;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid #0f172a;
}
.cw-header-info {
    display: flex;
    flex-direction: column;
}
.cw-header-title {
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}
.cw-header-subtitle {
    font-size: 12px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 3px;
}
.cw-pulse {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
    box-shadow: 0 0 8px #10b981;
}

/* Actions in Header */
.cw-header-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.cw-btn-icon {
    background: rgba(255, 255, 255, 0.06);
    border: none;
    border-radius: 8px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #94a3b8;
    transition: all 0.15s ease;
    padding: 0;
}
.cw-btn-icon:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
}
.cw-action-icon {
    width: 18px;
    height: 18px;
}

/* Guest Identity Bar */
#cw-guest-identity-bar {
    background: #1e293b;
    padding: 6px 14px;
    font-size: 11px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
#cw-guest-identity-bar strong {
    color: #e2e8f0;
}
#cw-guest-identity-bar a {
    color: #ff4d00;
    text-decoration: underline;
    font-weight: 600;
    cursor: pointer;
}

/* Chat Messages Container */
#cw-chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: radial-gradient(circle at 50% 10%, #172033 0%, #0f172a 100%);
    scroll-behavior: smooth;
}
#cw-chat-messages::-webkit-scrollbar {
    width: 5px;
}
#cw-chat-messages::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 3px;
}

/* Welcome Card */
.cw-welcome-banner {
    text-align: center;
    background: rgba(255, 255, 255, 0.03);
    border: 1px dashed rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 16px 12px;
    margin-bottom: 8px;
}
.cw-welcome-logo {
    font-size: 26px;
    margin-bottom: 6px;
}
.cw-welcome-title {
    font-size: 14px;
    font-weight: 700;
    color: #f8fafc;
    margin-bottom: 4px;
}
.cw-welcome-text {
    font-size: 12px;
    color: #94a3b8;
    line-height: 1.4;
}

/* Message Stream & Bubbles */
#cw-messages-stream {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cw-msg-row {
    display: flex;
    flex-direction: column;
    max-width: 82%;
    animation: cw-fadein 0.2s ease;
}
@keyframes cw-fadein {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.cw-msg-row.cw-msg-incoming {
    align-self: flex-start;
}
.cw-msg-row.cw-msg-outgoing {
    align-self: flex-end;
}

.cw-msg-sender-name {
    font-size: 10px;
    color: #64748b;
    margin-bottom: 3px;
    padding: 0 4px;
}
.cw-msg-outgoing .cw-msg-sender-name {
    text-align: right;
}

.cw-msg-bubble {
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.45;
    word-break: break-word;
    position: relative;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}

.cw-msg-incoming .cw-msg-bubble {
    background: #1e293b;
    color: #f1f5f9;
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.cw-msg-outgoing .cw-msg-bubble {
    background: linear-gradient(135deg, #ff4d00 0%, #e03e00 100%);
    color: #ffffff;
    border-bottom-right-radius: 4px;
}

/* Meta time and read receipt ticks */
.cw-msg-meta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
    margin-top: 4px;
    font-size: 10px;
}
.cw-msg-incoming .cw-msg-meta {
    justify-content: flex-start;
    color: #64748b;
}
.cw-msg-outgoing .cw-msg-meta {
    color: rgba(255, 255, 255, 0.7);
}

/* Read receipt ticks */
.cw-tick {
    font-size: 11px;
    font-weight: bold;
    letter-spacing: -2px;
    margin-left: 2px;
    color: rgba(255, 255, 255, 0.6);
    user-select: none;
}
.cw-tick.cw-tick-read {
    color: #38bdf8 !important; /* Double Blue Checkmark */
}

/* Typing Indicator */
#cw-typing-indicator {
    align-self: flex-start;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: cw-fadein 0.2s ease;
    margin-top: 2px;
}
.cw-typing-bubble {
    background: #1e293b;
    padding: 8px 12px;
    border-radius: 14px;
    border-bottom-left-radius: 4px;
    display: flex;
    gap: 4px;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.cw-typing-text {
    font-size: 11px;
    color: #94a3b8;
    font-style: italic;
}
.cw-typing-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #38bdf8;
    animation: cw-typing 1.2s infinite ease-in-out;
}
.cw-typing-dot:nth-child(2) { animation-delay: 0.2s; }
.cw-typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes cw-typing {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

/* Footer / Input */
#cw-chat-footer {
    padding: 10px 14px 12px;
    background: #0f172a;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}
#cw-chat-form {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 6px 8px 6px 12px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
#cw-chat-form:focus-within {
    border-color: #ff4d00;
    box-shadow: 0 0 0 2px rgba(255, 77, 0, 0.25);
}
#cw-chat-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: #ffffff;
    font-size: 13px;
    resize: none;
    max-height: 90px;
    line-height: 1.4;
    padding: 4px 0;
    font-family: inherit;
}
#cw-chat-input::placeholder {
    color: #64748b;
}
#cw-send-btn {
    background: #ff4d00;
    border: none;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.15s ease, transform 0.1s ease;
    padding: 0;
}
#cw-send-btn:hover {
    background: #e03e00;
    transform: scale(1.04);
}
#cw-send-btn svg {
    width: 17px;
    height: 17px;
}
.cw-footer-branding {
    font-size: 10px;
    color: #475569;
    text-align: center;
    margin-top: 8px;
    letter-spacing: 0.3px;
    font-weight: 500;
}

/* Mobile responsive */
@media (max-width: 480px) {
    #cw-livechat-widget {
        bottom: 16px;
        right: 16px;
    }
    #cw-chat-box {
        position: fixed;
        top: 10px;
        bottom: 10px;
        left: 10px;
        right: 10px;
        width: auto;
        max-width: none;
        height: auto;
        max-height: none;
        border-radius: 16px;
    }
}
</style>

<script>
(function() {
    'use strict';

    // ── STATE & STORAGE ──────────────────────────────────────────────
    var CW_CHAT = {
        isOpen: false,
        lastId: 0,
        lastReadUserMsgId: 0,
        pollTimer: null,
        typingPingTimer: null,
        soundEnabled: localStorage.getItem('cw_chat_sound') !== 'off',
        guestToken: '',
        guestName: @json(Auth::user()->name),
        unreadCount: 0,
        audioCtx: null,
        isAuth: true
    };

    // DOM Elements
    var launcher = document.getElementById('cw-chat-launcher');
    var chatBox = document.getElementById('cw-chat-box');
    var iconOpen = document.getElementById('cw-icon-open');
    var iconClose = document.getElementById('cw-icon-close');
    var unreadBadge = document.getElementById('cw-chat-unread-badge');
    var messagesStream = document.getElementById('cw-messages-stream');
    var messagesContainer = document.getElementById('cw-chat-messages');
    var chatForm = document.getElementById('cw-chat-form');
    var chatInput = document.getElementById('cw-chat-input');
    var soundToggle = document.getElementById('cw-sound-toggle');
    var soundOnIcon = document.getElementById('cw-sound-on-icon');
    var soundMuteIcon = document.getElementById('cw-sound-mute-icon');
    var minimizeBtn = document.getElementById('cw-minimize-btn');
    var guestDisplayName = document.getElementById('cw-guest-display-name');
    var editGuestNameBtn = document.getElementById('cw-edit-guest-name');
    var typingIndicator = document.getElementById('cw-typing-indicator');

    if (guestDisplayName) {
        guestDisplayName.textContent = CW_CHAT.guestName;
    }

    // ── SOUND SYNTHESIS (WEB AUDIO API) ──────────────────────────────
    function playIncomingTone() {
        if (!CW_CHAT.soundEnabled) return;
        try {
            var AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            if (!CW_CHAT.audioCtx) {
                CW_CHAT.audioCtx = new AudioContext();
            }
            if (CW_CHAT.audioCtx.state === 'suspended') {
                CW_CHAT.audioCtx.resume();
            }

            var ctx = CW_CHAT.audioCtx;
            var now = ctx.currentTime;

            // First chime tone (D5 - 587.33 Hz)
            var osc1 = ctx.createOscillator();
            var gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            gain1.gain.setValueAtTime(0.18, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.22);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.22);

            // Second chime tone (A5 - 880 Hz)
            var osc2 = ctx.createOscillator();
            var gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880, now + 0.11);
            gain2.gain.setValueAtTime(0.22, now + 0.11);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.42);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.11);
            osc2.stop(now + 0.42);
        } catch (e) {
            console.warn('[LiveChat Sound]', e);
        }
    }

    function updateSoundUI() {
        if (CW_CHAT.soundEnabled) {
            soundOnIcon.style.display = 'block';
            soundMuteIcon.style.display = 'none';
            soundToggle.setAttribute('title', 'Sound tone is ON (Click to mute)');
        } else {
            soundOnIcon.style.display = 'none';
            soundMuteIcon.style.display = 'block';
            soundToggle.setAttribute('title', 'Sound tone is OFF (Click to unmute)');
        }
    }
    updateSoundUI();

    soundToggle.addEventListener('click', function(e) {
        e.preventDefault();
        CW_CHAT.soundEnabled = !CW_CHAT.soundEnabled;
        localStorage.setItem('cw_chat_sound', CW_CHAT.soundEnabled ? 'on' : 'off');
        updateSoundUI();
        if (CW_CHAT.soundEnabled) playIncomingTone();
    });

    // ── OPEN / CLOSE / TOGGLE ────────────────────────────────────────
    function openChat() {
        CW_CHAT.isOpen = true;
        chatBox.style.display = 'flex';
        iconOpen.style.display = 'none';
        iconClose.style.display = 'block';
        unreadBadge.style.display = 'none';
        CW_CHAT.unreadCount = 0;
        fetchMessages(true);
        setTimeout(function() {
            chatInput.focus();
            scrollToBottom();
        }, 150);
        restartPolling();
    }

    function closeChat() {
        CW_CHAT.isOpen = false;
        chatBox.style.display = 'none';
        iconOpen.style.display = 'block';
        iconClose.style.display = 'none';
        restartPolling();
    }

    launcher.addEventListener('click', function() {
        if (CW_CHAT.isOpen) {
            closeChat();
        } else {
            openChat();
        }
    });

    minimizeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeChat();
    });

    if (editGuestNameBtn) {
        editGuestNameBtn.addEventListener('click', function() {
            var currentName = localStorage.getItem('cw_chat_guest_name') || 'Guest';
            var newName = prompt('Enter your name for support chat:', currentName);
            if (newName && newName.trim() !== '') {
                CW_CHAT.guestName = newName.trim().substring(0, 40);
                localStorage.setItem('cw_chat_guest_name', CW_CHAT.guestName);
                if (guestDisplayName) guestDisplayName.textContent = CW_CHAT.guestName;
            }
        });
    }

    // Auto-expand textarea & submit on Enter
    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });

    // ── REAL-TIME TYPING PING FROM USER ──────────────────────────────
    chatInput.addEventListener('input', function() {
        if (!CW_CHAT.typingPingTimer) {
            sendTypingPing();
            CW_CHAT.typingPingTimer = setTimeout(function() {
                CW_CHAT.typingPingTimer = null;
            }, 2500);
        }
    });

    function sendTypingPing() {
        var csrfToken = document.querySelector('meta[name="csrf-token"]') ?
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        fetch('/livechat/typing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ guest_token: CW_CHAT.guestToken })
        }).catch(function() {});
    }

    // ── MESSAGE RENDERING & READ RECEIPT TICKS ────────────────────────
    function appendMessage(msg, isInitial) {
        if (document.getElementById('cw-msg-' + msg.id)) return;

        var row = document.createElement('div');
        row.id = 'cw-msg-' + msg.id;
        row.dataset.id = msg.id;
        var isOutgoing = (msg.sender !== 'admin');
        row.className = 'cw-msg-row ' + (isOutgoing ? 'cw-msg-outgoing' : 'cw-msg-incoming');

        var senderName = document.createElement('div');
        senderName.className = 'cw-msg-sender-name';
        senderName.textContent = msg.sender_name || (isOutgoing ? 'You' : 'Support');

        var bubble = document.createElement('div');
        bubble.className = 'cw-msg-bubble';
        bubble.innerHTML = msg.message.replace(/\n/g, '<br>');

        var meta = document.createElement('div');
        meta.className = 'cw-msg-meta';

        var timeSpan = document.createElement('span');
        timeSpan.textContent = msg.time || '';
        meta.appendChild(timeSpan);

        // Read receipt tick for outgoing user messages
        if (isOutgoing) {
            var tickSpan = document.createElement('span');
            tickSpan.className = 'cw-tick';
            var isRead = msg.is_read || (CW_CHAT.lastReadUserMsgId && msg.id <= CW_CHAT.lastReadUserMsgId);
            if (isRead) {
                tickSpan.classList.add('cw-tick-read');
                tickSpan.innerHTML = '&#10003;&#10003;'; // Double blue tick
                tickSpan.title = 'Read by Support';
            } else {
                tickSpan.innerHTML = '&#10003;'; // Single tick
                tickSpan.title = 'Delivered';
            }
            meta.appendChild(tickSpan);
        }

        bubble.appendChild(meta);
        row.appendChild(senderName);
        row.appendChild(bubble);
        messagesStream.appendChild(row);

        if (msg.id > CW_CHAT.lastId) {
            CW_CHAT.lastId = msg.id;
        }

        if (!isInitial && !isOutgoing) {
            playIncomingTone();
        }
    }

    // Update existing message ticks when admin reads messages
    function updateReadReceipts(lastReadId) {
        if (!lastReadId) return;
        CW_CHAT.lastReadUserMsgId = Math.max(CW_CHAT.lastReadUserMsgId, lastReadId);

        var outgoingRows = messagesStream.querySelectorAll('.cw-msg-outgoing');
        outgoingRows.forEach(function(row) {
            var msgId = parseInt(row.dataset.id, 10);
            if (msgId <= CW_CHAT.lastReadUserMsgId) {
                var tick = row.querySelector('.cw-tick');
                if (tick && !tick.classList.contains('cw-tick-read')) {
                    tick.classList.add('cw-tick-read');
                    tick.innerHTML = '&#10003;&#10003;'; // Double blue tick
                    tick.title = 'Read by Support';
                }
            }
        });
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // ── DATA FETCHING ────────────────────────────────────────────────
    function fetchMessages(markRead) {
        var url = '/livechat/messages?last_id=' + CW_CHAT.lastId +
                  '&guest_token=' + encodeURIComponent(CW_CHAT.guestToken) +
                  (markRead ? '&mark_read=1' : '');

        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                if (data.last_read_user_msg_id) {
                    updateReadReceipts(data.last_read_user_msg_id);
                }

                // Handle Admin Typing Indicator
                if (data.is_admin_typing) {
                    typingIndicator.style.display = 'flex';
                } else {
                    typingIndicator.style.display = 'none';
                }

                if (data.messages && data.messages.length > 0) {
                    var wasAtBottom = (messagesContainer.scrollHeight - messagesContainer.clientHeight <= messagesContainer.scrollTop + 60);
                    var isInitial = (CW_CHAT.lastId === 0);

                    data.messages.forEach(function(msg) {
                        appendMessage(msg, isInitial);
                    });

                    if (wasAtBottom || isInitial || CW_CHAT.isOpen) {
                        scrollToBottom();
                    }
                }

                // Update unread badge when chat is closed
                if (!CW_CHAT.isOpen && typeof data.unread_count === 'number') {
                    if (data.unread_count > 0) {
                        unreadBadge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                        unreadBadge.style.display = 'flex';
                    } else {
                        unreadBadge.style.display = 'none';
                    }
                }
            }
        })
        .catch(function(err) {
            console.error('[LiveChat Poll Error]', err);
        });
    }

    // ── SEND MESSAGE ─────────────────────────────────────────────────
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var text = chatInput.value.trim();
        if (!text) return;

        chatInput.value = '';
        chatInput.style.height = 'auto';

        var csrfToken = document.querySelector('meta[name="csrf-token"]') ?
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        var payload = {
            message: text,
            guest_token: CW_CHAT.guestToken,
            guest_name: CW_CHAT.guestName
        };

        fetch('/livechat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success && data.message) {
                appendMessage(data.message, false);
                scrollToBottom();
            }
        })
        .catch(function(err) {
            console.error('[LiveChat Send Error]', err);
            alert('Could not send message. Please try again.');
        });
    });

    // ── POLLING INTERVAL ─────────────────────────────────────────────
    function restartPolling() {
        if (CW_CHAT.pollTimer) {
            clearInterval(CW_CHAT.pollTimer);
        }
        var interval = CW_CHAT.isOpen ? 2500 : 7000;
        CW_CHAT.pollTimer = setInterval(function() {
            fetchMessages(CW_CHAT.isOpen);
        }, interval);
    }

    // Initial fetch on page load
    fetchMessages(false);
    restartPolling();

    // Check if ?open_chat=1 in URL
    if (window.location.search.indexOf('open_chat=1') !== -1) {
        setTimeout(openChat, 400);
    }
})();
</script>
@endif
