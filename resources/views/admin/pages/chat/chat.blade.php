@extends('admin.admin_app')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            {{-- Page Header --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0 font-size-18">Live Chat & Support Inbox</h4>
                        <div class="page-title-right">
                            <span class="badge badge-success px-3 py-2" id="adm-status-indicator">
                                <span class="adm-pulse-green"></span> Live Polling Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Chat Container --}}
            <div class="card livechat-card">
                <div class="livechat-wrapper">
                    
                    {{-- LEFT SIDEBAR: Conversation List --}}
                    <div class="livechat-sidebar">
                        <div class="sidebar-header">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="mb-0 font-weight-bold">
                                    Conversations
                                    <span class="badge badge-danger ml-1" id="adm-total-unread-badge" style="display:none;">0</span>
                                </h5>
                                {{-- Sound Toggle --}}
                                <button type="button" id="adm-sound-toggle" class="btn btn-sm btn-outline-secondary" title="Toggle Sound Tone">
                                    <span id="adm-sound-icon">&#128266; Sound ON</span>
                                </button>
                            </div>
                            {{-- Search Box --}}
                            <div class="search-wrap">
                                <input type="text" id="adm-thread-search" class="form-control form-control-sm" placeholder="Search conversations...">
                            </div>
                            {{-- Filter Pills --}}
                            <div class="filter-pills mt-2">
                                <button type="button" class="pill-btn active" data-filter="all">All</button>
                                <button type="button" class="pill-btn" data-filter="unread">Unread</button>
                                <button type="button" class="pill-btn" data-filter="user">Users</button>
                                <button type="button" class="pill-btn" data-filter="guest">Guests</button>
                            </div>
                        </div>

                        {{-- Conversation Items List --}}
                        <div class="sidebar-thread-list" id="adm-thread-list">
                            <div class="text-center text-muted p-4" id="adm-threads-loading">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <div class="mt-2 font-12">Loading conversations...</div>
                            </div>
                        </div>
                    </div>

                    {{-- CENTER: Active Chat Area --}}
                    <div class="livechat-main" id="adm-chat-main">
                        
                        {{-- Placeholder when no thread is selected --}}
                        <div class="chat-empty-state" id="adm-empty-state">
                            <div class="empty-icon">&#128172;</div>
                            <h4>Select a conversation</h4>
                            <p class="text-muted">Choose a visitor or registered user from the left inbox to view messages and reply in real time.</p>
                        </div>

                        {{-- Active Chat View --}}
                        <div class="chat-active-state" id="adm-active-state" style="display: none;">
                            {{-- Thread Header --}}
                            <div class="chat-thread-header">
                                <div class="d-flex align-items-center">
                                    <div class="thread-avatar-box mr-3" id="adm-header-avatar"></div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold" id="adm-header-name">Customer Name</h5>
                                        <div class="font-12 text-muted" id="adm-header-subinfo">User &bull; user@example.com</div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="adm-delete-thread-btn" title="Delete Conversation">
                                        <i class="fa fa-trash"></i> Delete Chat
                                    </button>
                                </div>
                            </div>

                            {{-- Message Stream --}}
                            <div class="chat-messages-body" id="adm-messages-body">
                                <div id="adm-messages-stream"></div>
                            </div>

                            {{-- Reply Input Area --}}
                            <div class="chat-reply-bar">
                                <form id="adm-reply-form">
                                    <div class="input-group">
                                        <textarea id="adm-reply-input" class="form-control" placeholder="Write your reply... (Press Enter to send, Shift+Enter for newline)" rows="2"></textarea>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="adm-send-btn">
                                                <i class="fa fa-paper-plane mr-1"></i> Send
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1 font-11 text-muted">
                                        <span>Tip: Responses appear instantly in the user's floating widget.</span>
                                        <span><kbd>Enter</kbd> to send</span>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT SIDEBAR: Customer Profile Details --}}
                    <div class="livechat-customer-sidebar" id="adm-customer-sidebar" style="display: none;">
                        <div class="sidebar-header border-bottom">
                            <h6 class="mb-0 font-weight-bold">Visitor Details</h6>
                        </div>
                        <div class="customer-info-body p-3">
                            <div class="text-center mb-3">
                                <div class="customer-big-avatar mx-auto mb-2" id="adm-cust-big-avatar">U</div>
                                <h6 class="font-weight-bold mb-0" id="adm-cust-name">User Name</h6>
                                <span class="badge badge-primary font-11 mt-1" id="adm-cust-badge">Registered User</span>
                            </div>

                            <hr>

                            <div class="detail-row mb-2">
                                <label class="font-11 text-muted text-uppercase d-block mb-0">Email</label>
                                <span class="font-13 font-weight-500" id="adm-cust-email">N/A</span>
                            </div>

                            <div class="detail-row mb-2" id="adm-cust-phone-row">
                                <label class="font-11 text-muted text-uppercase d-block mb-0">Phone</label>
                                <span class="font-13 font-weight-500" id="adm-cust-phone">N/A</span>
                            </div>

                            <div class="detail-row mb-2" id="adm-cust-ip-row">
                                <label class="font-11 text-muted text-uppercase d-block mb-0">IP Address</label>
                                <span class="font-13 font-weight-500" id="adm-cust-ip">N/A</span>
                            </div>

                            <div class="detail-row mb-2">
                                <label class="font-11 text-muted text-uppercase d-block mb-0">Joined / First Seen</label>
                                <span class="font-13 font-weight-500" id="adm-cust-joined">N/A</span>
                            </div>

                            <div class="detail-row mb-2" id="adm-cust-plan-row">
                                <label class="font-11 text-muted text-uppercase d-block mb-0">Plan</label>
                                <span class="font-13 font-weight-500" id="adm-cust-plan">Free</span>
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
/* ─── ADMIN LIVE CHAT DASHBOARD STYLES ──────────────────────────────── */
.livechat-card {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
}
.livechat-wrapper {
    display: flex;
    height: 720px;
    background: #ffffff;
}

/* Pulse Green Indicator */
.adm-pulse-green {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ffffff;
    display: inline-block;
    margin-right: 4px;
    vertical-align: middle;
}

/* Left Sidebar */
.livechat-sidebar {
    width: 320px;
    border-right: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    background: #f8fafc;
}
.sidebar-header {
    padding: 14px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e9ecef;
}
.filter-pills {
    display: flex;
    gap: 4px;
}
.pill-btn {
    border: none;
    background: #f1f5f9;
    color: #64748b;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.15s;
}
.pill-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
}
.pill-btn.active {
    background: #ff4d00;
    color: #ffffff;
}

.sidebar-thread-list {
    flex: 1;
    overflow-y: auto;
}
.thread-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background 0.15s;
    position: relative;
}
.thread-item:hover {
    background: #f1f5f9;
}
.thread-item.active {
    background: #fff4ed;
    border-left: 4px solid #ff4d00;
}
.thread-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #475569;
    font-weight: 700;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-right: 12px;
    position: relative;
}
.thread-avatar.guest {
    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
    color: #ffffff;
}
.thread-avatar.user {
    background: linear-gradient(135deg, #ff4d00 0%, #d83b01 100%);
    color: #ffffff;
}
.thread-info {
    flex: 1;
    min-width: 0;
}
.thread-name {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.thread-time {
    font-size: 10px;
    color: #94a3b8;
    font-weight: normal;
}
.thread-snippet {
    font-size: 12px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.thread-badge-unread {
    background: #ef4444;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 6px;
}

/* Center Chat Main */
.livechat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #ffffff;
}
.chat-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
    background: #f8fafc;
}
.chat-empty-state .empty-icon {
    font-size: 48px;
    margin-bottom: 15px;
    color: #cbd5e1;
}
.chat-active-state {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.chat-thread-header {
    padding: 14px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.thread-avatar-box {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #ff4d00;
    color: #ffffff;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Message Stream */
.chat-messages-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
}
#adm-messages-stream {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.adm-msg-row {
    display: flex;
    flex-direction: column;
    max-width: 75%;
    animation: adm-msg-fade 0.15s ease;
}
@keyframes adm-msg-fade {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
.adm-msg-row.incoming {
    align-self: flex-start;
}
.adm-msg-row.outgoing {
    align-self: flex-end;
}
.adm-msg-sender {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 3px;
    font-weight: 600;
}
.adm-msg-row.outgoing .adm-msg-sender {
    text-align: right;
    color: #ff4d00;
}
.adm-msg-bubble {
    padding: 10px 15px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}
.adm-msg-row.incoming .adm-msg-bubble {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 3px;
}
.adm-msg-row.outgoing .adm-msg-bubble {
    background: #0284c7;
    color: #ffffff;
    border-bottom-right-radius: 3px;
}
.adm-msg-time {
    font-size: 10px;
    margin-top: 4px;
}
.adm-msg-row.incoming .adm-msg-time {
    color: #94a3b8;
}
.adm-msg-row.outgoing .adm-msg-time {
    color: rgba(255, 255, 255, 0.75);
    text-align: right;
}

/* Reply Bar */
.chat-reply-bar {
    padding: 12px 18px;
    background: #ffffff;
    border-top: 1px solid #e9ecef;
}
#adm-reply-input {
    resize: none;
    border-radius: 8px 0 0 8px;
}
#adm-send-btn {
    border-radius: 0 8px 8px 0;
    background: #ff4d00;
    border-color: #ff4d00;
}
#adm-send-btn:hover {
    background: #e03e00;
    border-color: #e03e00;
}

/* Right Customer Sidebar */
.livechat-customer-sidebar {
    width: 260px;
    border-left: 1px solid #e9ecef;
    background: #ffffff;
    display: flex;
    flex-direction: column;
}
.customer-big-avatar {
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
</style>

<script>
$(document).ready(function() {
    'use strict';

    var currentThread = null; // { type: 'user'|'guest', id: '...', key: '...' }
    var currentLastId = 0;
    var pollListTimer = null;
    var pollThreadTimer = null;
    var soundEnabled = localStorage.getItem('adm_chat_sound') !== 'off';
    var audioCtx = null;
    var activeFilter = 'all';
    var cachedThreads = [];

    // ── SOUND SYNTHESIS ──────────────────────────────────────────────
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
            $('#adm-sound-icon').html('&#128266; Sound ON');
            $('#adm-sound-toggle').removeClass('btn-outline-danger').addClass('btn-outline-secondary');
        } else {
            $('#adm-sound-icon').html('&#128263; Sound OFF');
            $('#adm-sound-toggle').removeClass('btn-outline-secondary').addClass('btn-outline-danger');
        }
    }
    updateSoundUI();

    $('#adm-sound-toggle').on('click', function() {
        soundEnabled = !soundEnabled;
        localStorage.setItem('adm_chat_sound', soundEnabled ? 'on' : 'off');
        updateSoundUI();
        if (soundEnabled) playIncomingTone();
    });

    // ── FILTER PILLS ─────────────────────────────────────────────────
    $('.pill-btn').on('click', function() {
        $('.pill-btn').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        renderThreadList();
    });

    $('#adm-thread-search').on('input', function() {
        renderThreadList();
    });

    // ── FETCH CONVERSATION LIST ──────────────────────────────────────
    function loadConversations(isPolling) {
        $.ajax({
            url: '{{ route("admin.livechat.conversations") }}',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var prevTotalUnread = parseInt($('#adm-total-unread-badge').text()) || 0;
                var newTotalUnread = data.total_unread || 0;

                // Play tone if unread messages increased
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
            var unreadHtml = t.unread_count > 0 ? '<span class="thread-badge-unread">' + t.unread_count + '</span>' : '';

            html += '<div class="thread-item ' + isActive + '" data-key="' + t.key + '" data-type="' + t.type + '" data-id="' + t.id + '">';
            html += '  <div class="thread-avatar ' + avatarClass + '">' + initials + '</div>';
            html += '  <div class="thread-info">';
            html += '    <div class="thread-name">';
            html += '      <span>' + escapeHtml(t.name) + '</span>';
            html += '      <span class="thread-time">' + (t.last_time || '') + '</span>';
            html += '    </div>';
            html += '    <div class="thread-snippet d-flex justify-content-between align-items-center">';
            html += '      <span class="text-truncate">' + escapeHtml(t.last_message || '') + '</span>';
            html += '      ' + unreadHtml;
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        });

        container.html(html);
    }

    // ── SELECT & LOAD THREAD ─────────────────────────────────────────
    $(document).on('click', '.thread-item', function() {
        var key = $(this).data('key');
        var type = $(this).data('type');
        var id = $(this).data('id');

        $('.thread-item').removeClass('active');
        $(this).addClass('active');

        currentThread = { key: key, type: type, id: id };
        currentLastId = 0;

        $('#adm-empty-state').hide();
        $('#adm-active-state').show();
        $('#adm-customer-sidebar').show();
        $('#adm-messages-stream').html('<div class="text-center text-muted p-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading thread...</div>');

        loadThread(false);
        restartThreadPolling();
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

    function appendAdminMessage(m) {
        var isOutgoing = (m.sender === 'admin');
        var rowClass = isOutgoing ? 'outgoing' : 'incoming';
        var senderLabel = isOutgoing ? 'You (Admin)' : (m.sender_name || 'Customer');

        var html = '<div class="adm-msg-row ' + rowClass + '" id="adm-msg-' + m.id + '">';
        html += '  <div class="adm-msg-sender">' + escapeHtml(senderLabel) + '</div>';
        html += '  <div class="adm-msg-bubble">' + m.message.replace(/\n/g, '<br>') + '</div>';
        html += '  <div class="adm-msg-time">' + (m.time || '') + '</div>';
        html += '</div>';

        $('#adm-messages-stream').append(html);

        if (m.id > currentLastId) {
            currentLastId = m.id;
        }
    }

    function scrollToBottom() {
        var body = document.getElementById('adm-messages-body');
        if (body) {
            body.scrollTop = body.scrollHeight;
        }
    }

    // ── SEND REPLY ───────────────────────────────────────────────────
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

    // Enter sends reply, Shift+Enter creates new line
    $('#adm-reply-input').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#adm-reply-form').trigger('submit');
        }
    });

    // ── DELETE THREAD ────────────────────────────────────────────────
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
                    $('#adm-active-state').hide();
                    $('#adm-customer-sidebar').hide();
                    $('#adm-empty-state').show();
                    loadConversations(false);
                }
            }
        });
    });

    // ── POLLING INTERVALS ────────────────────────────────────────────
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
});
</script>
@endsection
