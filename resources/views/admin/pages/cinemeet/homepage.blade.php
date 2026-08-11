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
    .section-label { display:inline-block; background:#1e293b; color:#60a5fa; border:1px solid #334155; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:600; margin-bottom:6px; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Homepage Content
                </div>
                <h3>🏠 Homepage Content</h3>
                <p>Edit hero text, call-to-action buttons, and section descriptions shown on the landing page.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/homepage') }}" method="POST">
                {{ csrf_field() }}

                {{-- Hero Section --}}
                <div class="form-section">
                    <h5><i class="fa fa-star" style="color:#fbbf24;"></i> Hero Section</h5>
                    <div class="section-label">Landing page — top hero banner</div>

                    <div class="form-group row mt-3">
                        <label class="col-sm-3 col-form-label">Hero Title</label>
                        <div class="col-sm-8">
                            <textarea name="APP_HERO_TITLE" class="form-control" rows="3"
                                placeholder="CineMeet&#10;HD Video Calls, Webinars & Online Classes.&#10;Simple, Secure, Fast.">{{ $settings['APP_HERO_TITLE'] ?? '' }}</textarea>
                            <div class="hint-text">The big heading on the homepage. Use newlines for line breaks.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Hero Description</label>
                        <div class="col-sm-8">
                            <textarea name="APP_HERO_DESCRIPTION" class="form-control" rows="3"
                                placeholder="Host interactive webinars, online classes...">{{ $settings['APP_HERO_DESCRIPTION'] ?? '' }}</textarea>
                            <div class="hint-text">Paragraph shown below the hero title.</div>
                        </div>
                    </div>
                </div>

                {{-- Room Input Section --}}
                <div class="form-section">
                    <h5><i class="fa fa-sign-in" style="color:#60a5fa;"></i> Room Entry Section</h5>
                    <div class="section-label">The "Pick a room name" box</div>

                    <div class="form-group row mt-3">
                        <label class="col-sm-3 col-form-label">Section Description</label>
                        <div class="col-sm-8">
                            <input type="text" name="APP_JOIN_DESCRIPTION" class="form-control"
                                value="{{ $settings['APP_JOIN_DESCRIPTION'] ?? 'Pick a room name. How about this one?' }}"
                                placeholder="Pick a room name. How about this one?">
                            <div class="hint-text">Heading above the room name input field.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Join / Create Button Label</label>
                        <div class="col-sm-8">
                            <input type="text" name="JOIN_BUTTON_LABEL" class="form-control"
                                value="{{ $settings['JOIN_BUTTON_LABEL'] ?? 'CREATE ROOM' }}"
                                placeholder="CREATE ROOM">
                            <div class="hint-text">Text on the main blue CREATE/JOIN button.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Customize Button Label</label>
                        <div class="col-sm-8">
                            <input type="text" name="CUSTOMIZE_BUTTON_LABEL" class="form-control"
                                value="{{ $settings['CUSTOMIZE_BUTTON_LABEL'] ?? 'Customize Room' }}"
                                placeholder="Customize Room">
                            <div class="hint-text">Text on the secondary customize button.</div>
                        </div>
                    </div>
                </div>

                {{-- Features Section --}}
                <div class="form-section">
                    <h5><i class="fa fa-list-alt" style="color:#c084fc;"></i> Features Section</h5>
                    <div class="section-label">The features cards below the room input</div>

                    <div class="form-group row mt-3">
                        <label class="col-sm-3 col-form-label">Features Heading</label>
                        <div class="col-sm-8">
                            <input type="text" name="APP_FEATURES_HEADING" class="form-control"
                                value="{{ $settings['APP_FEATURES_HEADING'] ?? 'Unlimited number of conference rooms and users!' }}"
                                placeholder="Unlimited rooms and users...">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Features Description</label>
                        <div class="col-sm-8">
                            <textarea name="APP_FEATURES_DESCRIPTION" class="form-control" rows="2"
                                placeholder="With SFU integrated Server...">{{ $settings['APP_FEATURES_DESCRIPTION'] ?? '' }}</textarea>
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
