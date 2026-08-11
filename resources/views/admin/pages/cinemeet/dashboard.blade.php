@extends('admin.admin_app')

@section('content')
<style>
    .cinemeet-header {
        background: linear-gradient(135deg, #161b26 0%, #1f2737 50%, #253147 100%);
        border: 1px solid #2d3748;
        border-radius: 12px;
        padding: 24px 28px;
        margin-bottom: 24px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cinemeet-header h2 { margin: 0; font-size: 22px; font-weight: 700; color: #fff; }
    .cinemeet-header p { margin: 4px 0 0; color: #a0aec0; font-size: 13px; }
    .cm-badge-online  { background: #10b981; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .cm-badge-offline { background: #ef4444; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    .stat-card {
        background: #252b36;
        border: 1px solid #2d3748;
        border-radius: 10px;
        padding: 20px 22px;
        box-shadow: 0 4px 15px rgba(0,0,0,.15);
        border-left: 4px solid #3b82f6;
        margin-bottom: 20px;
    }
    .stat-card .stat-icon { font-size: 28px; color: #60a5fa; float: right; margin-top: -2px; }
    .stat-card .stat-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600; margin-bottom: 6px; }
    .stat-card .stat-value { font-size: 24px; font-weight: 700; color: #f8fafc; }
    .stat-card .stat-sub { font-size: 12px; color: #64748b; margin-top: 4px; }

    .quick-action-btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all .2s; }
    .quick-action-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.3); text-decoration: none; }
    .btn-restart { background: #ef4444; color: #fff; }
    .btn-restart:hover { background: #dc2626; color: #fff; }
    .btn-visit { background: #3b82f6; color: #fff; }
    .btn-visit:hover { background: #2563eb; color: #fff; }

    .nav-links-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px; margin-top: 8px; }
    .nav-card {
        background: #252b36;
        border: 1px solid #333b4d;
        border-radius: 10px;
        padding: 20px 16px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,.15);
        text-decoration: none;
        color: #e2e8f0;
        transition: all .2s ease-in-out;
    }
    .nav-card:hover {
        border-color: #60a5fa;
        background: #2d3544;
        transform: translateY(-3px);
        text-decoration: none;
        color: #60a5fa;
    }
    .nav-card .nav-icon { font-size: 28px; margin-bottom: 8px; }
    .nav-card .nav-label { font-size: 13px; font-weight: 600; color: #f1f5f9; }
    .nav-card .nav-sub { font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .nav-card:hover .nav-label { color: #60a5fa; }

    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #2d3748; font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-key { color: #94a3b8; }
    .info-val { color: #f1f5f9; font-weight: 500; }
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

            {{-- Header --}}
            <div class="cinemeet-header">
                <div>
                    <h2><i class="fa fa-video-camera" style="color:#60a5fa;"></i> &nbsp;CineMeet Manager</h2>
                    <p>Manage your live video conferencing platform from here</p>
                </div>
                <div style="text-align:right;">
                    @if(isset($status['status']) && $status['status'] === 'online')
                        <span class="cm-badge-online"><i class="fa fa-circle"></i> Online</span>
                    @else
                        <span class="cm-badge-offline"><i class="fa fa-circle"></i> Offline</span>
                    @endif
                    <div style="margin-top:8px; font-size:12px; color:#94a3b8;">
                        {{ env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org') }}
                    </div>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card" style="border-left-color:#3b82f6;">
                        <i class="fa fa-clock-o stat-icon" style="color:#60a5fa;"></i>
                        <div class="stat-label">Uptime</div>
                        <div class="stat-value">{{ $status['uptime']['formatted'] ?? '—' }}</div>
                        <div class="stat-sub">Since last restart</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left-color:#10b981;">
                        <i class="fa fa-code-fork stat-icon" style="color:#34d399;"></i>
                        <div class="stat-label">Version</div>
                        <div class="stat-value" style="font-size:18px;">{{ $status['version'] ?? '—' }}</div>
                        <div class="stat-sub">CineMeet SFU</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left-color:#f59e0b;">
                        <i class="fa fa-microchip stat-icon" style="color:#fbbf24;"></i>
                        <div class="stat-label">Memory Used</div>
                        <div class="stat-value" style="font-size:18px;">{{ $status['memory']['rss'] ?? '—' }}</div>
                        <div class="stat-sub">Heap: {{ $status['memory']['heapUsed'] ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left-color:#a855f7;">
                        <i class="fa fa-tag stat-icon" style="color:#c084fc;"></i>
                        <div class="stat-label">App Name</div>
                        <div class="stat-value" style="font-size:18px;">{{ $settings['APP_NAME'] ?? 'CineMeet' }}</div>
                        <div class="stat-sub">Node {{ $status['nodeVersion'] ?? '' }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Quick Actions & Info --}}
                <div class="col-md-4">
                    <div class="card-box" style="background:#252b36; border:1px solid #2d3748; border-radius:10px;">
                        <h4 class="header-title mb-3" style="color:#f8fafc;"><i class="fa fa-bolt" style="color:#f59e0b;"></i> Quick Actions</h4>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <form action="{{ URL::to('admin/cinemeet/restart') }}" method="POST">
                                {{ csrf_field() }}
                                <button type="submit" class="quick-action-btn btn-restart" onclick="return confirm('Restart CineMeet now?')" style="width:100%; justify-content:center;">
                                    <i class="fa fa-refresh"></i> Restart CineMeet
                                </button>
                            </form>
                            <a href="{{ env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org') }}" target="_blank" class="quick-action-btn btn-visit" style="justify-content:center;">
                                <i class="fa fa-external-link"></i> Visit Live Site
                            </a>
                        </div>

                        <hr style="margin:20px 0; border-color:#2d3748;">
                        <h4 class="header-title mb-3" style="color:#f8fafc;"><i class="fa fa-info-circle" style="color:#60a5fa;"></i> Server Info</h4>
                        <div class="info-row">
                            <span class="info-key">Domain</span>
                            <span class="info-val" style="color:#60a5fa;">{{ $status['domain'] ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Platform</span>
                            <span class="info-val">{{ ucfirst($status['platform'] ?? '—') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Last Checked</span>
                            <span class="info-val">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Navigation Grid --}}
                <div class="col-md-8">
                    <div class="card-box" style="background:#252b36; border:1px solid #2d3748; border-radius:10px;">
                        <h4 class="header-title mb-3" style="color:#f8fafc;"><i class="fa fa-th" style="color:#a855f7;"></i> Management Sections</h4>
                        <div class="nav-links-grid">
                            <a href="{{ URL::to('admin/cinemeet/branding') }}" class="nav-card">
                                <div class="nav-icon">🎨</div>
                                <div class="nav-label">Branding</div>
                                <div class="nav-sub">Name, Logo, Title</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/homepage') }}" class="nav-card">
                                <div class="nav-icon">🏠</div>
                                <div class="nav-label">Homepage</div>
                                <div class="nav-sub">Hero, CTA, Buttons</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/social') }}" class="nav-card">
                                <div class="nav-icon">🔗</div>
                                <div class="nav-label">Social & Links</div>
                                <div class="nav-sub">Discord, GitHub...</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/visibility') }}" class="nav-card">
                                <div class="nav-icon">👁</div>
                                <div class="nav-label">Visibility</div>
                                <div class="nav-sub">Show/Hide sections</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/seo') }}" class="nav-card">
                                <div class="nav-icon">🔍</div>
                                <div class="nav-label">SEO & Meta</div>
                                <div class="nav-sub">Title, OG, Keywords</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/server') }}" class="nav-card">
                                <div class="nav-icon">⚙️</div>
                                <div class="nav-label">Server</div>
                                <div class="nav-sub">Domain, Port, IP</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/api-docs') }}" class="nav-card">
                                <div class="nav-icon">📄</div>
                                <div class="nav-label">API Docs</div>
                                <div class="nav-sub">REST API Reference</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
