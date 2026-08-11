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
    .danger-zone { border:1px solid #991b1b; background:#451a1a; border-radius:10px; padding:20px; margin-top:8px; }
    .danger-zone h5 { color:#fecaca; margin-top:0; font-size:14px; font-weight:700; }
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
                    <a href="{{ URL::to('admin/cinemeet') }}">CineMeet</a> &rsaquo; Server Settings
                </div>
                <h3>⚙️ Server Settings</h3>
                <p>Configure domain, network, and server-level settings. All changes trigger an automatic restart.</p>
            </div>

            <form action="{{ URL::to('admin/cinemeet/server') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-section">
                    <h5><i class="fa fa-globe" style="color:#60a5fa;"></i> Domain & Network</h5>

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
                    <h5><i class="fa fa-shield" style="color:#34d399;"></i> Security & CORS</h5>

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
                    <p style="font-size:12px; color:#fca5a5; margin-bottom:0;">
                        Server settings changes immediately restart CineMeet. All active video calls will be dropped.
                        Make changes during low-traffic periods.
                    </p>
                </div>

                <div class="text-right mt-3">
                    <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-secondary mr-2">Cancel</a>
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
