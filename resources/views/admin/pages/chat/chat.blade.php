@extends('admin.admin_app')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            {{-- Page Header matching Cineworm admin style --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0">Live Chat &amp; Support Inbox</h4>
                        <div class="page-title-right">
                            <span class="badge badge-success px-3 py-2" id="adm-status-indicator" style="font-size: 12px;">
                                <i class="fa fa-circle text-white mr-1"></i> Live Real-Time Polling
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Dark Card Box --}}
            <div class="row">
                <div class="col-12">
                    <div class="card-box p-0 cw-dark-chat-card">
                        <div class="cw-chat-wrapper">
                            
                            {{-- LEFT SIDEBAR: Conversation List --}}
                            <div class="cw-sidebar">
                                <div class="cw-sidebar-header">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="m-0 text-white font-weight-bold">
                                            Conversations
                                            <span class="badge badge-danger ml-1" id="adm-total-unread-badge" style="display:none;">0</span>
                                        </h5>
                                        {{-- Sound Tone Toggle --}}
                                        <button type="button" id="adm-sound-toggle" class="btn btn-xs cw-btn-sound" title="Toggle Sound Tone">
                                            <span id="adm-sound-icon"><i class="fa fa-volume-up"></i> Sound ON</span>
                                        </button>
                                    </div>
                                    {{-- Search Box --}}
                                    <div class="cw-search-wrap">
                                        <input type="text" id="adm-thread-search" class="form-control form-control-sm cw-dark-input" placeholder="Search by name, email or message...">
                                    </div>
                                    {{-- Filter Pills --}}
                                    <div class="cw-filter-pills mt-2">
                                        <button type="button" class="cw-pill-btn active" data-filter="all">All</button>
                                        <button type="button" class="cw-pill-btn" data-filter="unread">Unread</button>
                                        <button type="button" class="cw-pill-btn" data-filter="user">Users</button>
                                        <button type="button" class="cw-pill-btn" data-filter="guest">Guests</button>
                                    </div>
                                </div>

                                {{-- Conversation Items List --}}
                                <div class="cw-sidebar-thread-list" id="adm-thread-list">
                                    <div class="text-center text-muted p-4" id="adm-threads-loading">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="mt-2 font-12 text-muted">Loading conversations...</div>
                                    </div>
                                </div>
                            </div>

                            {{-- CENTER: Active Chat Area --}}
                            <div class="cw-main-panel" id="adm-chat-main">
                                
                                {{-- Placeholder when no thread is selected --}}
                                <div class="cw-empty-state" id="adm-empty-state">
                                    <div class="cw-empty-icon"><i class="fa fa-comments-o"></i></div>
                                    <h4 class="text-white">Select a conversation</h4>
                                    <p class="text-muted">Choose a visitor or registered user from the left inbox to view messages and reply in real time.</p>
                                </div>

                                {{-- Active Chat View --}}
                                <div class="cw-active-state" id="adm-active-state" style="display: none;">
                                    {{-- Thread Header --}}
                                    <div class="cw-thread-header">
                                        <div class="d-flex align-items-center">
                                            <div class="cw-thread-avatar mr-3" id="adm-header-avatar">U</div>
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <h5 class="m-0 text-white font-weight-bold" id="adm-header-name">Customer Name</h5>
                                                    <span id="adm-user-typing-badge" class="badge badge-success ml-2 font-11" style="display: none;">
                                                        <i class="fa fa-pencil"></i> typing...
                                                    </span>
                                                </div>
                                                <div class="font-12 text-muted" id="adm-header-subinfo">User &bull; user@example.com</div>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="adm-delete-thread-btn" title="Delete Conversation History">
                                                <i class="fa fa-trash"></i> Delete Chat
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Message Stream --}}
                                    <div class="cw-messages-body" id="adm-messages-body">
                                        <div id="adm-messages-stream"></div>
                                        {{-- User Typing Bubble --}}
                                        <div id="adm-user-typing-indicator" style="display: none;" class="mt-2">
                                            <div class="cw-typing-bubble">
                                                <span class="cw-typing-dot"></span>
                                                <span class="cw-typing-dot"></span>
                                                <span class="cw-typing-dot"></span>
                                            </div>
                                            <span class="cw-typing-label font-11 text-muted ml-2">Customer is typing...</span>
                                        </div>
                                    </div>

                                    {{-- Reply Input Bar --}}
                                    <div class="cw-reply-bar">
                                        <form id="adm-reply-form">
                                            <div class="input-group">
                                                <textarea id="adm-reply-input" class="form-control cw-dark-input" placeholder="Write your reply... (Press Enter to send, Shift+Enter for new line)" rows="2"></textarea>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary px-4 font-weight-bold cw-send-btn" id="adm-send-btn">
                                                        <i class="fa fa-paper-plane mr-1"></i> Send
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2 font-11 text-muted">
                                                <span><i class="fa fa-info-circle text-primary"></i> Replies include read receipts (ticks) and trigger audio chimes on customer side.</span>
                                                <span><kbd style="background:#333; color:#fff;">Enter</kbd> to send</span>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>

                            {{-- RIGHT SIDEBAR: Customer Profile Details --}}
                            <div class="cw-customer-sidebar" id="adm-customer-sidebar" style="display: none;">
                                <div class="cw-customer-header">
                                    <h6 class="m-0 text-white font-weight-bold">Visitor Details</h6>
                                </div>
                                <div class="cw-customer-body p-3">
                                    <div class="text-center mb-3">
                                        <div class="cw-customer-big-avatar mx-auto mb-2" id="adm-cust-big-avatar">U</div>
                                        <h6 class="font-weight-bold text-white mb-1" id="adm-cust-name">User Name</h6>
                                        <span class="badge badge-primary font-11" id="adm-cust-badge">Registered User</span>
                                    </div>

                                    <div class="cw-divider my-3"></div>

                                    <div class="detail-row mb-3">
                                        <label class="font-11 text-muted text-uppercase d-block mb-1">Email</label>
                                        <span class="font-13 text-light font-weight-500" id="adm-cust-email">N/A</span>
                                    </div>

                                    <div class="detail-row mb-3" id="adm-cust-phone-row">
                                        <label class="font-11 text-muted text-uppercase d-block mb-1">Phone</label>
                                        <span class="font-13 text-light font-weight-500" id="adm-cust-phone">N/A</span>
                                    </div>

                                    <div class="detail-row mb-3" id="adm-cust-ip-row">
                                        <label class="font-11 text-muted text-uppercase d-block mb-1">IP Address</label>
                                        <span class="font-13 text-light font-weight-500" id="adm-cust-ip">N/A</span>
                                    </div>

                                    <div class="detail-row mb-3">
                                        <label class="font-11 text-muted text-uppercase d-block mb-1">Joined / First Seen</label>
                                        <span class="font-13 text-light font-weight-500" id="adm-cust-joined">N/A</span>
                                    </div>

                                    <div class="detail-row mb-3" id="adm-cust-plan-row">
                                        <label class="font-11 text-muted text-uppercase d-block mb-1">Subscription Plan</label>
                                        <span class="font-13 text-light font-weight-500" id="adm-cust-plan">Free</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('admin.copyright')
</div>

<style>
/* ─── CINEWORM ADMIN DARK THEME STYLES FOR LIVE CHAT ───────────────── */
.cw-dark-chat-card {
    background-color: #1c1c1e !important;
    border: 1px solid #2a2a2d !important;
    border-radius: 6px !important;
    overflow: hidden;
    margin-bottom: 30px;
}

.cw-chat-wrapper {
    display: flex;
    height: 720px;
    background: #1c1c1e;
}

/* Left Sidebar */
.cw-sidebar {
    width: 320px;
    border-right: 1px solid #2a2a2d;
    display: flex;
    flex-direction: column;
    background: #151517;
}
.cw-sidebar-header {
    padding: 14px 16px;
    background: #1c1c1e;
    border-bottom: 1px solid #2a2a2d;
}
.cw-btn-sound {
    background: #2a2a2e;
    color: #98a6ad;
    border: 1px solid #38383e;
    border-radius: 4px;
    padding: 3px 8px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s;
}
.cw-btn-sound:hover {
    color: #ffffff;
    background: #333338;
}

.cw-dark-input {
    background-color: #2a2a2e !important;
    border: 1px solid #38383e !important;
    color: #f9f9f9 !important;
    border-radius: 4px;
}
.cw-dark-input:focus {
    border-color: #ff4d00 !important;
    box-shadow: 0 0 0 2px rgba(255, 77, 0, 0.25) !important;
}

.cw-filter-pills {
    display: flex;
    gap: 4px;
}
.cw-pill-btn {
    border: 1px solid #323238;
    background: #222226;
    color: #98a6ad;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.15s;
}
.cw-pill-btn:hover {
    background: #2d2d33;
    color: #ffffff;
}
.cw-pill-btn.active {
    background: #ff4d00;
    border-color: #ff4d00;
    color: #ffffff;
}

.cw-sidebar-thread-list {
    flex: 1;
    overflow-y: auto;
}
.cw-sidebar-thread-list::-webkit-scrollbar {
    width: 5px;
}
.cw-sidebar-thread-list::-webkit-scrollbar-thumb {
    background: #333338;
    border-radius: 3px;
}

.cw-thread-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #222226;
    cursor: pointer;
    transition: background 0.15s;
    position: relative;
}
.cw-thread-item:hover {
    background: #202025;
}
.cw-thread-item.active {
    background: #27272e;
    border-left: 4px solid #ff4d00;
}
.cw-item-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #333338;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-right: 12px;
}
.cw-item-avatar.guest {
    background: linear-gradient(135deg, #475569 0%, #334155 100%);
}
.cw-item-avatar.user {
    background: linear-gradient(135deg, #ff4d00 0%, #d83b01 100%);
}
.cw-item-info {
    flex: 1;
    min-width: 0;
}
.cw-item-title {
    font-size: 13px;
    font-weight: 700;
    color: #f9f9f9;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cw-item-time {
    font-size: 10px;
    color: #98a6ad;
    font-weight: normal;
}
.cw-item-snippet {
    font-size: 12px;
    color: #98a6ad;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cw-unread-pill {
    background: #ff4d00;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    margin-left: 6px;
}

/* Center Chat Area */
.cw-main-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #18181b;
}
.cw-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
    background: #141416;
}
.cw-empty-icon {
    font-size: 52px;
    margin-bottom: 15px;
    color: #383840;
}

.cw-active-state {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.cw-thread-header {
    padding: 14px 20px;
    background: #1c1c1e;
    border-bottom: 1px solid #2a2a2d;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cw-thread-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #ff4d00;
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Message Stream */
.cw-messages-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #141416;
    display: flex;
    flex-direction: column;
}
.cw-messages-body::-webkit-scrollbar {
    width: 6px;
}
.cw-messages-body::-webkit-scrollbar-thumb {
    background: #2a2a30;
    border-radius: 3px;
}
#adm-messages-stream {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cw-msg-bubble-row {
    display: flex;
    flex-direction: column;
    max-width: 75%;
}
.cw-msg-bubble-row.incoming {
    align-self: flex-start;
}
.cw-msg-bubble-row.outgoing {
    align-self: flex-end;
}
.cw-msg-bubble-sender {
    font-size: 11px;
    color: #98a6ad;
    margin-bottom: 4px;
    font-weight: 600;
}
.cw-msg-bubble-row.outgoing .cw-msg-bubble-sender {
    text-align: right;
    color: #ff7300;
}
.cw-msg-bubble-box {
    padding: 10px 15px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
}
.cw-msg-bubble-row.incoming .cw-msg-bubble-box {
    background: #25252a;
    color: #f2f2f2;
    border: 1px solid #33333a;
    border-bottom-left-radius: 3px;
}
.cw-msg-bubble-row.outgoing .cw-msg-bubble-box {
    background: linear-gradient(135deg, #ff4d00 0%, #d83b01 100%);
    color: #ffffff;
    border-bottom-right-radius: 3px;
}
.cw-msg-bubble-meta {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    font-size: 10px;
}
.cw-msg-bubble-row.incoming .cw-msg-bubble-meta {
    justify-content: flex-start;
    color: #787882;
}
.cw-msg-bubble-row.outgoing .cw-msg-bubble-meta {
    justify-content: flex-end;
    color: rgba(255, 255, 255, 0.75);
}

/* Read receipt ticks in Admin */
.cw-adm-tick {
    font-size: 11px;
    font-weight: bold;
    letter-spacing: -2px;
    margin-left: 2px;
    color: rgba(255, 255, 255, 0.6);
    user-select: none;
}
.cw-adm-tick.cw-tick-read {
    color: #38bdf8 !important; /* Double Blue Checkmark */
}

/* Typing Indicator in Admin */
#adm-user-typing-indicator {
    display: flex;
    align-items: center;
}
.cw-typing-bubble {
    background: #25252a;
    padding: 6px 10px;
    border-radius: 12px;
    display: inline-flex;
    gap: 3px;
    align-items: center;
    border: 1px solid #33333a;
}
.cw-typing-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #81c868;
    animation: cw-typing 1.2s infinite ease-in-out;
}
.cw-typing-dot:nth-child(2) { animation-delay: 0.2s; }
.cw-typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes cw-typing {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

/* Reply Bar */
.cw-reply-bar {
    padding: 14px 20px;
    background: #1c1c1e;
    border-top: 1px solid #2a2a2d;
}
#adm-reply-input {
    resize: none;
    border-radius: 4px 0 0 4px !important;
    height: auto !important;
    min-height: 52px;
}
.cw-send-btn {
    border-radius: 0 4px 4px 0 !important;
    background: #ff4d00 !important;
    border-color: #ff4d00 !important;
}
.cw-send-btn:hover {
    background: #e03e00 !important;
    border-color: #e03e00 !important;
}

/* Right Customer Sidebar */
.cw-customer-sidebar {
    width: 260px;
    border-left: 1px solid #2a2a2d;
    background: #18181b;
    display: flex;
    flex-direction: column;
}
.cw-customer-header {
    padding: 14px 16px;
    background: #1c1c1e;
    border-bottom: 1px solid #2a2a2d;
}
.cw-customer-big-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #ff4d00;
    color: #ffffff;
    font-size: 24px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cw-divider {
    height: 1px;
    background: #2a2a2d;
}
</style>

{{-- Ensure jQuery is loaded before our chat script runs --}}
<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
<script>
(function() {
    'use strict';

    function initAdminLiveChat() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initAdminLiveChat, 50);
            return;
        }

        var $ = jQuery;
        var currentThread = null;
        var currentLastId = 0;
        var currentLastReadAdminId = 0;
        var pollListTimer = null;
        var pollThreadTimer = null;
        var adminTypingPingTimer = null;
        var soundEnabled = localStorage.getItem('adm_chat_sound') !== 'off';
        var audioCtx = null;
        var activeFilter = 'all';
        var cachedThreads = [];

        // ── SOUND SYNTHESIS (Web Audio API) ───────────────────────────
        function playIncomingTone() {
            if (!soundEnabled) return;
            try {
                var AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                if (!audioCtx) audioCtx = new AudioContext();
                if (audioCtx.state === 'suspended') audioCtx.resume();

                var now = audioCtx.currentTime;
                var osc1 = audioCtx.createOscillator();
                var gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now); // D5
                gain1.gain.setValueAtTime(0.2, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.22);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.22);

                var osc2 = audioCtx.createOscillator();
                var gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.11); // A5
                gain2.gain.setValueAtTime(0.24, now + 0.11);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.42);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.11);
                osc2.stop(now + 0.42);
            } catch (e) {
                console.warn('[Admin Sound]', e);
            }
        }

        function updateSoundUI() {
            if (soundEnabled) {
                $('#adm-sound-icon').html('<i class="fa fa-volume-up"></i> Sound ON');
                $('#adm-sound-toggle').css({'color': '#81c868', 'border-color': '#81c868'});
            } else {
                $('#adm-sound-icon').html('<i class="fa fa-volume-off"></i> Sound OFF');
                $('#adm-sound-toggle').css({'color': '#f05050', 'border-color': '#f05050'});
            }
        }
        updateSoundUI();

        $('#adm-sound-toggle').on('click', function(e) {
            e.preventDefault();
            soundEnabled = !soundEnabled;
            localStorage.setItem('adm_chat_sound', soundEnabled ? 'on' : 'off');
            updateSoundUI();
            if (soundEnabled) playIncomingTone();
        });

        // ── FILTER PILLS ─────────────────────────────────────────────
        $('.cw-pill-btn').on('click', function() {
            $('.cw-pill-btn').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            renderThreadList();
        });

        $('#adm-thread-search').on('input', function() {
            renderThreadList();
        });

        // ── REAL-TIME ADMIN TYPING PING ──────────────────────────────
        $('#adm-reply-input').on('input', function() {
            if (!currentThread) return;
            if (!adminTypingPingTimer) {
                $.post('{{ route("admin.livechat.typing") }}', {
                    _token: '{{ csrf_token() }}',
                    type: currentThread.type,
                    target_id: currentThread.id
                });
                adminTypingPingTimer = setTimeout(function() {
                    adminTypingPingTimer = null;
                }, 2500);
            }
        });

        // ── FETCH CONVERSATION LIST ──────────────────────────────────
        function loadConversations(isPolling) {
            $.ajax({
                url: '{{ route("admin.livechat.conversations") }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var prevTotalUnread = parseInt($('#adm-total-unread-badge').text()) || 0;
                    var newTotalUnread = data.total_unread || 0;

                    if (isPolling && newTotalUnread > prevTotalUnread) {
                        playIncomingTone();
                    }

                    if (newTotalUnread > 0) {
                        $('#adm-total-unread-badge').text(newTotalUnread).show();
                    } else {
                        $('#adm-total-unread-badge').hide();
                    }

                    cachedThreads = data.threads || [];
                    renderThreadList();

                    // If no thread is currently selected, auto-select the first one
                    if (!currentThread && cachedThreads.length > 0) {
                        var first = cachedThreads[0];
                        selectThread(first.key, first.type, first.id);
                    }
                },
                error: function(err) {
                    console.error('[Admin Chat Poll Error]', err);
                    if (!isPolling) {
                        $('#adm-thread-list').html('<div class="text-center text-danger p-4 font-12"><i class="fa fa-exclamation-triangle"></i> Failed to load conversations.<br><a href="javascript:void(0)" id="retry-conv" class="text-primary mt-2 d-inline-block">Retry</a></div>');
                        $('#retry-conv').on('click', function() { loadConversations(false); });
                    }
                }
            });
        }

        function renderThreadList() {
            var query = $('#adm-thread-search').val().toLowerCase().trim();
            var container = $('#adm-thread-list');

            var filtered = cachedThreads.filter(function(t) {
                if (activeFilter === 'unread' && t.unread_count === 0) return false;
                if (activeFilter === 'user' && t.type !== 'user') return false;
                if (activeFilter === 'guest' && t.type !== 'guest') return false;
                if (query) {
                    return (t.name && t.name.toLowerCase().indexOf(query) !== -1) ||
                           (t.last_message && t.last_message.toLowerCase().indexOf(query) !== -1) ||
                           (t.email && t.email.toLowerCase().indexOf(query) !== -1);
                }
                return true;
            });

            if (filtered.length === 0) {
                container.html('<div class="text-center text-muted p-4 font-13">No conversations found</div>');
                return;
            }

            var html = '';
            filtered.forEach(function(t) {
                var isActive = (currentThread && currentThread.key === t.key) ? 'active' : '';
                var initials = t.type === 'guest' ? 'G' : (t.name ? t.name.substring(0, 2).toUpperCase() : 'U');
                var avatarClass = t.type === 'guest' ? 'guest' : 'user';
                var unreadHtml = t.unread_count > 0 ? '<span class="cw-unread-pill">' + t.unread_count + '</span>' : '';

                var previewText = t.is_typing ?
                    '<span class="text-success font-weight-bold"><i class="fa fa-pencil"></i> typing...</span>' :
                    '<span class="text-truncate">' + escapeHtml(t.last_message || '') + '</span>';

                html += '<div class="cw-thread-item ' + isActive + '" data-key="' + t.key + '" data-type="' + t.type + '" data-id="' + t.id + '">';
                html += '  <div class="cw-item-avatar ' + avatarClass + '">' + initials + '</div>';
                html += '  <div class="cw-item-info">';
                html += '    <div class="cw-item-title">';
                html += '      <span>' + escapeHtml(t.name) + '</span>';
                html += '      <span class="cw-item-time">' + (t.last_time || '') + '</span>';
                html += '    </div>';
                html += '    <div class="cw-item-snippet d-flex justify-content-between align-items-center">';
                html += '      ' + previewText;
                html += '      ' + unreadHtml;
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            });

            container.html(html);
        }

        // ── SELECT THREAD ────────────────────────────────────────────
        function selectThread(key, type, id) {
            $('.cw-thread-item').removeClass('active');
            $('.cw-thread-item[data-key="' + key + '"]').addClass('active');

            currentThread = { key: key, type: type, id: id };
            currentLastId = 0;
            currentLastReadAdminId = 0;

            $('#adm-empty-state').hide();
            $('#adm-active-state').show();
            $('#adm-customer-sidebar').show();
            $('#adm-messages-stream').html('<div class="text-center text-muted p-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading chat...</div>');

            loadThread(false);
            restartThreadPolling();
        }

        $(document).on('click', '.cw-thread-item', function() {
            var key = $(this).data('key');
            var type = $(this).data('type');
            var id = $(this).data('id');
            selectThread(key, type, id);
        });

        function loadThread(isPolling) {
            if (!currentThread) return;

            var url = '{{ route("admin.livechat.thread") }}?type=' + currentThread.type +
                      '&id=' + encodeURIComponent(currentThread.id) +
                      '&last_id=' + currentLastId;

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (!res.success) return;

                    // Update Read Receipts for Admin messages if user read them
                    if (res.last_read_admin_msg_id) {
                        updateAdminReadReceipts(res.last_read_admin_msg_id);
                    }

                    // Typing Indicator for Customer
                    if (res.is_user_typing) {
                        $('#adm-user-typing-badge').show();
                        $('#adm-user-typing-indicator').show();
                    } else {
                        $('#adm-user-typing-badge').hide();
                        $('#adm-user-typing-indicator').hide();
                    }

                    // Update Customer Sidebar details
                    if (res.customer && !isPolling) {
                        var cust = res.customer;
                        $('#adm-header-name').text(cust.name);
                        $('#adm-header-subinfo').text((cust.type === 'user' ? 'Registered User' : 'Guest Visitor') + ' • ' + (cust.email || ''));
                        $('#adm-header-avatar').text(cust.type === 'guest' ? 'G' : cust.name.substring(0, 2).toUpperCase());

                        $('#adm-cust-name').text(cust.name);
                        $('#adm-cust-big-avatar').text(cust.type === 'guest' ? 'G' : cust.name.substring(0, 2).toUpperCase());
                        $('#adm-cust-badge').text(cust.type === 'user' ? 'Registered User' : 'Guest Visitor')
                            .removeClass('badge-primary badge-secondary')
                            .addClass(cust.type === 'user' ? 'badge-primary' : 'badge-secondary');
                        $('#adm-cust-email').text(cust.email || 'N/A');
                        $('#adm-cust-joined').text(cust.created_at || cust.first_seen || 'N/A');

                        if (cust.ip_address) {
                            $('#adm-cust-ip').text(cust.ip_address);
                            $('#adm-cust-ip-row').show();
                        } else {
                            $('#adm-cust-ip-row').hide();
                        }

                        if (cust.plan) {
                            $('#adm-cust-plan').text(cust.plan);
                            $('#adm-cust-plan-row').show();
                        } else {
                            $('#adm-cust-plan-row').hide();
                        }
                    }

                    if (currentLastId === 0) {
                        $('#adm-messages-stream').empty();
                    }

                    if (res.messages && res.messages.length > 0) {
                        var hadNewUserMessage = false;

                        res.messages.forEach(function(m) {
                            if ($('#adm-msg-' + m.id).length === 0) {
                                appendAdminMessage(m);
                                if (m.sender !== 'admin') {
                                    hadNewUserMessage = true;
                                }
                            }
                        });

                        if (isPolling && hadNewUserMessage) {
                            playIncomingTone();
                        }

                        scrollToBottom();
                    } else if (currentLastId === 0) {
                        $('#adm-messages-stream').html('<div class="text-center text-muted p-4">No messages yet in this conversation.</div>');
                    }
                }
            });
        }

        // Render message in Admin panel
        function appendAdminMessage(m) {
            var isOutgoing = (m.sender === 'admin');
            var rowClass = isOutgoing ? 'outgoing' : 'incoming';
            var senderLabel = isOutgoing ? 'You (Support Admin)' : (m.sender_name || 'Customer');

            var tickHtml = '';
            if (isOutgoing) {
                var isRead = m.is_read || (currentLastReadAdminId && m.id <= currentLastReadAdminId);
                var tickClass = isRead ? 'cw-adm-tick cw-tick-read' : 'cw-adm-tick';
                var tickText = isRead ? '&#10003;&#10003;' : '&#10003;';
                var tickTitle = isRead ? 'Read by Customer' : 'Delivered';
                tickHtml = '<span class="' + tickClass + '" title="' + tickTitle + '">' + tickText + '</span>';
            }

            var html = '<div class="cw-msg-bubble-row ' + rowClass + '" id="adm-msg-' + m.id + '" data-id="' + m.id + '">';
            html += '  <div class="cw-msg-bubble-sender">' + escapeHtml(senderLabel) + '</div>';
            html += '  <div class="cw-msg-bubble-box">' + m.message.replace(/\n/g, '<br>') + '</div>';
            html += '  <div class="cw-msg-bubble-meta">';
            html += '    <span>' + (m.time || '') + '</span>';
            html += '    ' + tickHtml;
            html += '  </div>';
            html += '</div>';

            $('#adm-messages-stream').append(html);

            if (m.id > currentLastId) {
                currentLastId = m.id;
            }
        }

        // Update read receipts for Admin messages
        function updateAdminReadReceipts(lastReadId) {
            if (!lastReadId) return;
            currentLastReadAdminId = Math.max(currentLastReadAdminId, lastReadId);

            $('#adm-messages-stream .cw-msg-bubble-row.outgoing').each(function() {
                var msgId = parseInt($(this).data('id'), 10);
                if (msgId <= currentLastReadAdminId) {
                    var tick = $(this).find('.cw-adm-tick');
                    if (tick.length && !tick.hasClass('cw-tick-read')) {
                        tick.addClass('cw-tick-read');
                        tick.html('&#10003;&#10003;'); // Double blue checkmark
                        tick.attr('title', 'Read by Customer');
                    }
                }
            });
        }

        function scrollToBottom() {
            var body = document.getElementById('adm-messages-body');
            if (body) {
                body.scrollTop = body.scrollHeight;
            }
        }

        // ── SEND REPLY ───────────────────────────────────────────────
        $('#adm-reply-form').on('submit', function(e) {
            e.preventDefault();
            if (!currentThread) return;

            var text = $('#adm-reply-input').val().trim();
            if (!text) return;

            $('#adm-reply-input').val('');

            $.ajax({
                url: '{{ route("admin.livechat.reply") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: currentThread.type,
                    target_id: currentThread.id,
                    message: text
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.message) {
                        appendAdminMessage(res.message);
                        scrollToBottom();
                        loadConversations(false);
                    }
                },
                error: function() {
                    alert('Failed to send reply. Please try again.');
                }
            });
        });

        $('#adm-reply-input').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#adm-reply-form').trigger('submit');
            }
        });

        // ── DELETE THREAD ────────────────────────────────────────────
        $('#adm-delete-thread-btn').on('click', function() {
            if (!currentThread) return;
            if (!confirm('Are you sure you want to permanently delete this chat history?')) return;

            $.ajax({
                url: '{{ route("admin.livechat.delete_thread") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: currentThread.type,
                    id: currentThread.id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        currentThread = null;
                        currentLastId = 0;
                        currentLastReadAdminId = 0;
                        $('#adm-active-state').hide();
                        $('#adm-customer-sidebar').hide();
                        $('#adm-empty-state').show();
                        loadConversations(false);
                    }
                }
            });
        });

        // ── POLLING INTERVALS ────────────────────────────────────────
        function restartThreadPolling() {
            if (pollThreadTimer) clearInterval(pollThreadTimer);
            pollThreadTimer = setInterval(function() {
                loadThread(true);
            }, 2500);
        }

        pollListTimer = setInterval(function() {
            loadConversations(true);
        }, 5000);

        // Initial Load
        loadConversations(false);

        function escapeHtml(text) {
            if (!text) return '';
            var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initAdminLiveChat();
    } else {
        document.addEventListener('DOMContentLoaded', initAdminLiveChat);
    }
})();
</script>
@endsection
