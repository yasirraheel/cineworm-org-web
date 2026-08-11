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
    .form-group label { font-size:13px; font-weight:600; color:#555; }
    .form-control { border-radius:6px; border:1px solid #dde; font-size:13px; }
    .form-control:focus { border-color:#0f3460; box-shadow:0 0 0 2px rgba(15,52,96,.1); }
    .btn-save { background:#0f3460; color:#fff; border:none; padding:10px 28px; border-radius:8px; font-size:13px; font-weight:600; }
    .btn-save:hover { background:#0d2c55; color:#fff; }
    .hint-text { font-size:11px; color:#aaa; margin-top:4px; }
    .section-label { display:inline-block; background:#e8f4fd; color:#0f3460; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:600; margin-bottom:6px; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Homepage Content
                </div>
                <h3>🏠 Homepage Content</h3>
                <p>Edit hero text, call-to-action buttons, and section descriptions shown on the landing page.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/homepage') }}" method="POST">
                {{ csrf_field() }}

                {{-- Hero Section --}}
                <div class="form-section">
                    <h5><i class="fa fa-star"></i> Hero Section</h5>
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
                    <h5><i class="fa fa-sign-in"></i> Room Entry Section</h5>
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
                    <h5><i class="fa fa-list-alt"></i> Features Section</h5>
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
