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
    .og-preview { border:1px solid #333b4d; border-radius:8px; padding:16px; background:#1e232d; margin-top:12px; }
    .og-preview-title { font-size:14px; font-weight:700; color:#60a5fa; }
    .og-preview-url { font-size:12px; color:#34d399; margin:2px 0; }
    .og-preview-desc { font-size:12px; color:#cbd5e1; }
    .og-preview img { max-width:100%; border-radius:4px; margin-top:8px; max-height:120px; object-fit:cover; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; SEO & Meta
                </div>
                <h3>🔍 SEO & Meta Tags</h3>
                <p>Manage page title, meta description, OpenGraph tags for social sharing and search engines.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/seo') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-section">
                    <h5><i class="fa fa-search" style="color:#60a5fa;"></i> Page SEO</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Page Title</label>
                        <div class="col-sm-8">
                            <input type="text" name="SEO_TITLE" class="form-control"
                                value="{{ $settings['SEO_TITLE'] ?? '' }}"
                                placeholder="CineMeet SFU - Free Video Calls, Messaging and Screen Sharing"
                                maxlength="70">
                            <div class="hint-text">Shown in browser tab and search engine results. Keep under 60 characters.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Meta Description</label>
                        <div class="col-sm-8">
                            <textarea name="SEO_DESCRIPTION" class="form-control" rows="3"
                                maxlength="160"
                                placeholder="CineMeet SFU powered by WebRTC — Real-time Simple Secure Fast video calls...">{{ $settings['SEO_DESCRIPTION'] ?? '' }}</textarea>
                            <div class="hint-text">Shown in search engine results. Keep under 160 characters.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Meta Keywords</label>
                        <div class="col-sm-8">
                            <input type="text" name="SEO_KEYWORDS" class="form-control"
                                value="{{ $settings['SEO_KEYWORDS'] ?? '' }}"
                                placeholder="video call, webinar, screen sharing, online meeting">
                            <div class="hint-text">Comma-separated keywords.</div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fa fa-share-alt" style="color:#34d399;"></i> OpenGraph (Social Sharing)</h5>
                    <p style="font-size:12px; color:#94a3b8; margin-bottom:16px;">These tags control how your page looks when shared on Facebook, Twitter, WhatsApp, etc.</p>

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
                            <input type="url" name="OG_IMAGE" id="og_image_url" class="form-control"
                                value="{{ $settings['OG_IMAGE'] ?? '' }}"
                                placeholder="https://cinemeet.cineworm.org/images/preview.png"
                                oninput="updateOgPreview()">
                            <div class="hint-text">Full URL to the image shown when shared. Recommended: 1200×630px.</div>
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

                    {{-- Live Preview --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Preview</label>
                        <div class="col-sm-8">
                            <div class="og-preview">
                                <div class="og-preview-title" id="preview_title">{{ $settings['OG_TITLE'] ?? 'OG Title' }}</div>
                                <div class="og-preview-url">{{ $settings['OG_URL'] ?? 'https://cinemeet.cineworm.org' }}</div>
                                <div class="og-preview-desc" id="preview_desc">{{ $settings['OG_DESCRIPTION'] ?? 'OG Description' }}</div>
                                @if(!empty($settings['OG_IMAGE']))
                                    <img id="preview_img" src="{{ $settings['OG_IMAGE'] }}" alt="OG Image">
                                @else
                                    <img id="preview_img" src="" alt="OG Image" style="display:none;">
                                @endif
                            </div>
                            <div class="hint-text">Approximate preview of how your page looks when shared on social media.</div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn-save">
                        <i class="fa fa-save"></i> Save SEO Settings
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function updateOgPreview() {
    var url = document.getElementById('og_image_url').value;
    var img = document.getElementById('preview_img');
    if (url) {
        img.src = url;
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }
}
</script>
@endsection
