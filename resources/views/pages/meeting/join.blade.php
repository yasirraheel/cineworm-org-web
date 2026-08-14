@extends('site_app')

@section('head_title', $meetingTitle . ' | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<style>
    header, .header-area, .navbar, .user-dropdown, .dropdown-menu {
        z-index: 99999 !important;
    }

    .cineworm-join-container {
        padding: 30px 15px;
        background: #0d0f12;
        min-height: 85vh;
    }

    .cineworm-join-box {
        background: rgba(20, 24, 33, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }

    .cineworm-join-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 14px 20px;
        margin-bottom: 18px;
    }

    .join-iframe-wrapper {
        position: relative;
        width: 100%;
        height: calc(88vh - 160px);
        min-height: 520px;
        max-height: 750px;
        border-radius: 8px;
        overflow: hidden;
        background: #000;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    iframe.cinemeet-join-frame {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }
</style>

<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Live Meeting Room</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li>Join Meeting</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="cineworm-join-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-lg-12">

                @include('pages.user.whatsapp._flash')

                <div class="cineworm-join-box">
                    
                    {{-- Meeting Header Bar --}}
                    <div class="cineworm-join-header">
                        <div>
                            <h3 style="color:#fff; margin:0; font-size:18px; font-weight:700;">
                                <i class="fa fa-video-camera" style="color:#e50914; margin-right:8px;"></i> {{ $meetingTitle }}
                            </h3>
                            <p style="color:#ccc; margin:4px 0 0; font-size:13px;">
                                Room ID: <code style="color:#fe0278; font-weight:600;">{{ $roomId }}</code>
                                @if($hostUser)
                                    <span style="margin-left:12px; color:#aaa;"><i class="fa fa-user"></i> Host: <strong style="color:#fff;">{{ $hostUser->name }}</strong></span>
                                @endif
                            </p>
                        </div>

                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            {{-- Copy Invite Link --}}
                            <button type="button" class="btn btn-sm btn-success" onclick="copyInviteLink('{{ $shareableJoinUrl }}')" style="background:#28a745; border:none; padding:7px 16px; font-size:13px; font-weight:600;">
                                <i class="fa fa-copy"></i> Copy Link
                            </button>

                            {{-- Share WhatsApp --}}
                            <a href="https://api.whatsapp.com/send?text={{ urlencode('Join live meeting on CineWorm: ' . $shareableJoinUrl) }}" target="_blank" class="btn btn-sm btn-success" style="background:#25D366; border:none; padding:7px 16px; font-size:13px; font-weight:600; color:#fff !important;">
                                <i class="fa-brands fa-whatsapp"></i> WhatsApp
                            </a>

                            {{-- Fullscreen --}}
                            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleIframeFullscreen()" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; padding:7px 16px; font-size:13px;">
                                <i class="fa fa-expand"></i> Fullscreen
                            </button>
                        </div>
                    </div>

                    {{-- Guest Share Link Row --}}
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; width: 100%; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; overflow: hidden; background: rgba(0,0,0,0.4);">
                            <span style="background:rgba(255,255,255,0.05); color:#ccc; padding:10px 14px; font-size:12px; font-weight:600; border-right:1px solid rgba(255,255,255,0.1); white-space:nowrap;">
                                Meeting Share Link
                            </span>
                            <input type="text" id="shareUrlInput" value="{{ $shareableJoinUrl }}" readonly style="flex: 1; background: transparent; border: none; color: #fff; padding: 10px 14px; font-size: 13px; font-family: monospace; outline: none;">
                            <button type="button" onclick="copyInviteLink('{{ $shareableJoinUrl }}')" style="background: #e50914; color: #fff; border: none; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                                <i class="fa fa-copy"></i> Copy Link
                            </button>
                        </div>
                    </div>

                    {{-- Embedded Call View --}}
                    <div class="join-iframe-wrapper" id="joinFrameWrapper">
                        <iframe id="cinemeetJoinFrame"
                            class="cinemeet-join-frame"
                            src="{{ $cinemeetEmbedUrl }}"
                            allow="camera; microphone; display-capture; autoplay; clipboard-write; fullscreen"
                            title="CineWorm Live Video Meeting">
                        </iframe>
                    </div>

                </div>
            </div>
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

function toggleIframeFullscreen() {
    var wrapper = document.getElementById('joinFrameWrapper');
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
