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
    .social-icon { font-size:20px; width:36px; text-align:center; }
    .input-group-text { background:#f8f9fa; border-color:#dde; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Social & Links
                </div>
                <h3>🔗 Social & Links</h3>
                <p>Manage community links, social media URLs, and contact information shown on CineMeet.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/social') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-section">
                    <h5><i class="fa fa-share-alt"></i> Community Links</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><i class="fab fa-discord social-icon" style="color:#5865F2;"></i> Discord URL</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="url" name="SOCIAL_DISCORD_URL" class="form-control"
                                    value="{{ $settings['SOCIAL_DISCORD_URL'] ?? '' }}"
                                    placeholder="https://discord.gg/your-server">
                            </div>
                            <div class="hint-text">Discord community invite link shown in "Join our community" section.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><i class="fab fa-github social-icon"></i> GitHub URL</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="url" name="SOCIAL_GITHUB_URL" class="form-control"
                                    value="{{ $settings['SOCIAL_GITHUB_URL'] ?? '' }}"
                                    placeholder="https://github.com/your-repo">
                            </div>
                            <div class="hint-text">GitHub repository link.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><i class="fab fa-twitter social-icon" style="color:#1DA1F2;"></i> Twitter / X URL</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="url" name="SOCIAL_TWITTER_URL" class="form-control"
                                    value="{{ $settings['SOCIAL_TWITTER_URL'] ?? '' }}"
                                    placeholder="https://twitter.com/yourhandle">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><i class="fab fa-youtube social-icon" style="color:#FF0000;"></i> YouTube URL</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="url" name="SOCIAL_YOUTUBE_URL" class="form-control"
                                    value="{{ $settings['SOCIAL_YOUTUBE_URL'] ?? '' }}"
                                    placeholder="https://youtube.com/@yourchannel">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><i class="fab fa-facebook social-icon" style="color:#1877F2;"></i> Facebook URL</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="url" name="SOCIAL_FACEBOOK_URL" class="form-control"
                                    value="{{ $settings['SOCIAL_FACEBOOK_URL'] ?? '' }}"
                                    placeholder="https://facebook.com/yourpage">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><i class="fab fa-linkedin social-icon" style="color:#0077B5;"></i> LinkedIn URL</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="url" name="SOCIAL_LINKEDIN_URL" class="form-control"
                                    value="{{ $settings['SOCIAL_LINKEDIN_URL'] ?? '' }}"
                                    placeholder="https://linkedin.com/company/yourcompany">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fa fa-envelope"></i> Contact Information</h5>

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
                </div>

                <div class="text-right">
                    <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-default mr-2">Cancel</a>
                    <button type="submit" class="btn-save">
                        <i class="fa fa-save"></i> Save Links
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
