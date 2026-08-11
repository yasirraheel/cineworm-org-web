@extends('admin.admin_app')

@section('content')
<style>
    .cm-page-header { background: linear-gradient(135deg, #161b26, #253147); color:#fff; border-radius:10px; border:1px solid #2d3748; padding:20px 24px; margin-bottom:24px; }
    .cm-page-header h3 { margin:0; font-size:18px; font-weight:700; color:#fff; }
    .cm-page-header p { margin:4px 0 0; color:#a0aec0; font-size:12px; }
    .cm-breadcrumb { font-size:12px; color:#64748b; margin-bottom:6px; }
    .cm-breadcrumb a { color:#94a3b8; text-decoration:none; }
    .cm-breadcrumb a:hover { color:#60a5fa; }
    .docs-frame-wrapper { background:#252b36; border:1px solid #2d3748; border-radius:10px; padding:0; box-shadow:0 4px 15px rgba(0,0,0,.15); overflow:hidden; }
    .docs-toolbar { background:#1e232d; padding:12px 20px; border-bottom:1px solid #333b4d; display:flex; align-items:center; justify-content:space-between; }
    .docs-toolbar span { font-size:13px; font-weight:600; color:#f8fafc; }
    .docs-toolbar a { font-size:12px; color:#60a5fa; text-decoration:none; }
    .docs-toolbar a:hover { text-decoration:underline; }
    iframe.api-frame { width:100%; height:78vh; border:none; display:block; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="cm-page-header">
                <div class="cm-breadcrumb">
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; API Documentation
                </div>
                <h3>📄 API Documentation</h3>
                <p>Interactive REST API reference for CineMeet SFU. Test endpoints directly from the browser.</p>
            </div>

            <div class="docs-frame-wrapper">
                <div class="docs-toolbar">
                    <span><i class="fa fa-file-code-o" style="color:#60a5fa;"></i> CineMeet Swagger API Docs</span>
                    <div>
                        <a href="{{ $apiUrl }}/api" target="_blank"><i class="fa fa-external-link"></i> Open in New Tab</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="{{ $apiUrl }}/admin-api/status" target="_blank"><i class="fa fa-plug"></i> Test API Status</a>
                    </div>
                </div>
                <iframe class="api-frame"
                    src="{{ $apiUrl }}/api"
                    title="CineMeet API Documentation"
                    sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox">
                </iframe>
            </div>

        </div>
    </div>
</div>
@endsection
