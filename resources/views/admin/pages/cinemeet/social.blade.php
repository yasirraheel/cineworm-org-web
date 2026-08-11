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
    .social-icon { font-size:18px; width:30px; text-align:center; }
    .input-group-text { background:#1e232d !important; border-color:#333b4d !important; color:#94a3b8 !important; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Social & Links
                </div>
                <h3>🔗 Social & Links</h3>
                <p>Manage community links, social media URLs, and contact information shown on CineMeet.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/social') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-section">
                    <h5><i class="fa fa-share-alt" style="color:#60a5fa;"></i> Community Links</h5>

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
                        <label class="col-sm-3 col-form-label"><i class="fab fa-github social-icon" style="color:#e2e8f0;"></i> GitHub URL</label>
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
                    <h5><i class="fa fa-envelope" style="color:#34d399;"></i> Contact Information</h5>

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
                    <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn-save">
                        <i class="fa fa-save"></i> Save Links
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
