@extends('admin.admin_app')

@section('content')
<style>
    .cm-page-header { background: linear-gradient(135deg, #1a1a2e, #0f3460); color:#fff; border-radius:10px; padding:20px 24px; margin-bottom:24px; }
    .cm-page-header h3 { margin:0; font-size:18px; font-weight:700; }
    .cm-page-header p { margin:4px 0 0; color:rgba(255,255,255,.6); font-size:12px; }
    .cm-breadcrumb { font-size:12px; color:rgba(255,255,255,.5); margin-bottom:6px; }
    .cm-breadcrumb a { color:rgba(255,255,255,.7); text-decoration:none; }
    .form-section { background:#fff; border-radius:10px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:20px; }
    .form-section h5 { font-size:14px; font-weight:700; color:#2c3e50; margin-bottom:18px; padding-bottom:10px; border-bottom:2px solid #f0f0f0; }
    .btn-save { background:#0f3460; color:#fff; border:none; padding:10px 28px; border-radius:8px; font-size:13px; font-weight:600; }
    .btn-save:hover { background:#0d2c55; color:#fff; }

    /* Toggle Switch Styles */
    .toggle-row { display:flex; align-items:center; justify-content:space-between; padding:14px 0; border-bottom:1px solid #f5f5f5; }
    .toggle-row:last-child { border-bottom:none; }
    .toggle-info { flex:1; }
    .toggle-info .toggle-label { font-size:13px; font-weight:600; color:#2c3e50; margin-bottom:2px; }
    .toggle-info .toggle-desc { font-size:11px; color:#aaa; }
    .switch { position:relative; display:inline-block; width:50px; height:26px; margin-left:16px; flex-shrink:0; }
    .switch input { opacity:0; width:0; height:0; }
    .slider-sw { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.3s; border-radius:26px; }
    .slider-sw:before { position:absolute; content:""; height:20px; width:20px; left:3px; bottom:3px; background:white; transition:.3s; border-radius:50%; }
    input:checked + .slider-sw { background:#27ae60; }
    input:checked + .slider-sw:before { transform:translateX(24px); }
    .toggle-on-badge  { display:none; font-size:10px; font-weight:700; color:#27ae60; margin-left:8px; }
    .toggle-off-badge { display:none; font-size:10px; font-weight:700; color:#e74c3c; margin-left:8px; }
    input:checked ~ .toggle-on-badge  { display:inline; }
    input:not(:checked) ~ .toggle-off-badge { display:inline; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            @if(session('flash_message'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('flash_message') }}
                </div>
            @endif
            @if(session('flash_error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('flash_error') }}
                </div>
            @endif

            <div class="cm-page-header">
                <div class="cm-breadcrumb">
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Section Visibility
                </div>
                <h3>👁 Section Visibility</h3>
                <p>Toggle which sections appear on the CineMeet homepage. Changes require a restart.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/visibility') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-section">
                    <h5><i class="fa fa-star"></i> Sponsors & Advertisers</h5>

                    @php
                        $isTrue = fn($key) => isset($settings[$key]) && strtolower($settings[$key]) === 'true';
                    @endphp

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Top Sponsors</div>
                            <div class="toggle-desc">The "This project is proudly sponsored by" section at the top.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_TOP_SPONSORS" {{ $isTrue('SHOW_TOP_SPONSORS') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Current Sponsors</div>
                            <div class="toggle-desc">Active sponsors section with logos.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_SPONSORS" {{ $isTrue('SHOW_SPONSORS') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Past Sponsors</div>
                            <div class="toggle-desc">Previous sponsors section.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_PAST_SPONSORS" {{ $isTrue('SHOW_PAST_SPONSORS') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Our Advertisers</div>
                            <div class="toggle-desc">Advertisers section with partner logos.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_ADVERTISERS" {{ $isTrue('SHOW_ADVERTISERS') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fa fa-th-large"></i> Page Sections</h5>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Features Section</div>
                            <div class="toggle-desc">Feature cards (Screen Sharing, WebCam, etc.).</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_FEATURES" {{ $isTrue('SHOW_FEATURES') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Teams Section</div>
                            <div class="toggle-desc">Team members / contributors section.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_TEAMS" {{ $isTrue('SHOW_TEAMS') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Try Other Apps Section</div>
                            <div class="toggle-desc">MiroTalk alternatives / related products section.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_TRY_EASIER" {{ $isTrue('SHOW_TRY_EASIER') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Support Us Section</div>
                            <div class="toggle-desc">PayPal / Stripe / GitHub Sponsors donation section.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_SUPPORT_US" {{ $isTrue('SHOW_SUPPORT_US') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Active Rooms</div>
                            <div class="toggle-desc">Shows currently active rooms on the homepage.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_ACTIVE_ROOMS" {{ $isTrue('SHOW_ACTIVE_ROOMS') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fa fa-columns"></i> Header & Footer</h5>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Powered By</div>
                            <div class="toggle-desc">"Powered by MiroTalk SFU" branding badge.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_POWERED_BY" {{ $isTrue('SHOW_POWERED_BY') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label">Footer</div>
                            <div class="toggle-desc">The entire footer section at the bottom of the page.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="SHOW_FOOTER" {{ $isTrue('SHOW_FOOTER') ? 'checked' : '' }}>
                            <span class="slider-sw"></span>
                        </label>
                    </div>
                </div>

                <div class="alert alert-warning" style="border-radius:8px; font-size:12px;">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Note:</strong> Visibility changes require a CineMeet server restart to take effect. The restart happens automatically when you save.
                </div>

                <div class="text-right">
                    <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-default mr-2">Cancel</a>
                    <button type="submit" class="btn-save">
                        <i class="fa fa-save"></i> Save & Restart CineMeet
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
