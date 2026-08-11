@extends('admin.admin_app')

@section('content')
<style>
    .cm-page-header { background: linear-gradient(135deg, #161b26, #253147); color:#fff; border-radius:10px; border:1px solid #2d3748; padding:20px 24px; margin-bottom:24px; }
    .cm-page-header h3 { margin:0; font-size:18px; font-weight:700; color:#fff; }
    .cm-page-header p { margin:4px 0 0; color:#a0aec0; font-size:12px; }
    .cm-breadcrumb { font-size:12px; color:#64748b; margin-bottom:6px; }
    .cm-breadcrumb a { color:#94a3b8; text-decoration:none; }
    .cm-breadcrumb a:hover { color:#60a5fa; }
    .form-section { background:#252b36; border:1px solid #2d3748; border-radius:10px; padding:24px; box-shadow:0 4px 15px rgba(0,0,0,.15); margin-bottom:20px; }
    .form-section h5 { font-size:14px; font-weight:700; color:#f8fafc; margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid #333b4d; }
    .form-group label { font-size:13px; font-weight:600; color:#cbd5e1; }
    .form-control { background:#1e232d !important; color:#f1f5f9 !important; border:1px solid #333b4d !important; border-radius:6px; font-size:13px; }
    .form-control:focus { border-color:#3b82f6 !important; box-shadow:0 0 0 2px rgba(59,130,246,.2) !important; }
    .btn-save { background:#3b82f6; color:#fff; border:none; padding:10px 28px; border-radius:8px; font-size:13px; font-weight:600; }
    .btn-save:hover { background:#2563eb; color:#fff; }
    .hint-text { font-size:11px; color:#94a3b8; margin-top:4px; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            @if(session('flash_message'))
                <div class="alert alert-success alert-dismissible" style="background:#065f46; color:#a7f3d0; border:1px solid #047857;">
                    <button type="button" class="close" data-dismiss="alert" style="color:#a7f3d0;">&times;</button>
                    {{ session('flash_message') }}
                </div>
            @endif
            @if(session('flash_error'))
                <div class="alert alert-danger alert-dismissible" style="background:#991b1b; color:#fecaca; border:1px solid #b91c1c;">
                    <button type="button" class="close" data-dismiss="alert" style="color:#fecaca;">&times;</button>
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
                    <h5><i class="fa fa-id-card" style="color:#60a5fa;"></i> App Identity</h5>

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
                    <h5><i class="fa fa-image" style="color:#34d399;"></i> Logo & Icon</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Logo URL</label>
                        <div class="col-sm-8">
                            <input type="text" name="APP_ICON" class="form-control"
                                value="{{ $settings['APP_ICON'] ?? '../images/logo.svg' }}"
                                placeholder="../images/logo.svg">
                            <div class="hint-text">Path to the main logo image. Relative to public folder (e.g. <code>../images/logo.svg</code>) or full HTTPS URL.</div>
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
                    <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn-save">
                        <i class="fa fa-save"></i> Save & Restart CineMeet
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
