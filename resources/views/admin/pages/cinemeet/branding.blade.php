@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30"><i class="fa fa-paint-brush text-primary"></i> CineMeet — Branding Settings</h4>

                            @if(session('flash_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('flash_message') }}
                                </div>
                            @endif
                            @if(session('flash_error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('flash_error') }}
                                </div>
                            @endif

                            <form action="{{ URL::to('admin/cinemeet/branding') }}" method="POST" class="form-horizontal" role="form">
                                {{ csrf_field() }}

                                <h5 class="text-custom m-b-20"><i class="fa fa-id-card"></i> App Identity</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">App Name*</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="APP_NAME" class="form-control"
                                            value="{{ $settings['APP_NAME'] ?? 'CineMeet' }}"
                                            placeholder="e.g. CineMeet">
                                        <small class="form-text text-muted">Short name shown in browser tab and header.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">App Title</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="APP_TITLE" class="form-control"
                                            value="{{ $settings['APP_TITLE'] ?? 'CineMeet SFU' }}"
                                            placeholder="e.g. CineMeet SFU — Free Video Calls">
                                        <small class="form-text text-muted">Full title shown on landing page hero section.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">App Description</label>
                                    <div class="col-sm-8">
                                        <textarea name="APP_DESCRIPTION" class="form-control" rows="3"
                                            placeholder="Short description of your app...">{{ $settings['APP_DESCRIPTION'] ?? '' }}</textarea>
                                        <small class="form-text text-muted">Used in page meta description and landing page.</small>
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-image"></i> Logo & Icon</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Logo URL</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="APP_ICON" class="form-control"
                                            value="{{ $settings['APP_ICON'] ?? '../images/logo.svg' }}"
                                            placeholder="../images/logo.svg">
                                        <small class="form-text text-muted">Path to logo image relative to public folder (e.g. <code>../images/logo.svg</code>) or full HTTPS URL.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Apple Touch Icon</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="APP_APPLE_TOUCH_ICON" class="form-control"
                                            value="{{ $settings['APP_APPLE_TOUCH_ICON'] ?? '../images/logo.svg' }}"
                                            placeholder="../images/logo.svg">
                                        <small class="form-text text-muted">Icon for iOS home screen.</small>
                                    </div>
                                </div>

                                <div class="form-group row m-t-30">
                                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="fa fa-save"></i> Save & Restart CineMeet
                                        </button>
                                        <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-secondary waves-effect m-l-5">Cancel</a>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
