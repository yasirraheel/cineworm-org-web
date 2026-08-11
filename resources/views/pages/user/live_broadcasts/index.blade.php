@extends('site_app')

@section('head_title', 'Live Broadcast & Meetings | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<style>
    .meeting-workspace-card {
        background: #181d27;
        border: 1px solid #2d3748;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        margin-bottom: 24px;
    }
    .meeting-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        background: #0f131a;
        border: 1px solid #252e3e;
        border-radius: 10px;
        padding: 14px 20px;
        margin-bottom: 16px;
    }
    .meeting-title-box h4 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #fff;
    }
    .meeting-title-box p {
        margin: 2px 0 0;
        font-size: 12px;
        color: #94a3b8;
    }
    .btn-zoom-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none !important;
    }
    .btn-zoom-primary { background: #2563eb; color: #fff !important; }
    .btn-zoom-primary:hover { background: #1d4ed8; color: #fff !important; }
    .btn-zoom-success { background: #059669; color: #fff !important; }
    .btn-zoom-success:hover { background: #047857; color: #fff !important; }
    .btn-zoom-dark { background: #334155; color: #f8fafc !important; }
    .btn-zoom-dark:hover { background: #475569; color: #f8fafc !important; }

    .iframe-wrapper {
        position: relative;
        width: 100%;
        height: 75vh;
        min-height: 520px;
        border-radius: 10px;
        overflow: hidden;
        background: #000;
        border: 1px solid #2d3748;
    }
    iframe.cinemeet-frame {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

    .share-link-input {
        background: #0f131a !important;
        color: #60a5fa !important;
        border: 1px solid #252e3e !important;
        font-size: 13px;
        font-family: monospace;
    }

    /* Modal Styling */
    .modal-content-dark {
        background: #181d27;
        color: #fff;
        border: 1px solid #2d3748;
        border-radius: 12px;
    }
    .modal-header-dark {
        border-bottom: 1px solid #2d3748;
    }
</style>

<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Live Broadcast & Meetings</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li>Live Broadcasts</li>
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
                @include('pages.user._sidebar')
                
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">

                    @if(session('flash_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background:#065f46; color:#a7f3d0; border:1px solid #047857;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color:#a7f3d0;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ session('flash_message') }}
                        </div>
                    @endif

                    {{-- Main Workspace Card --}}
                    <div class="meeting-workspace-card">
                        
                        {{-- Zoom-Style Toolbar --}}
                        <div class="meeting-toolbar">
                            <div class="meeting-title-box">
                                <h4><i class="fa fa-video-camera" style="color:#60a5fa; margin-right:6px;"></i> {{ $meetingTitle }}</h4>
                                <p>Room ID: <code>{{ $roomId }}</code></p>
                            </div>

                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                {{-- Copy Link Button --}}
                                <button type="button" class="btn-zoom-action btn-zoom-success" onclick="copyInviteLink('{{ $shareableJoinUrl }}')">
                                    <i class="fa fa-copy"></i> Copy Invite Link
                                </button>

                                {{-- Share WhatsApp --}}
                                <a href="https://api.whatsapp.com/send?text={{ urlencode('Join my live meeting on CineWorm: ' . $shareableJoinUrl) }}" target="_blank" class="btn-zoom-action btn-zoom-dark" style="background:#25D366; color:#fff !important;">
                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                </a>

                                {{-- Instant New Meeting Modal Trigger --}}
                                <button type="button" class="btn-zoom-action btn-zoom-primary" data-toggle="modal" data-target="#newMeetingModal">
                                    <i class="fa fa-plus-circle"></i> New Meeting
                                </button>

                                {{-- Fullscreen Toggle --}}
                                <button type="button" class="btn-zoom-action btn-zoom-dark" onclick="toggleIframeFullscreen()">
                                    <i class="fa fa-expand"></i> Fullscreen
                                </button>
                            </div>
                        </div>

                        {{-- Share Link Bar --}}
                        <div class="row m-b-15" style="margin-bottom: 16px;">
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background:#0f131a; border-color:#252e3e; color:#94a3b8; font-size:12px;">Guest Share Link</span>
                                    </div>
                                    <input type="text" id="shareUrlInput" class="form-control share-link-input" value="{{ $shareableJoinUrl }}" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" style="background:#252e3e; color:#fff; border-color:#252e3e;" onclick="copyInviteLink('{{ $shareableJoinUrl }}')">
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

                    {{-- Meeting History Table --}}
                    <div class="meeting-workspace-card" style="margin-top:24px;">
                        <h4 style="color:#fff; font-size:16px; font-weight:700; margin-bottom:16px;">
                            <i class="fa fa-history" style="color:#c084fc; margin-right:6px;"></i> My Created Meetings History
                        </h4>

                        <div class="table-responsive">
                            <table class="table table-bordered" style="color: #fff; border-color: rgba(255,255,255,0.08);">
                                <thead>
                                    <tr style="background:#0f131a;">
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
                                            <td style="border-color: rgba(255,255,255,0.08); color:#94a3b8; font-size:12px;">
                                                {{ $broadcast->created_at ? $broadcast->created_at->format('M d, Y H:i') : '—' }}
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.08);" class="text-center">
                                                <a href="{{ URL::to('user/live_broadcasts?room=' . $broadcast->zoom_meeting_id) }}" class="btn btn-sm btn-primary waves-effect" style="font-size:11px; padding:4px 10px;">
                                                    <i class="fa fa-sign-in"></i> Open Call
                                                </a>
                                                <button type="button" class="btn btn-sm btn-dark waves-effect" style="font-size:11px; padding:4px 10px; background:#334155; color:#fff;" onclick="copyInviteLink('{{ $cinemeetBaseUrl }}/join?room={{ $broadcast->zoom_meeting_id }}')">
                                                    <i class="fa fa-copy"></i> Link
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center" style="border-color: rgba(255,255,255,0.08); padding: 30px; color:#64748b;">
                                                <i class="fa fa-video-camera" style="font-size:28px; display:block; margin-bottom:10px; opacity:0.3;"></i>
                                                No meeting rooms created yet. Click <strong>"New Meeting"</strong> above to start your first live call!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:16px;">
                            @include('_particles.pagination', ['paginator' => $live_broadcasts])
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- New Meeting Modal --}}
<div class="modal fade" id="newMeetingModal" tabindex="-1" role="dialog" aria-labelledby="newMeetingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-dark">
            <div class="modal-header modal-header-dark">
                <h5 class="modal-title" id="newMeetingModalLabel" style="color:#fff; font-weight:700;">
                    <i class="fa fa-video-camera text-primary"></i> Create Instant Live Meeting
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ URL::to('user/live_broadcasts/create') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label style="color:#cbd5e1; font-weight:600; font-size:13px;">Meeting Topic / Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Weekly Strategy Sync, Film Review..." style="background:#0f131a; color:#fff; border:1px solid #252e3e;" value="{{ Auth::user()->name }}'s Live Meeting">
                        <small style="color:#94a3b8; font-size:11px;">Guests will see this topic when joining your meeting link.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #2d3748;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#2563eb; border:none; font-weight:600;">
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
        input.select();
        document.execCommand('copy');
        showCopyToast();
    }
}

function showCopyToast() {
    alert('Invite link copied to clipboard!');
}

function toggleIframeFullscreen() {
    var wrapper = document.getElementById('meetingFrameWrapper');
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
</script>
@endsection
