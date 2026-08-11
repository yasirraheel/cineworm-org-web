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
    .danger-zone { border:2px solid #e74c3c; border-radius:10px; padding:20px; margin-top:8px; }
    .danger-zone h5 { color:#e74c3c; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Server Settings
                </div>
                <h3>⚙️ Server Settings</h3>
                <p>Configure domain, network, and server-level settings. All changes trigger an automatic restart.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/server') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-section">
                    <h5><i class="fa fa-globe"></i> Domain & Network</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Domain / Host URL</label>
                        <div class="col-sm-8">
                            <input type="text" name="DOMAIN" class="form-control"
                                value="{{ $settings['DOMAIN'] ?? 'https://cinemeet.cineworm.org' }}"
                                placeholder="https://cinemeet.cineworm.org">
                            <div class="hint-text">The public URL of CineMeet (without trailing slash).</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Listen IP</label>
                        <div class="col-sm-4">
                            <input type="text" name="SERVER_LISTEN_IP" class="form-control"
                                value="{{ $settings['SERVER_LISTEN_IP'] ?? '0.0.0.0' }}"
                                placeholder="0.0.0.0">
                            <div class="hint-text">IP to bind to. Use <code>0.0.0.0</code> for all interfaces.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Listen Port</label>
                        <div class="col-sm-3">
                            <input type="number" name="SERVER_LISTEN_PORT" class="form-control"
                                value="{{ $settings['SERVER_LISTEN_PORT'] ?? '3010' }}"
                                placeholder="3010" min="1" max="65535">
                            <div class="hint-text">Default: 3010</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Announced IP</label>
                        <div class="col-sm-5">
                            <input type="text" name="ANNOUNCED_IP" class="form-control"
                                value="{{ $settings['ANNOUNCED_IP'] ?? '' }}"
                                placeholder="191.215.37.220">
                            <div class="hint-text">Your public VPS IP address for WebRTC (mediasoup). Required for production.</div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5><i class="fa fa-shield"></i> Security & CORS</h5>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">CORS Origin</label>
                        <div class="col-sm-8">
                            <input type="text" name="CORS_ORIGIN" class="form-control"
                                value="{{ $settings['CORS_ORIGIN'] ?? '*' }}"
                                placeholder="* or https://cineworm.org,https://cinemeet.cineworm.org">
                            <div class="hint-text">Allowed CORS origins. Use <code>*</code> for all, or comma-separated specific domains.</div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Trust Proxy</label>
                        <div class="col-sm-4">
                            <select name="TRUST_PROXY" class="form-control">
                                <option value="true"  {{ ($settings['TRUST_PROXY'] ?? '') === 'true'  ? 'selected' : '' }}>true  (behind Nginx/proxy)</option>
                                <option value="false" {{ ($settings['TRUST_PROXY'] ?? 'false') === 'false' ? 'selected' : '' }}>false (direct)</option>
                            </select>
                            <div class="hint-text">Enable if CineMeet runs behind Nginx or a reverse proxy.</div>
                        </div>
                    </div>
                </div>

                <div class="danger-zone">
                    <h5><i class="fa fa-exclamation-triangle"></i> Danger Zone</h5>
                    <p style="font-size:12px; color:#666; margin-bottom:0;">
                        Server settings changes immediately restart CineMeet. All active video calls will be dropped.
                        Make changes during low-traffic periods.
                    </p>
                </div>

                <div class="text-right mt-3">
                    <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-default mr-2">Cancel</a>
                    <button type="submit" class="btn-save"
                        onclick="return confirm('This will restart CineMeet and drop all active calls. Continue?')">
                        <i class="fa fa-save"></i> Save & Restart CineMeet
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
