@extends('site_app')

@section('head_title', 'Live Broadcast & Meetings | '.getcong('site_name'))
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
        padding: 10px 16px;
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

    .share-link-input {
        background: rgba(0, 0, 0, 0.4) !important;
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        font-size: 13px;
        font-family: monospace;
    }

    .lobby-hero-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 30px 20px;
        margin-bottom: 25px;
        text-align: center;
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
                
                {{-- Standard User Sidebar --}}
                @include('pages.user._sidebar')
                
                {{-- Main Content Workspace Column --}}
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    <div class="edit-profile-form">

                        @if(session('flash_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background:#065f46; color:#a7f3d0; border:1px solid #047857; margin-bottom:20px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color:#a7f3d0;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{ session('flash_message') }}
                            </div>
                        @endif

                        @if($inCall)
                            {{-- ACTIVE CALL WORKSPACE --}}
                            <div class="cineworm-meeting-box">
                                
                                {{-- Toolbar --}}
                                <div class="cineworm-toolbar">
                                    <div>
                                        <h4 style="color:#fff; margin:0; font-size:16px; font-weight:700;">
                                            <i class="fa fa-video-camera" style="color:#e50914; margin-right:6px;"></i> {{ $meetingTitle }}
                                        </h4>
                                        <p style="color:#ccc; margin:2px 0 0; font-size:12px;">Room ID: <code style="color:#fe0278;">{{ $roomId }}</code></p>
                                    </div>

                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                        {{-- Leave Call --}}
                                        <a href="{{ URL::to('user/live_broadcasts') }}" class="btn btn-sm btn-danger" onclick="return leaveCallConfirm(this.href);" style="background:#e50914; border:none; padding:6px 14px; font-size:12px; font-weight:600;">
                                            <i class="fa fa-phone"></i> Leave Call
                                        </a>

                                        {{-- Copy Link --}}
                                        <button type="button" class="btn btn-sm btn-success" onclick="copyInviteLink('{{ $shareableJoinUrl }}')" style="background:#28a745; border:none; padding:6px 14px; font-size:12px; font-weight:600;">
                                            <i class="fa fa-copy"></i> Copy Link
                                        </button>

                                        {{-- Share WhatsApp --}}
                                        <a href="https://api.whatsapp.com/send?text={{ urlencode('Join my live meeting on CineWorm: ' . $shareableJoinUrl) }}" target="_blank" class="btn btn-sm btn-success" style="background:#25D366; border:none; padding:6px 14px; font-size:12px; font-weight:600; color:#fff !important;">
                                            <i class="fa fa-whatsapp"></i> WhatsApp
                                        </a>

                                        {{-- Fullscreen --}}
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleIframeFullscreen()" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; padding:6px 14px; font-size:12px;">
                                            <i class="fa fa-expand"></i> Fullscreen
                                        </button>
                                    </div>
                                </div>

                                {{-- Guest Link Bar --}}
                                <div class="input-group" style="margin-bottom: 12px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background:rgba(0,0,0,0.5); border-color:rgba(255,255,255,0.1); color:#ccc; font-size:12px;">Guest Share Link</span>
                                    </div>
                                    <input type="text" id="shareUrlInput" class="form-control share-link-input" value="{{ $shareableJoinUrl }}" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" style="background:rgba(255,255,255,0.1); color:#fff; border-color:rgba(255,255,255,0.1); font-size:12px;" onclick="copyInviteLink('{{ $shareableJoinUrl }}')">
                                            <i class="fa fa-clone"></i> Copy
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
                        @else
                            {{-- DASHBOARD LOBBY VIEW --}}
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-md-7">
                                    <h3 style="color:#fff; margin-bottom:5px;"><i class="fa fa-video-camera" style="color:#e50914; margin-right:8px;"></i> Live Broadcast & Meetings</h3>
                                    <p style="color:#ccc; font-size:14px;">Host HD video calls, webinars, and screen sharing sessions directly inside CineWorm.</p>
                                </div>
                                <div class="col-md-5 text-right" style="text-align: right; padding-top: 10px;">
                                    <button type="button" onclick="openNewMeetingModal();" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none; border:none; cursor:pointer; margin-right:5px;">
                                        <i class="fa fa-plus"></i> Create Meeting
                                    </button>
                                    <a href="{{ URL::to('user/live_broadcasts?room=' . $roomId) }}" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none; background-color:#28a745;">
                                        <i class="fa fa-play"></i> Start Call
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- HISTORY TABLE --}}
                        <div class="row" style="margin-top:25px; margin-bottom:10px;">
                            <div class="col-12">
                                <h4 style="color:#fff; font-size:16px; font-weight:700; margin-bottom:15px;">
                                    <i class="fa fa-history" style="color:#e50914; margin-right:8px;"></i> My Created Meetings History
                                </h4>

                                <div class="table-responsive">
                                    <table class="table table-bordered" style="color: #fff; border-color: rgba(255,255,255,0.1);">
                                        <thead>
                                            <tr>
                                                <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Meeting Title</th>
                                                <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Room ID</th>
                                                <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Created Date</th>
                                                <th style="border-color: rgba(255,255,255,0.1); color:#fff;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($live_broadcasts as $broadcast)
                                                <tr>
                                                    <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">{{ $broadcast->title }}</td>
                                                    <td style="border-color: rgba(255,255,255,0.1); color:#fe0278; font-family:monospace;">{{ $broadcast->zoom_meeting_id }}</td>
                                                    <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">{{ $broadcast->created_at ? $broadcast->created_at->format('M d, Y H:i') : '—' }}</td>
                                                    <td style="border-color: rgba(255,255,255,0.1);" class="text-center">
                                                        <a href="{{ URL::to('user/live_broadcasts?room=' . $broadcast->zoom_meeting_id) }}" class="btn btn-sm btn-success" style="background:#e50914; border:none; padding:4px 10px; font-size:12px; margin-right:4px;">
                                                            <i class="fa fa-video-camera"></i> Start Call
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-secondary" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; padding:4px 10px; font-size:12px;" onclick="copyInviteLink('{{ $cinemeetBaseUrl }}/join?room={{ $broadcast->zoom_meeting_id }}')">
                                                            <i class="fa fa-copy"></i> Copy Link
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center" style="border-color: rgba(255,255,255,0.1); padding: 30px; color:#aaa;">
                                                        <i class="fa fa-video-camera" style="font-size:32px; display:block; margin-bottom:14px; opacity:0.3;"></i>
                                                        No live broadcast meetings scheduled yet. Click <strong>"Create Meeting"</strong> above to get started!
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div style="margin-top:15px;">
                                    @include('_particles.pagination', ['paginator' => $live_broadcasts])
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openNewMeetingModal() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Create Instant Live Meeting',
            html: `
                <div style="text-align:left; font-size:13px; color:#ccc; margin-top:10px;">
                    <label style="font-weight:600; margin-bottom:6px; display:block; color:#fff;">Meeting Topic / Title</label>
                    <input type="text" id="swalMeetingTitle" class="form-control" style="background:rgba(0,0,0,0.5); color:#fff; border:1px solid rgba(255,255,255,0.2); font-size:13px; padding:8px 12px; border-radius:4px; width:100%;" value="{{ Auth::user()->name }}'s Live Meeting" placeholder="e.g. Weekly Strategy Sync, Film Review...">
                    <small style="color:#aaa; font-size:11px; margin-top:4px; display:block;">Guests will see this topic when joining your meeting link.</small>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Start Meeting Now',
            confirmButtonColor: '#e50914',
            cancelButtonText: 'Cancel',
            cancelButtonColor: '#333',
            background: '#181d27',
            color: '#fff',
            focusConfirm: false,
            preConfirm: () => {
                const title = document.getElementById('swalMeetingTitle').value;
                if (!title || !title.trim()) {
                    Swal.showValidationMessage('Please enter a meeting topic');
                    return false;
                }
                return title.trim();
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

                var titleInput = document.createElement('input');
                titleInput.type = 'hidden';
                titleInput.name = 'title';
                titleInput.value = result.value;
                form.appendChild(titleInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    } else {
        var topic = prompt('Enter Meeting Topic / Title:', "{{ Auth::user()->name }}'s Live Meeting");
        if (topic) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ URL::to('user/live_broadcasts/create') }}";
            
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);

            var titleInput = document.createElement('input');
            titleInput.type = 'hidden';
            titleInput.name = 'title';
            titleInput.value = topic;
            form.appendChild(titleInput);

            document.body.appendChild(form);
            form.submit();
        }
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
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Invite link copied to clipboard!',
            showConfirmButton: false,
            timer: 2500,
            background: '#181d27',
            color: '#fff'
        });
    } else {
        var toast = document.createElement('div');
        toast.style.cssText = 'position:fixed; top:20px; right:20px; background:#e50914; color:#fff; padding:12px 20px; border-radius:4px; z-index:999999; font-size:14px; font-weight:600; box-shadow:0 10px 25px rgba(0,0,0,0.5);';
        toast.innerHTML = '<i class="fa fa-check-circle"></i> Invite link copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(function() { toast.remove(); }, 2500);
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
