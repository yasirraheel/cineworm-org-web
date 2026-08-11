@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30"><i class="fa fa-home text-primary"></i> CineMeet — Homepage Content</h4>

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

                            <form action="{{ URL::to('admin/cinemeet/homepage') }}" method="POST" class="form-horizontal" role="form">
                                {{ csrf_field() }}

                                <h5 class="text-custom m-b-20"><i class="fa fa-star text-warning"></i> Hero Section</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Hero Title</label>
                                    <div class="col-sm-8">
                                        <textarea name="APP_HERO_TITLE" class="form-control" rows="3"
                                            placeholder="CineMeet&#10;HD Video Calls, Webinars & Online Classes.&#10;Simple, Secure, Fast.">{{ $settings['APP_HERO_TITLE'] ?? '' }}</textarea>
                                        <small class="form-text text-muted">Main heading on homepage. Use newlines for line breaks.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Hero Description</label>
                                    <div class="col-sm-8">
                                        <textarea name="APP_HERO_DESCRIPTION" class="form-control" rows="3"
                                            placeholder="Host interactive webinars, online classes...">{{ $settings['APP_HERO_DESCRIPTION'] ?? '' }}</textarea>
                                        <small class="form-text text-muted">Paragraph shown below hero title.</small>
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-sign-in text-info"></i> Room Entry Section</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Section Description</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="APP_JOIN_DESCRIPTION" class="form-control"
                                            value="{{ $settings['APP_JOIN_DESCRIPTION'] ?? 'Pick a room name. How about this one?' }}"
                                            placeholder="Pick a room name. How about this one?">
                                        <small class="form-text text-muted">Heading above the room input field.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Join / Create Button Label</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="JOIN_BUTTON_LABEL" class="form-control"
                                            value="{{ $settings['JOIN_BUTTON_LABEL'] ?? 'CREATE ROOM' }}"
                                            placeholder="CREATE ROOM">
                                        <small class="form-text text-muted">Text on the main CREATE/JOIN button.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Customize Button Label</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="CUSTOMIZE_BUTTON_LABEL" class="form-control"
                                            value="{{ $settings['CUSTOMIZE_BUTTON_LABEL'] ?? 'Customize Room' }}"
                                            placeholder="Customize Room">
                                        <small class="form-text text-muted">Text on the customize button.</small>
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-list-alt text-purple"></i> Features Section</h5>

                                <div class="form-group row">
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
