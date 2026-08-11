@extends('admin.admin_app')

@section('content')
<style>
    .cm-page-header { background: linear-gradient(135deg, #1a1a2e, #0f3460); color:#fff; border-radius:10px; padding:20px 24px; margin-bottom:24px; }
    .cm-page-header h3 { margin:0; font-size:18px; font-weight:700; }
    .cm-page-header p { margin:4px 0 0; color:rgba(255,255,255,.6); font-size:12px; }
    .cm-breadcrumb { font-size:12px; color:rgba(255,255,255,.5); margin-bottom:6px; }
    .cm-breadcrumb a { color:rgba(255,255,255,.7); text-decoration:none; }
    .cm-breadcrumb a:hover { color:#fff; }
    .form-section { background:#fff; border-radius:10px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:20px; }
    .form-section h5 { font-size:14px; font-weight:700; color:#2c3e50; margin-bottom:18px; padding-bottom:10px; border-bottom:2px solid #f0f0f0; }
    .form-group label { font-size:13px; font-weight:600; color:#555; }
    .form-control { border-radius:6px; border:1px solid #dde; font-size:13px; }
    .form-control:focus { border-color:#0f3460; box-shadow:0 0 0 2px rgba(15,52,96,.1); }
    .btn-save { background:#0f3460; color:#fff; border:none; padding:10px 28px; border-radius:8px; font-size:13px; font-weight:600; }
    .btn-save:hover { background:#0d2c55; color:#fff; }
    .hint-text { font-size:11px; color:#aaa; margin-top:4px; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Branding
                </div>
                <h3>🎨 Branding</h3>
                <p>Change your app name, title, logo, and description.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/branding') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-section">
                    <h5><i class="fa fa-id-card"></i> App Identity</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">App Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="APP_NAME" class="form-control"
                                value="{{ $settings['APP_NAME'] ?? 'CineMeet' }}"
                                placeholder="e.g. CineMeet">
                            <div class="hint-text">Short name shown in browser tab and app header.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">App Title</label>
                        <div class="col-sm-8">
                            <input type="text" name="APP_TITLE" class="form-control"
                                value="{{ $settings['APP_TITLE'] ?? 'CineMeet SFU' }}"
                                placeholder="e.g. CineMeet SFU — Free Video Calls">
                            <div class="hint-text">Full title shown on landing page hero section.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">App Description</label>
                        <div class="col-sm-8">
                            <textarea name="APP_DESCRIPTION" class="form-control" rows="3"
                                placeholder="Short description of your app...">{{ $settings['APP_DESCRIPTION'] ?? '' }}</textarea>
                            <div class="hint-text">Used in page meta description and landing page.</div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fa fa-image"></i> Logo & Icon</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Logo URL</label>
                        <div class="col-sm-8">
                            <input type="text" name="APP_ICON" class="form-control"
                                value="{{ $settings['APP_ICON'] ?? '../images/logo.svg' }}"
                                placeholder="../images/logo.svg">
                            <div class="hint-text">Path to the main logo image. Relative to the public folder (e.g., <code>../images/logo.svg</code>) or full HTTPS URL.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Apple Touch Icon</label>
                        <div class="col-sm-8">
                            <input type="text" name="APP_APPLE_TOUCH_ICON" class="form-control"
                                value="{{ $settings['APP_APPLE_TOUCH_ICON'] ?? '../images/logo.svg' }}"
                                placeholder="../images/logo.svg">
                            <div class="hint-text">Icon for iOS home screen.</div>
                        </div>
                    </div>
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
