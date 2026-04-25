@extends("admin.admin_app")

@section("content")
@include('admin.pages.promo_mail.partials.content_styles')

<div class="content-page promo-mail-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        @include('admin.pages.promo_mail.partials.nav')

                        <div class="row m-b-20">
                            <div class="col-md-4">
                                <div class="card-box">
                                    <h4 class="m-b-5">{{ $servers->total() }}</h4>
                                    <p class="mb-0">Configured SMTP servers</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-box">
                                    <h4 class="m-b-5">{{ $sendingDomainsCount }}</h4>
                                    <p class="mb-0">Sending domains linked</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-box">
                                    <h4 class="m-b-5">{{ $trackingDomainsCount }}</h4>
                                    <p class="mb-0">Tracking domains linked</p>
                                </div>
                            </div>
                        </div>

                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0">Promotional SMTP Infrastructure</h4>
                                <p>
                                    Keep campaign SMTP separate from the app login/order email server. Add one or more promotional
                                    servers here, mark one as default, then attach verified sending and tracking domains before
                                    building campaigns.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ URL::to('admin/promo_mail/servers/add') }}" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Add SMTP Server
                                </a>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Server</th>
                                    <th>Sender</th>
                                    <th>SMTP</th>
                                    <th>Rate Controls</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($servers as $server)
                                    <tr>
                                        <td>
                                            <strong>{{ $server->server_name }}</strong>
                                            @if($server->is_default)
                                                <span class="badge badge-primary ml-1">Default</span>
                                            @endif
                                            <div>{{ strtoupper($server->gateway_type) }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $server->from_name ?: 'Not set' }}</div>
                                            <div>{{ $server->sender_email }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $server->host }}:{{ $server->port }}</div>
                                            <div>{{ $server->username }}</div>
                                        </td>
                                        <td>
                                            <div>Delay: {{ $server->min_delay_per_message }} - {{ $server->max_delay_per_message }} sec</div>
                                            <div>Pause {{ $server->pause_duration }} sec after {{ $server->pause_after_messages }} emails</div>
                                        </td>
                                        <td>
                                            @if($server->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ URL::to('admin/promo_mail/servers/edit/'.$server->id) }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ URL::to('admin/promo_mail/servers/delete/'.$server->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this SMTP server?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No promotional SMTP servers added yet.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $servers])
                        </nav>

                        <div class="row m-t-20">
                            <div class="col-md-12">
                                <div class="card-box" style="border: 1px dashed #d6d6d6;">
                                    <h4 class="header-title">Recommended Setup Flow</h4>
                                    <ol class="m-b-0" style="color: #cfd8e3;">
                                        <li>Add the SMTP server with mailbox, host, encryption, and rate limits.</li>
                                        <li>Add the sending domain and publish SPF, DKIM, and DMARC DNS records.</li>
                                        <li>Add a tracking domain for open/click tracking CNAME routing.</li>
                                        <li>Only after domain verification should campaigns use that server.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

@endsection
