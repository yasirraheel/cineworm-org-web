@extends('site_app')

@section('head_title', 'My Live Broadcasts | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<style>
    /* Ensure site header dropdown float over iframe */
    header, .header-area, .navbar, .user-dropdown, .dropdown-menu {
        z-index: 99999 !important;
    }

    .cineworm-meeting-box {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .cineworm-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 6px;
        padding: 12px 18px;
        margin-bottom: 15px;
    }

    .iframe-wrapper {
        position: relative;
        width: 100%;
        height: calc(85vh - 180px);
        min-height: 480px;
        max-height: 680px;
        border-radius: 6px;
        overflow: hidden;
        background: #000;
        border: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 1;
    }

    iframe.cinemeet-frame {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

    /* Guided Tour Modal Styles matching native app theme */
    .tour-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .tour-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .tour-card {
        background: #141821;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        width: 100%;
        max-width: 480px;
        padding: 28px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8);
        position: relative;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .tour-overlay.active .tour-card {
        transform: translateY(0);
    }

    .tour-close-btn {
        position: absolute;
        top: 16px;
        right: 18px;
        background: transparent;
        border: none;
        color: #888;
        font-size: 20px;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .tour-close-btn:hover {
        color: #fff;
    }

    .tour-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        background: rgba(229, 9, 20, 0.15);
        border: 1px solid rgba(229, 9, 20, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        color: #e50914;
        font-size: 24px;
    }

    .tour-title {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .tour-description {
        color: #ccc;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 22px;
        min-height: 60px;
    }

    .tour-dots {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 24px;
    }

    .tour-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .tour-dot.active {
        background: #e50914;
        width: 22px;
        border-radius: 10px;
    }

    .tour-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .tour-btn-secondary {
        background: transparent;
        border: none;
        color: #aaa;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        padding: 8px 14px;
        border-radius: 4px;
        transition: color 0.2s ease;
    }

    .tour-btn-secondary:hover {
        color: #fff;
    }

    .tour-btn-primary {
        background: #e50914;
        color: #fff;
        border: none;
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s ease;
    }

    .tour-btn-primary:hover {
        background: #b80710;
        color: #fff;
    }
</style>

<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>My Live Broadcasts</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li>My Live Broadcasts</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="edit-profile-area vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                
                {{-- Standard User Sidebar --}}
                @include('pages.user._sidebar')
                
                {{-- Main Content Workspace Column --}}
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    <div class="edit-profile-form">

                        {{-- Standard App Flash Messages --}}
                        @include('pages.user.whatsapp._flash')

                        {{-- Header Row --}}
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-6">
                                <h3 style="color:#fff;margin-bottom:5px;"><i class="fa fa-video-camera" style="color:#e50914;margin-right:8px;"></i> Live Broadcasts</h3>
                                <p style="color:#ccc;font-size:14px;">Manage and customize your live video meeting rooms.</p>
                            </div>
                            <div class="col-md-6 text-right" style="text-align: right; padding-top: 10px;">
                                <button type="button" onclick="startLiveTour(true);" class="vfx-item-btn-danger text-uppercase" style="background: rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.2); color:#fff; text-decoration:none; margin-right:5px;">
                                    <i class="fa fa-question-circle"></i> Quick Tour
                                </button>
                                <a href="javascript:void(0);" onclick="openNewMeetingModal();" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none; margin-right:5px;">
                                    <i class="fa fa-sliders"></i> Customize & Create
                                </a>
                                @if(!$inCall)
                                    <a href="{{ URL::to('user/live_broadcasts?room=' . $roomId) }}" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none; background-color:#28a745;">
                                        <i class="fa fa-play"></i> Start Call
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($inCall)
                            {{-- ACTIVE CALL WORKSPACE --}}
                            <div class="cineworm-meeting-box">
                                
                                {{-- Toolbar --}}
                                <div class="cineworm-toolbar">
                                    <div>
                                        <h4 style="color:#fff; margin:0; font-size:16px; font-weight:700;">
                                            <i class="fa fa-video-camera" style="color:#e50914; margin-right:6px;"></i> {{ $meetingTitle }}
                                        </h4>
                                        <p style="color:#ccc; margin:2px 0 0; font-size:13px;">
                                            Room ID: <code style="color:#fe0278;">{{ $roomId }}</code>
                                            @if(!empty($roomPassword))
                                                <span class="badge badge-warning" style="background:#ffc107; color:#000; margin-left:6px;"><i class="fa fa-lock"></i> Protected</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                        {{-- Leave Call --}}
                                        <a href="{{ URL::to('user/live_broadcasts') }}" class="btn btn-sm btn-danger" onclick="return leaveCallConfirm(this.href);" style="background:#e50914; border:none; padding:6px 14px; font-size:13px; font-weight:600;">
                                            <i class="fa fa-phone"></i> Leave Call
                                        </a>

                                        {{-- Copy Link --}}
                                        <button type="button" class="btn btn-sm btn-success" onclick="copyInviteLink('{{ $shareableJoinUrl }}')" style="background:#28a745; border:none; padding:6px 14px; font-size:13px; font-weight:600;">
                                            <i class="fa fa-copy"></i> Copy Link
                                        </button>

                                        {{-- Share WhatsApp --}}
                                        <a href="https://api.whatsapp.com/send?text={{ urlencode('Join my live meeting on CineWorm: ' . $shareableJoinUrl) }}" target="_blank" class="btn btn-sm btn-success" style="background:#25D366; border:none; padding:6px 14px; font-size:13px; font-weight:600; color:#fff !important;">
                                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                        </a>

                                        {{-- Fullscreen --}}
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleIframeFullscreen()" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; padding:6px 14px; font-size:13px;">
                                            <i class="fa fa-expand"></i> Fullscreen
                                        </button>
                                    </div>
                                </div>

                                {{-- Guest Link Bar --}}
                                <div style="margin-bottom: 15px;">
                                    <label style="color:#ccc; font-size:13px; font-weight:600; margin-bottom:5px; display:block;">Guest Share Link</label>
                                    <div style="display: flex; align-items: center; width: 100%; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; overflow: hidden; background: rgba(0,0,0,0.4);">
                                        <input type="text" id="shareUrlInput" value="{{ $shareableJoinUrl }}" readonly style="flex: 1; background: transparent; border: none; color: #fff; padding: 10px 14px; font-size: 14px; font-family: monospace; outline: none;">
                                        <button type="button" onclick="copyInviteLink('{{ $shareableJoinUrl }}')" style="background: #e50914; color: #fff; border: none; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                                            <i class="fa fa-copy"></i> Copy Link
                                        </button>
                                    </div>
                                </div>

                                {{-- Embedded Call View --}}
                                <div class="iframe-wrapper" id="meetingFrameWrapper">
                                    <iframe id="cinemeetFrame"
                                        class="cinemeet-frame"
                                        src="{{ $cinemeetEmbedUrl }}"
                                        allow="camera; microphone; display-capture; autoplay; clipboard-write; fullscreen"
                                        title="CineMeet Video Meeting">
                                    </iframe>
                                </div>

                            </div>
                        @endif

                        {{-- HISTORY TABLE --}}
                        <div class="table-responsive" style="margin-top: 15px;">
                            <table class="table table-bordered" style="color: #fff; border-color: rgba(255,255,255,0.1);">
                                <thead>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Title</th>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Room ID</th>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Security</th>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Created</th>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($live_broadcasts as $broadcast)
                                        <tr>
                                            <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">{{ $broadcast->title }}</td>
                                            <td style="border-color: rgba(255,255,255,0.1); color:#fe0278; font-family:monospace;">{{ $broadcast->zoom_meeting_id }}</td>
                                            <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">
                                                @if(!empty($broadcast->zoom_meeting_password))
                                                    <span class="badge" style="background-color:#ffc107; color:#000; padding:4px 8px;"><i class="fa fa-lock"></i> Protected</span>
                                                @else
                                                    <span class="badge" style="background-color:#17a2b8; color:#fff; padding:4px 8px;"><i class="fa fa-unlock"></i> Open</span>
                                                @endif
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">{{ $broadcast->created_at ? $broadcast->created_at->format('M d, Y') : '—' }}</td>
                                            <td style="border-color: rgba(255,255,255,0.1);">
                                                <a href="{{ URL::to('user/live_broadcasts?room=' . $broadcast->zoom_meeting_id) }}" class="btn btn-sm btn-success" style="background:#e50914; border:none; padding:5px 12px; font-size:13px; margin-right:4px; text-decoration:none;">
                                                    <i class="fa fa-video-camera"></i> Start Call
                                                </a>
                                                <button type="button" class="btn btn-sm btn-info" style="background:#17a2b8; border:none; color:#fff; padding:5px 12px; font-size:13px; margin-right:4px;" onclick="openEditMeetingModal('{{ $broadcast->id }}', '{{ addslashes($broadcast->title) }}', '{{ addslashes($broadcast->zoom_meeting_password) }}')">
                                                    <i class="fa fa-sliders"></i> Customize
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; padding:5px 12px; font-size:13px;" onclick="copyInviteLink('{{ url('meeting/join/' . $broadcast->zoom_meeting_id) }}@if(!empty($broadcast->zoom_meeting_password))?roomPassword={{ urlencode($broadcast->zoom_meeting_password) }}@endif')">
                                                    <i class="fa fa-copy"></i> Copy Link
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center" style="border-color: rgba(255,255,255,0.1); padding: 30px; color:#ccc;">
                                                <i class="fa fa-video-camera" style="font-size:32px; display:block; margin-bottom:14px; opacity:0.2;"></i>
                                                No live broadcasts scheduled yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:20px;">
                            @include('_particles.pagination', ['paginator' => $live_broadcasts])
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Interactive Tour Modal Overlay Component --}}
<div id="liveTourOverlay" class="tour-overlay">
    <div class="tour-card">
        <button type="button" class="tour-close-btn" onclick="closeLiveTour()">&times;</button>

        <div id="tourIconBox" class="tour-icon-box">
            <i id="tourIcon" class="fa fa-video-camera"></i>
        </div>

        <h3 id="tourTitle" class="tour-title">Welcome to Live Broadcasts</h3>
        <p id="tourDescription" class="tour-description">
            Host HD video meetings, webinars, screen sharing, and interactive breakout rooms directly inside your CineWorm workspace.
        </p>

        <div id="tourDots" class="tour-dots">
            <span class="tour-dot active" onclick="goToTourStep(0)"></span>
            <span class="tour-dot" onclick="goToTourStep(1)"></span>
            <span class="tour-dot" onclick="goToTourStep(2)"></span>
            <span class="tour-dot" onclick="goToTourStep(3)"></span>
            <span class="tour-dot" onclick="goToTourStep(4)"></span>
        </div>

        <div class="tour-footer">
            <button type="button" id="tourPrevBtn" class="tour-btn-secondary" onclick="prevTourStep()">
                <i class="fa fa-chevron-left"></i> Back
            </button>
            <button type="button" class="tour-btn-secondary" onclick="closeLiveTour()">Skip Tour</button>
            <button type="button" id="tourNextBtn" class="tour-btn-primary" onclick="nextTourStep()">
                Next <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
// Guided Onboarding Tour Steps Configuration
var tourSteps = [
    {
        icon: 'fa-video-camera',
        title: 'Welcome to Live Broadcasts',
        desc: 'Host HD video meetings, webinars, screen sharing, and interactive breakout rooms directly inside your CineWorm workspace.'
    },
    {
        icon: 'fa-sliders',
        title: 'Customize & Create Meetings',
        desc: 'Click "Customize & Create" to set custom topics, room security passwords, default microphone/camera states, screen sharing, and group chat rules.'
    },
    {
        icon: 'fa-share-alt',
        title: 'Share Native Meeting Links',
        desc: 'Share native cineworm.org/meeting/join/... links via 1-click Copy Link or WhatsApp. Guests are protected by subscription & account authentication.'
    },
    {
        icon: 'fa-desktop',
        title: 'In-Call Controls & Fullscreen',
        desc: 'Enjoy full audio/video controls, screen sharing, interactive whiteboard, group chat, and 1-click fullscreen view directly inside your call window.'
    },
    {
        icon: 'fa-play-circle',
        title: 'Ready to Broadcast!',
        desc: 'You are all set! Click "Start Call" or "Customize & Create" to launch your live meeting right away.'
    }
];

var currentTourStep = 0;

function startLiveTour(forceShow) {
    if (!forceShow && localStorage.getItem('cineworm_live_tour_seen') === 'true') {
        return;
    }
    currentTourStep = 0;
    renderTourStep();
    var overlay = document.getElementById('liveTourOverlay');
    if (overlay) {
        overlay.classList.add('active');
    }
}

function renderTourStep() {
    var step = tourSteps[currentTourStep];
    document.getElementById('tourIcon').className = 'fa ' + step.icon;
    document.getElementById('tourTitle').innerText = step.title;
    document.getElementById('tourDescription').innerText = step.desc;

    // Update dots
    var dots = document.querySelectorAll('.tour-dot');
    dots.forEach(function(dot, idx) {
        if (idx === currentTourStep) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });

    // Update Prev button
    var prevBtn = document.getElementById('tourPrevBtn');
    if (currentTourStep === 0) {
        prevBtn.style.opacity = '0.4';
        prevBtn.style.pointerEvents = 'none';
    } else {
        prevBtn.style.opacity = '1';
        prevBtn.style.pointerEvents = 'auto';
    }

    // Update Next button label
    var nextBtn = document.getElementById('tourNextBtn');
    if (currentTourStep === tourSteps.length - 1) {
        nextBtn.innerHTML = 'Get Started <i class="fa fa-check"></i>';
    } else {
        nextBtn.innerHTML = 'Next <i class="fa fa-chevron-right"></i>';
    }
}

function nextTourStep() {
    if (currentTourStep < tourSteps.length - 1) {
        currentTourStep++;
        renderTourStep();
    } else {
        closeLiveTour();
    }
}

function prevTourStep() {
    if (currentTourStep > 0) {
        currentTourStep--;
        renderTourStep();
    }
}

function goToTourStep(index) {
    currentTourStep = index;
    renderTourStep();
}

function closeLiveTour() {
    var overlay = document.getElementById('liveTourOverlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
    localStorage.setItem('cineworm_live_tour_seen', 'true');
}

// Auto-launch tour for first-time visitors
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        startLiveTour(false);
    }, 600);
});

function openNewMeetingModal() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Customize & Create Live Meeting',
            html: `
                <div style="text-align:left; font-size:13px; color:#ccc; margin-top:10px;">
                    <div style="margin-bottom:12px;">
                        <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;">Meeting Topic / Title</label>
                        <input type="text" id="swalMeetingTitle" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:13px; padding:8px 12px; border-radius:4px; width:100%;" value="{{ Auth::user()->name }}'s Live Meeting" placeholder="e.g. Weekly Strategy Sync, Film Review...">
                    </div>

                    <div style="margin-bottom:12px;">
                        <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;"><i class="fa fa-lock" style="color:#ffc107;"></i> Room Password (Optional)</label>
                        <input type="text" id="swalMeetingPassword" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:13px; padding:8px 12px; border-radius:4px; width:100%;" placeholder="Leave empty for open room">
                        <small style="color:#aaa; font-size:11px; margin-top:2px; display:block;">If set, guests must enter this password to join your call.</small>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
                        <div>
                            <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;"><i class="fa fa-microphone text-info"></i> Default Microphone</label>
                            <select id="swalAudio" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:12px;">
                                <option value="1" selected>Microphone Active (ON)</option>
                                <option value="0">Muted on Join (OFF)</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;"><i class="fa fa-video-camera text-info"></i> Default Camera</label>
                            <select id="swalVideo" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:12px;">
                                <option value="1" selected>Camera Active (ON)</option>
                                <option value="0">Camera Disabled (OFF)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                        <div>
                            <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;"><i class="fa fa-desktop text-success"></i> Allow Screen Share</label>
                            <select id="swalScreen" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:12px;">
                                <option value="1" selected>Enabled (Yes)</option>
                                <option value="0">Disabled (No)</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;"><i class="fa fa-comments text-warning"></i> Enable Group Chat</label>
                            <select id="swalChat" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:12px;">
                                <option value="1" selected>Enabled (Yes)</option>
                                <option value="0">Disabled (No)</option>
                            </select>
                        </div>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Create & Start Meeting',
            confirmButtonColor: '#e50914',
            cancelButtonText: 'Cancel',
            cancelButtonColor: '#333',
            background: '#181d27',
            color: '#fff',
            focusConfirm: false,
            preConfirm: () => {
                const title = document.getElementById('swalMeetingTitle').value;
                const password = document.getElementById('swalMeetingPassword').value;
                const audio = document.getElementById('swalAudio').value;
                const video = document.getElementById('swalVideo').value;
                const screen = document.getElementById('swalScreen').value;
                const chat = document.getElementById('swalChat').value;

                if (!title || !title.trim()) {
                    Swal.showValidationMessage('Please enter a meeting topic');
                    return false;
                }
                return { 
                    title: title.trim(), 
                    password: password.trim(),
                    audio: audio,
                    video: video,
                    screen: screen,
                    chat: chat
                };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ URL::to('user/live_broadcasts/create') }}";
                
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = "{{ csrf_token() }}";
                form.appendChild(csrf);

                var params = ['title', 'password', 'audio', 'video', 'screen', 'chat'];
                params.forEach(function(key) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = result.value[key];
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
}

function openEditMeetingModal(id, currentTitle, currentPassword) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Customize Meeting Settings',
            html: `
                <div style="text-align:left; font-size:14px; color:#ccc; margin-top:10px;">
                    <div style="margin-bottom:12px;">
                        <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;">Meeting Topic / Title</label>
                        <input type="text" id="swalEditTitle" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:14px; padding:8px 12px; border-radius:4px; width:100%;" value="${currentTitle}">
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="font-weight:600; margin-bottom:4px; display:block; color:#fff;"><i class="fa fa-lock" style="color:#ffc107;"></i> Room Password</label>
                        <input type="text" id="swalEditPassword" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:14px; padding:8px 12px; border-radius:4px; width:100%;" value="${currentPassword}" placeholder="Leave empty for open room">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Save Customizations',
            confirmButtonColor: '#e50914',
            cancelButtonText: 'Cancel',
            cancelButtonColor: '#333',
            background: '#181d27',
            color: '#fff',
            preConfirm: () => {
                const title = document.getElementById('swalEditTitle').value;
                const password = document.getElementById('swalEditPassword').value;
                if (!title || !title.trim()) {
                    Swal.showValidationMessage('Please enter a meeting topic');
                    return false;
                }
                return { title: title.trim(), password: password.trim() };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ URL::to('user/live_broadcasts/update') }}/" + id;
                
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = "{{ csrf_token() }}";
                form.appendChild(csrf);

                var titleInput = document.createElement('input');
                titleInput.type = 'hidden';
                titleInput.name = 'title';
                titleInput.value = result.value.title;
                form.appendChild(titleInput);

                var passInput = document.createElement('input');
                passInput.type = 'hidden';
                passInput.name = 'password';
                passInput.value = result.value.password;
                form.appendChild(passInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
}

function copyInviteLink(url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(showCopyToast);
    } else {
        var input = document.getElementById('shareUrlInput');
        if (input) {
            input.select();
            document.execCommand('copy');
        }
        showCopyToast();
    }
}

function showCopyToast() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Invite Link Copied!',
            text: 'The meeting link has been copied to your clipboard.',
            timer: 2200,
            showConfirmButton: false,
            confirmButtonColor: '#e50914',
            background: '#181d27',
            color: '#fff'
        });
    }
}

function leaveCallConfirm(url) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Leave Video Call?',
            text: 'Are you sure you want to exit this meeting?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e50914',
            cancelButtonColor: '#333',
            confirmButtonText: 'Yes, Leave Meeting',
            cancelButtonText: 'Stay in Call',
            background: '#181d27',
            color: '#fff'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    }
    return true;
}

function toggleIframeFullscreen() {
    var wrapper = document.getElementById('meetingFrameWrapper');
    if (wrapper) {
        if (!document.fullscreenElement) {
            if (wrapper.requestFullscreen) {
                wrapper.requestFullscreen();
            } else if (wrapper.webkitRequestFullscreen) {
                wrapper.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
}
</script>
@endsection
