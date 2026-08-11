@extends('admin.admin_app')

@section('content')
<style>
    .cinemeet-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: 12px;
        padding: 28px 30px;
        margin-bottom: 24px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cinemeet-header h2 { margin: 0; font-size: 22px; font-weight: 700; }
    .cinemeet-header p { margin: 4px 0 0; color: rgba(255,255,255,0.65); font-size: 13px; }
    .cm-badge-online  { background: #1abc9c; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .cm-badge-offline { background: #e74c3c; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .stat-card { background: #fff; border-radius: 10px; padding: 22px 24px; box-shadow: 0 2px 12px rgba(0,0,0,.06); border-left: 4px solid #0f3460; margin-bottom: 20px; }
    .stat-card .stat-icon { font-size: 32px; color: #0f3460; float: right; margin-top: -4px; }
    .stat-card .stat-label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .stat-card .stat-value { font-size: 26px; font-weight: 700; color: #2c3e50; }
    .stat-card .stat-sub { font-size: 12px; color: #aaa; margin-top: 2px; }
    .quick-action-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all .2s; }
    .quick-action-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.2); text-decoration: none; }
    .btn-restart { background: #e74c3c; color: #fff; }
    .btn-restart:hover { background: #c0392b; color: #fff; }
    .btn-visit { background: #3498db; color: #fff; }
    .btn-visit:hover { background: #2980b9; color: #fff; }
    .nav-links-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; margin-top: 8px; }
    .nav-card { background: #fff; border-radius: 10px; padding: 18px 16px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,.06); text-decoration: none; color: #2c3e50; transition: all .2s; border: 2px solid transparent; }
    .nav-card:hover { border-color: #0f3460; transform: translateY(-2px); text-decoration: none; color: #0f3460; }
    .nav-card .nav-icon { font-size: 28px; margin-bottom: 8px; }
    .nav-card .nav-label { font-size: 13px; font-weight: 600; }
    .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-key { color: #888; }
    .info-val { color: #2c3e50; font-weight: 500; }
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

            {{-- Header --}}
            <div class="cinemeet-header">
                <div>
                    <h2><i class="fa fa-video-camera"></i> &nbsp;CineMeet Manager</h2>
                    <p>Manage your live video conferencing platform from here</p>
                </div>
                <div style="text-align:right;">
                    @if(isset($status['status']) && $status['status'] === 'online')
                        <span class="cm-badge-online"><i class="fa fa-circle"></i> Online</span>
                    @else
                        <span class="cm-badge-offline"><i class="fa fa-circle"></i> Offline</span>
                    @endif
                    <div style="margin-top:8px; font-size:12px; color:rgba(255,255,255,.6);">
                        {{ env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org') }}
                    </div>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="fa fa-clock-o stat-icon"></i>
                        <div class="stat-label">Uptime</div>
                        <div class="stat-value">{{ $status['uptime']['formatted'] ?? '—' }}</div>
                        <div class="stat-sub">Since last restart</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left-color:#27ae60;">
                        <i class="fa fa-code-fork stat-icon" style="color:#27ae60;"></i>
                        <div class="stat-label">Version</div>
                        <div class="stat-value" style="font-size:18px;">{{ $status['version'] ?? '—' }}</div>
                        <div class="stat-sub">CineMeet SFU</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left-color:#e67e22;">
                        <i class="fa fa-microchip stat-icon" style="color:#e67e22;"></i>
                        <div class="stat-label">Memory Used</div>
                        <div class="stat-value" style="font-size:18px;">{{ $status['memory']['rss'] ?? '—' }}</div>
                        <div class="stat-sub">Heap: {{ $status['memory']['heapUsed'] ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left-color:#9b59b6;">
                        <i class="fa fa-tag stat-icon" style="color:#9b59b6;"></i>
                        <div class="stat-label">App Name</div>
                        <div class="stat-value" style="font-size:18px;">{{ $settings['APP_NAME'] ?? 'CineMeet' }}</div>
                        <div class="stat-sub">Node {{ $status['nodeVersion'] ?? '' }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Quick Actions & Info --}}
                <div class="col-md-4">
                    <div class="card-box">
                        <h4 class="header-title mb-3"><i class="fa fa-bolt"></i> Quick Actions</h4>
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

                        <hr style="margin:18px 0;">
                        <h4 class="header-title mb-3"><i class="fa fa-info-circle"></i> Server Info</h4>
                        <div class="info-row">
                            <span class="info-key">Domain</span>
                            <span class="info-val">{{ $status['domain'] ?? '—' }}</span>
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
                    <div class="card-box">
                        <h4 class="header-title mb-3"><i class="fa fa-th"></i> Management Sections</h4>
                        <div class="nav-links-grid">
                            <a href="{{ URL::to('admin/cinemeet/branding') }}" class="nav-card">
                                <div class="nav-icon">🎨</div>
                                <div class="nav-label">Branding</div>
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">Name, Logo, Title</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/homepage') }}" class="nav-card">
                                <div class="nav-icon">🏠</div>
                                <div class="nav-label">Homepage</div>
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">Hero, CTA, Buttons</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/social') }}" class="nav-card">
                                <div class="nav-icon">🔗</div>
                                <div class="nav-label">Social & Links</div>
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">Discord, GitHub...</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/visibility') }}" class="nav-card">
                                <div class="nav-icon">👁</div>
                                <div class="nav-label">Visibility</div>
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">Show/Hide sections</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/seo') }}" class="nav-card">
                                <div class="nav-icon">🔍</div>
                                <div class="nav-label">SEO & Meta</div>
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">Title, OG, Keywords</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/server') }}" class="nav-card">
                                <div class="nav-icon">⚙️</div>
                                <div class="nav-label">Server</div>
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">Domain, Port, IP</div>
                            </a>
                            <a href="{{ URL::to('admin/cinemeet/api-docs') }}" class="nav-card">
                                <div class="nav-icon">📄</div>
                                <div class="nav-label">API Docs</div>
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">REST API Reference</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
