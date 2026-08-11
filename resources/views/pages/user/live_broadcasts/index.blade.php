@extends('site_app')

@section('head_title', 'Live Broadcast & Meetings | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<style>
    header, .header-area, .navbar, .user-dropdown, .dropdown-menu {
        z-index: 99999 !important;
    }
    
    .breadcrumb-section {
        padding: 15px 0 !important;
        margin-bottom: 15px !important;
    }
    .breadcrumb-section h2 {
        font-size: 20px !important;
        margin-bottom: 0 !important;
    }
    .breadcrumb-section #breadcrumbs {
        display: none !important;
    }

    .meeting-workspace-card {
        background: #141820;
        border: 1px solid #2a3446;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        margin-bottom: 16px;
        z-index: 1;
        position: relative;
    }
    .meeting-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        background: #0b0e14;
        border: 1px solid #1e2636;
        border-radius: 8px;
        padding: 8px 14px;
        margin-bottom: 10px;
    }
    .meeting-title-box h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
    }
    .meeting-title-box p {
        margin: 1px 0 0;
        font-size: 11px;
        color: #94a3b8;
    }
    .btn-zoom-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none !important;
        white-space: nowrap;
    }
    .btn-zoom-primary { background: #2563eb; color: #fff !important; }
    .btn-zoom-primary:hover { background: #1d4ed8; color: #fff !important; }
    .btn-zoom-success { background: #059669; color: #fff !important; }
    .btn-zoom-success:hover { background: #047857; color: #fff !important; }
    .btn-zoom-danger { background: #dc2626; color: #fff !important; }
    .btn-zoom-danger:hover { background: #b91c1c; color: #fff !important; }
    .btn-zoom-dark { background: #334155; color: #f8fafc !important; }
    .btn-zoom-dark:hover { background: #475569; color: #f8fafc !important; }

    /* Viewport Height Frame */
    .iframe-wrapper {
        position: relative;
        width: 100%;
        height: calc(88vh - 170px);
        min-height: 480px;
        max-height: 680px;
        border-radius: 8px;
        overflow: hidden;
        background: #000;
        border: 1px solid #2a3446;
        z-index: 1;
    }
    iframe.cinemeet-frame {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

    .share-link-input {
        background: #0b0e14 !important;
        color: #60a5fa !important;
        border: 1px solid #1e2636 !important;
        font-size: 12px;
        font-family: monospace;
        height: 32px;
    }

    /* Modal Styling */
    .modal-content-dark {
        background: #141820;
        color: #fff;
        border: 1px solid #2a3446;
        border-radius: 12px;
    }
    .modal-header-dark {
        border-bottom: 1px solid #2a3446;
        padding: 12px 16px;
    }

    .sidebar-collapsed { display: none !important; }
    .main-workspace-full { flex: 0 0 100% !important; max-width: 100% !important; }

    .lobby-hero-card {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        border: 1px solid #334155;
        border-radius: 12px;
        padding: 28px 24px;
        margin-bottom: 20px;
        text-align: center;
    }
    .lobby-hero-card h3 {
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
    }
    .lobby-hero-card p {
        color: #94a3b8;
        font-size: 14px;
        max-width: 600px;
        margin: 0 auto 20px auto;
    }
</style>

<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Live Broadcast & Meetings</h2>
            </div>
        </div>
    </div>
</div>

<div class="edit-profile-area" style="padding: 10px 0 30px 0;">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                
                {{-- Collapsible Sidebar --}}
                <div class="col-lg-3 col-md-4 col-sm-12" id="profileSidebarCol">
                    @include('pages.user._sidebar')
                </div>
                
                {{-- Main Workspace Column --}}
                <div class="col-lg-9 col-md-8 col-sm-12" id="mainWorkspaceCol">

                    @if(session('flash_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background:#065f46; color:#a7f3d0; border:1px solid #047857; padding:8px 14px; font-size:13px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color:#a7f3d0;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ session('flash_message') }}
                        </div>
                    @endif

                    @if($inCall)
                        {{-- ACTIVE CALL WORKSPACE VIEW --}}
                        <div class="meeting-workspace-card">
                            
                            {{-- Zoom-Style Toolbar --}}
                            <div class="meeting-toolbar">
                                <div class="meeting-title-box">
                                    <h4><i class="fa fa-video-camera" style="color:#60a5fa; margin-right:4px;"></i> {{ $meetingTitle }}</h4>
                                    <p>Room: <code style="color:#60a5fa;">{{ $roomId }}</code></p>
                                </div>

                                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                    {{-- Leave Call Button --}}
                                    <a href="{{ URL::to('user/live_broadcasts') }}" class="btn-zoom-action btn-zoom-danger" onclick="return confirm('Leave this video call?')">
                                        <i class="fa fa-phone"></i> Leave Call
                                    </a>

                                    {{-- Sidebar Toggle Button --}}
                                    <button type="button" class="btn-zoom-action btn-zoom-dark" onclick="toggleProfileSidebar()" title="Toggle Sidebar">
                                        <i class="fa fa-columns"></i> <span id="sidebarToggleText">Expand Call</span>
                                    </button>

                                    {{-- Copy Link Button --}}
                                    <button type="button" class="btn-zoom-action btn-zoom-success" onclick="copyInviteLink('{{ $shareableJoinUrl }}')">
                                        <i class="fa fa-copy"></i> Copy Link
                                    </button>

                                    {{-- Share WhatsApp --}}
                                    <a href="https://api.whatsapp.com/send?text={{ urlencode('Join my live meeting on CineWorm: ' . $shareableJoinUrl) }}" target="_blank" class="btn-zoom-action btn-zoom-dark" style="background:#25D366; color:#fff !important;">
                                        <i class="fa fa-whatsapp"></i> WhatsApp
                                    </a>

                                    {{-- Fullscreen Toggle --}}
                                    <button type="button" class="btn-zoom-action btn-zoom-dark" onclick="toggleIframeFullscreen()">
                                        <i class="fa fa-expand"></i> Fullscreen
                                    </button>
                                </div>
                            </div>

                            {{-- Share Link Bar --}}
                            <div class="row" style="margin-bottom: 8px;">
                                <div class="col-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="background:#0b0e14; border-color:#1e2636; color:#94a3b8; font-size:11px; height:32px; padding:0 8px;">Guest Share Link</span>
                                        </div>
                                        <input type="text" id="shareUrlInput" class="form-control share-link-input" value="{{ $shareableJoinUrl }}" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" style="background:#1e2636; color:#fff; border-color:#1e2636; height:32px; font-size:11px; padding:0 10px;" onclick="copyInviteLink('{{ $shareableJoinUrl }}')">
                                                <i class="fa fa-clone"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Embedded CineMeet Call Workspace --}}
                            <div class="iframe-wrapper" id="meetingFrameWrapper">
                                <iframe id="cinemeetFrame"
                                    class="cinemeet-frame"
                                    src="{{ $cinemeetEmbedUrl }}"
                                    allow="camera; microphone; display-capture; autoplay; clipboard-write; fullscreen"
                                    title="CineMeet Video Meeting">
                                </iframe>
                            </div>

                        </div>
                    @else
                        {{-- DASHBOARD LOBBY VIEW --}}
                        <div class="lobby-hero-card">
                            <h3><i class="fa fa-video-camera" style="color:#60a5fa; margin-right:8px;"></i> Live Broadcast & Meetings</h3>
                            <p>Host HD video calls, webinars, and screen sharing sessions directly inside CineWorm. Invite colleagues or subscribers with a 1-click shareable link.</p>

                            <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-top:16px;">
                                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#newMeetingModal" style="background:#2563eb; border:none; font-weight:600; padding:10px 24px; border-radius:8px;">
                                    <i class="fa fa-plus-circle"></i> Create New Meeting
                                </button>
                                <a href="{{ URL::to('user/live_broadcasts?room=' . $roomId) }}" class="btn btn-success btn-lg" style="background:#059669; border:none; font-weight:600; padding:10px 24px; border-radius:8px;">
                                    <i class="fa fa-play-circle"></i> Start Quick Meeting
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Meeting History Table --}}
                    <div class="meeting-workspace-card">
                        <h4 style="color:#fff; font-size:15px; font-weight:700; margin-bottom:12px;">
                            <i class="fa fa-history" style="color:#c084fc; margin-right:6px;"></i> My Created Meetings History
                        </h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" style="color: #fff; border-color: rgba(255,255,255,0.08); font-size:12px;">
                                <thead>
                                    <tr style="background:#0b0e14;">
                                        <th style="border-color: rgba(255,255,255,0.08); color:#94a3b8;">Meeting Title</th>
                                        <th style="border-color: rgba(255,255,255,0.08); color:#94a3b8;">Room ID</th>
                                        <th style="border-color: rgba(255,255,255,0.08); color:#94a3b8;">Created Date</th>
                                        <th style="border-color: rgba(255,255,255,0.08); color:#94a3b8;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($live_broadcasts as $broadcast)
                                        <tr>
                                            <td style="border-color: rgba(255,255,255,0.08); color:#f1f5f9; font-weight:600;">
                                                {{ $broadcast->title }}
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.08); color:#60a5fa; font-family:monospace;">
                                                {{ $broadcast->zoom_meeting_id }}
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.08); color:#94a3b8;">
                                                {{ $broadcast->created_at ? $broadcast->created_at->format('M d, Y H:i') : '—' }}
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.08);" class="text-center">
                                                <a href="{{ URL::to('user/live_broadcasts?room=' . $broadcast->zoom_meeting_id) }}" class="btn btn-sm btn-primary waves-effect" style="font-size:11px; padding:3px 10px; background:#2563eb;">
                                                    <i class="fa fa-video-camera"></i> Start Meeting
                                                </a>
                                                <button type="button" class="btn btn-sm btn-dark waves-effect" style="font-size:11px; padding:3px 10px; background:#334155; color:#fff;" onclick="copyInviteLink('{{ $cinemeetBaseUrl }}/join?room={{ $broadcast->zoom_meeting_id }}')">
                                                    <i class="fa fa-copy"></i> Copy Link
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center" style="border-color: rgba(255,255,255,0.08); padding: 20px; color:#64748b;">
                                                <i class="fa fa-video-camera" style="font-size:24px; display:block; margin-bottom:8px; opacity:0.3;"></i>
                                                No meeting rooms created yet. Click <strong>"Create New Meeting"</strong> above to start your first live call!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:10px;">
                            @include('_particles.pagination', ['paginator' => $live_broadcasts])
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- New Meeting Modal --}}
<div class="modal fade" id="newMeetingModal" tabindex="-1" role="dialog" aria-labelledby="newMeetingModalLabel" aria-hidden="true" style="z-index:999999 !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-dark">
            <div class="modal-header modal-header-dark">
                <h5 class="modal-title" id="newMeetingModalLabel" style="color:#fff; font-weight:700; font-size:15px;">
                    <i class="fa fa-video-camera text-primary"></i> Create Instant Live Meeting
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ URL::to('user/live_broadcasts/create') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body" style="padding:16px;">
                    <div class="form-group">
                        <label style="color:#cbd5e1; font-weight:600; font-size:12px;">Meeting Topic / Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Weekly Strategy Sync, Film Review..." style="background:#0b0e14; color:#fff; border:1px solid #1e2636; font-size:13px;" value="{{ Auth::user()->name }}'s Live Meeting">
                        <small style="color:#94a3b8; font-size:11px;">Guests will see this topic when joining your meeting link.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #2a3446; padding:10px 16px;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" style="background:#2563eb; border:none; font-weight:600;">
                        <i class="fa fa-play-circle"></i> Start Meeting Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
    alert('Invite link copied to clipboard!');
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

function toggleProfileSidebar() {
    var sidebar = document.getElementById('profileSidebarCol');
    var mainCol = document.getElementById('mainWorkspaceCol');
    var btnText = document.getElementById('sidebarToggleText');
    
    if (sidebar && mainCol) {
        if (sidebar.classList.contains('sidebar-collapsed')) {
            sidebar.classList.remove('sidebar-collapsed');
            mainCol.classList.remove('main-workspace-full');
            if (btnText) btnText.textContent = 'Expand Call';
        } else {
            sidebar.classList.add('sidebar-collapsed');
            mainCol.classList.add('main-workspace-full');
            if (btnText) btnText.textContent = 'Show Profile';
        }
    }
}
</script>
@endsection
