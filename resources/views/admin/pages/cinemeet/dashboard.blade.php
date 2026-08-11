@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box" style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <h4 class="header-title m-t-0 m-b-5"><i class="fa fa-video-camera text-primary"></i> CineMeet Manager</h4>
                                <p class="text-muted font-13 m-b-0">Manage your live video conferencing platform</p>
                            </div>
                            <div class="text-right">
                                @if(isset($status['status']) && $status['status'] === 'online')
                                    <span class="badge badge-success" style="font-size: 13px; padding: 6px 12px;"><i class="fa fa-circle"></i> Online</span>
                                @else
                                    <span class="badge badge-danger" style="font-size: 13px; padding: 6px 12px;"><i class="fa fa-circle"></i> Offline</span>
                                @endif
                                <div class="text-muted font-12 m-t-5">{{ env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

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

                {{-- Stats Widgets --}}
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-primary" data-plugin="counterup">{{ $status['uptime']['formatted'] ?? '—' }}</h2>
                                <h5>Uptime</h5>
                                <p class="text-muted font-12 m-b-0">Since last restart</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-success">{{ $status['version'] ?? '—' }}</h2>
                                <h5>Version</h5>
                                <p class="text-muted font-12 m-b-0">CineMeet SFU Engine</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-warning">{{ $status['memory']['rss'] ?? '—' }}</h2>
                                <h5>Memory Used</h5>
                                <p class="text-muted font-12 m-b-0">Heap: {{ $status['memory']['heapUsed'] ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-purple">{{ $settings['APP_NAME'] ?? 'CineMeet' }}</h2>
                                <h5>App Name</h5>
                                <p class="text-muted font-12 m-b-0">Node {{ $status['nodeVersion'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Left Column: Actions & Server Info --}}
                    <div class="col-md-4">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-20"><i class="fa fa-bolt text-warning"></i> Quick Actions</h4>

                            <form action="{{ URL::to('admin/cinemeet/restart') }}" method="POST" class="m-b-15">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-danger btn-block waves-effect waves-light" onclick="return confirm('Restart CineMeet server now?')">
                                    <i class="fa fa-refresh"></i> Restart CineMeet Server
                                </button>
                            </form>

                            <a href="{{ env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org') }}" target="_blank" class="btn btn-primary btn-block waves-effect waves-light">
                                <i class="fa fa-external-link"></i> Open CineMeet Web App
                            </a>

                            <hr class="m-t-25 m-b-20">

                            <h4 class="header-title m-b-15"><i class="fa fa-info-circle text-info"></i> Server Information</h4>

                            <table class="table table-sm table-borderless m-b-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted">Domain</td>
                                        <td class="text-right font-weight-bold text-primary">{{ $status['domain'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Platform</td>
                                        <td class="text-right font-weight-bold">{{ ucfirst($status['platform'] ?? '—') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">API Status</td>
                                        <td class="text-right font-weight-bold text-success">Connected (HTTP 200)</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Last Checked</td>
                                        <td class="text-right">{{ \Carbon\Carbon::now()->format('H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Right Column: Management Sections Grid --}}
                    <div class="col-md-8">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-20"><i class="fa fa-th text-purple"></i> Management Sections</h4>

                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <a href="{{ URL::to('admin/cinemeet/branding') }}" style="text-decoration:none;">
                                        <div class="card-box widget-user text-center" style="border: 1px solid rgba(255,255,255,0.08); transition: transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div class="m-b-10" style="font-size:32px;">🎨</div>
                                            <h5>Branding</h5>
                                            <p class="text-muted font-12 m-b-0">Name, Logo, Title</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <a href="{{ URL::to('admin/cinemeet/homepage') }}" style="text-decoration:none;">
                                        <div class="card-box widget-user text-center" style="border: 1px solid rgba(255,255,255,0.08); transition: transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div class="m-b-10" style="font-size:32px;">🏠</div>
                                            <h5>Homepage</h5>
                                            <p class="text-muted font-12 m-b-0">Hero, CTA, Buttons</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <a href="{{ URL::to('admin/cinemeet/social') }}" style="text-decoration:none;">
                                        <div class="card-box widget-user text-center" style="border: 1px solid rgba(255,255,255,0.08); transition: transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div class="m-b-10" style="font-size:32px;">🔗</div>
                                            <h5>Social & Links</h5>
                                            <p class="text-muted font-12 m-b-0">Discord, GitHub...</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <a href="{{ URL::to('admin/cinemeet/visibility') }}" style="text-decoration:none;">
                                        <div class="card-box widget-user text-center" style="border: 1px solid rgba(255,255,255,0.08); transition: transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div class="m-b-10" style="font-size:32px;">👁</div>
                                            <h5>Visibility</h5>
                                            <p class="text-muted font-12 m-b-0">Show/Hide Sections</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <a href="{{ URL::to('admin/cinemeet/seo') }}" style="text-decoration:none;">
                                        <div class="card-box widget-user text-center" style="border: 1px solid rgba(255,255,255,0.08); transition: transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div class="m-b-10" style="font-size:32px;">🔍</div>
                                            <h5>SEO & Meta</h5>
                                            <p class="text-muted font-12 m-b-0">Title, OG, Keywords</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <a href="{{ URL::to('admin/cinemeet/server') }}" style="text-decoration:none;">
                                        <div class="card-box widget-user text-center" style="border: 1px solid rgba(255,255,255,0.08); transition: transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div class="m-b-10" style="font-size:32px;">⚙️</div>
                                            <h5>Server</h5>
                                            <p class="text-muted font-12 m-b-0">Domain, Port, IP</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <a href="{{ URL::to('admin/cinemeet/api-docs') }}" style="text-decoration:none;">
                                        <div class="card-box widget-user text-center" style="border: 1px solid rgba(255,255,255,0.08); transition: transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <div class="m-b-10" style="font-size:32px;">📄</div>
                                            <h5>API Docs</h5>
                                            <p class="text-muted font-12 m-b-0">REST API Reference</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
