@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30"><i class="fa fa-share-alt text-primary"></i> CineMeet — Social & Contact Links</h4>

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

                            <form action="{{ URL::to('admin/cinemeet/social') }}" method="POST" class="form-horizontal" role="form">
                                {{ csrf_field() }}

                                <h5 class="text-custom m-b-20"><i class="fa fa-link text-info"></i> Community Links</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><i class="fab fa-discord" style="color:#5865F2;"></i> Discord URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="SOCIAL_DISCORD_URL" class="form-control"
                                            value="{{ $settings['SOCIAL_DISCORD_URL'] ?? '' }}"
                                            placeholder="https://discord.gg/your-server">
                                        <small class="form-text text-muted">Discord invite link shown in community section.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><i class="fab fa-github"></i> GitHub URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="SOCIAL_GITHUB_URL" class="form-control"
                                            value="{{ $settings['SOCIAL_GITHUB_URL'] ?? '' }}"
                                            placeholder="https://github.com/your-repo">
                                        <small class="form-text text-muted">GitHub repository link.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><i class="fab fa-twitter" style="color:#1DA1F2;"></i> Twitter / X URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="SOCIAL_TWITTER_URL" class="form-control"
                                            value="{{ $settings['SOCIAL_TWITTER_URL'] ?? '' }}"
                                            placeholder="https://twitter.com/yourhandle">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><i class="fab fa-youtube" style="color:#FF0000;"></i> YouTube URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="SOCIAL_YOUTUBE_URL" class="form-control"
                                            value="{{ $settings['SOCIAL_YOUTUBE_URL'] ?? '' }}"
                                            placeholder="https://youtube.com/@yourchannel">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><i class="fab fa-facebook" style="color:#1877F2;"></i> Facebook URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="SOCIAL_FACEBOOK_URL" class="form-control"
                                            value="{{ $settings['SOCIAL_FACEBOOK_URL'] ?? '' }}"
                                            placeholder="https://facebook.com/yourpage">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><i class="fab fa-linkedin" style="color:#0077B5;"></i> LinkedIn URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="SOCIAL_LINKEDIN_URL" class="form-control"
                                            value="{{ $settings['SOCIAL_LINKEDIN_URL'] ?? '' }}"
                                            placeholder="https://linkedin.com/company/yourcompany">
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-envelope text-success"></i> Contact Information</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Contact Email</label>
                                    <div class="col-sm-8">
                                        <input type="email" name="CONTACT_EMAIL" class="form-control"
                                            value="{{ $settings['CONTACT_EMAIL'] ?? '' }}"
                                            placeholder="contact@yourdomain.com">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Website URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="CONTACT_WEBSITE_URL" class="form-control"
                                            value="{{ $settings['CONTACT_WEBSITE_URL'] ?? '' }}"
                                            placeholder="https://cineworm.org">
                                    </div>
                                </div>

                                <div class="form-group row m-t-30">
                                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="fa fa-save"></i> Save Links
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
