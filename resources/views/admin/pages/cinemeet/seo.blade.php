@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30"><i class="fa fa-search text-primary"></i> CineMeet — SEO & Meta Settings</h4>

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

                            <form action="{{ URL::to('admin/cinemeet/seo') }}" method="POST" class="form-horizontal" role="form">
                                {{ csrf_field() }}

                                <h5 class="text-custom m-b-20"><i class="fa fa-globe text-info"></i> Page SEO</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Page Title</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="SEO_TITLE" class="form-control"
                                            value="{{ $settings['SEO_TITLE'] ?? '' }}"
                                            placeholder="CineMeet SFU - Free Video Calls, Messaging and Screen Sharing"
                                            maxlength="70">
                                        <small class="form-text text-muted">Title shown in browser tab and search engine results.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Meta Description</label>
                                    <div class="col-sm-8">
                                        <textarea name="SEO_DESCRIPTION" class="form-control" rows="3"
                                            maxlength="160"
                                            placeholder="CineMeet SFU powered by WebRTC — Real-time Simple Secure Fast video calls...">{{ $settings['SEO_DESCRIPTION'] ?? '' }}</textarea>
                                        <small class="form-text text-muted">Summary shown in search engines. Max 160 characters.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Meta Keywords</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="SEO_KEYWORDS" class="form-control"
                                            value="{{ $settings['SEO_KEYWORDS'] ?? '' }}"
                                            placeholder="video call, webinar, screen sharing, online meeting">
                                        <small class="form-text text-muted">Comma-separated keyword tags.</small>
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-share-alt text-success"></i> OpenGraph (Social Sharing)</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">OG Type</label>
                                    <div class="col-sm-4">
                                        <select name="OG_TYPE" class="form-control">
                                            <option value="website" {{ ($settings['OG_TYPE'] ?? '') === 'website' ? 'selected' : '' }}>website</option>
                                            <option value="article" {{ ($settings['OG_TYPE'] ?? '') === 'article' ? 'selected' : '' }}>article</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">OG Site Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="OG_SITE_NAME" class="form-control"
                                            value="{{ $settings['OG_SITE_NAME'] ?? '' }}"
                                            placeholder="CineMeet">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">OG Title</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="OG_TITLE" class="form-control"
                                            value="{{ $settings['OG_TITLE'] ?? '' }}"
                                            placeholder="CineMeet — Free HD Video Calls">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">OG Description</label>
                                    <div class="col-sm-8">
                                        <textarea name="OG_DESCRIPTION" class="form-control" rows="2"
                                            placeholder="Host webinars, online classes and meetings...">{{ $settings['OG_DESCRIPTION'] ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">OG Image URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="OG_IMAGE" class="form-control"
                                            value="{{ $settings['OG_IMAGE'] ?? '' }}"
                                            placeholder="https://cinemeet.cineworm.org/images/preview.png">
                                        <small class="form-text text-muted">Full HTTPS URL of image displayed when shared on WhatsApp, Facebook, etc.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">OG URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="OG_URL" class="form-control"
                                            value="{{ $settings['OG_URL'] ?? '' }}"
                                            placeholder="https://cinemeet.cineworm.org">
                                    </div>
                                </div>

                                <div class="form-group row m-t-30">
                                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="fa fa-save"></i> Save SEO Settings
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
