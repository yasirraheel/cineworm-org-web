@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30"><i class="fa fa-server text-primary"></i> CineMeet — Server Settings</h4>

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

                            <form action="{{ URL::to('admin/cinemeet/server') }}" method="POST" class="form-horizontal" role="form">
                                {{ csrf_field() }}

                                <h5 class="text-custom m-b-20"><i class="fa fa-globe text-info"></i> Domain & Network</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Domain / Host URL</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="DOMAIN" class="form-control"
                                            value="{{ $settings['DOMAIN'] ?? 'https://cinemeet.cineworm.org' }}"
                                            placeholder="https://cinemeet.cineworm.org">
                                        <small class="form-text text-muted">The public URL of CineMeet (without trailing slash).</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Listen IP</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="SERVER_LISTEN_IP" class="form-control"
                                            value="{{ $settings['SERVER_LISTEN_IP'] ?? '0.0.0.0' }}"
                                            placeholder="0.0.0.0">
                                        <small class="form-text text-muted">IP interface to bind to. Use <code>0.0.0.0</code> for all.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Listen Port</label>
                                    <div class="col-sm-3">
                                        <input type="number" name="SERVER_LISTEN_PORT" class="form-control"
                                            value="{{ $settings['SERVER_LISTEN_PORT'] ?? '3010' }}"
                                            placeholder="3010" min="1" max="65535">
                                        <small class="form-text text-muted">Default port: 3010</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Announced IP</label>
                                    <div class="col-sm-5">
                                        <input type="text" name="ANNOUNCED_IP" class="form-control"
                                            value="{{ $settings['ANNOUNCED_IP'] ?? '' }}"
                                            placeholder="191.215.37.220">
                                        <small class="form-text text-muted">Public VPS IP address for WebRTC media streams.</small>
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-shield text-warning"></i> Security & CORS</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">CORS Origin</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="CORS_ORIGIN" class="form-control"
                                            value="{{ $settings['CORS_ORIGIN'] ?? '*' }}"
                                            placeholder="* or https://cineworm.org,https://cinemeet.cineworm.org">
                                        <small class="form-text text-muted">Allowed CORS origins. Use <code>*</code> for all domains.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Trust Proxy</label>
                                    <div class="col-sm-4">
                                        <select name="TRUST_PROXY" class="form-control">
                                            <option value="true"  {{ ($settings['TRUST_PROXY'] ?? '') === 'true'  ? 'selected' : '' }}>true (behind Nginx)</option>
                                            <option value="false" {{ ($settings['TRUST_PROXY'] ?? 'false') === 'false' ? 'selected' : '' }}>false (direct)</option>
                                        </select>
                                        <small class="form-text text-muted">Enable when running behind Nginx reverse proxy.</small>
                                    </div>
                                </div>

                                <div class="alert alert-danger m-t-25" role="alert">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> Saving server settings immediately restarts CineMeet. Active calls will be disconnected.
                                </div>

                                <div class="form-group row m-t-30">
                                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light" onclick="return confirm('Restart CineMeet server now?')">
                                            <i class="fa fa-save"></i> Save & Restart CineMeet
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
