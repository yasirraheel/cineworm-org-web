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
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0">Sending Domains</h4>
                                <p>
                                    Add the brand/domain used in the sender address, then publish SPF, DKIM, and DMARC records.
                                    Verification is kept dynamic in admin for now so your team can update status after DNS propagation.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ URL::to('admin/promo_mail/sending-domains/add') }}" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Add Sending Domain
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
                                    <th>Domain</th>
                                    <th>Authentication</th>
                                    <th>Server</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($domains as $domain)
                                    <tr>
                                        <td>
                                            <strong>{{ $domain->domain }}</strong>
                                            <div>Selector: {{ $domain->selector }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $domain->dkim_status ? 'badge-success' : 'badge-warning' }}">DKIM: {{ $domain->dkim_status ? 'Pass' : 'Pending' }}</span>
                                            <span class="badge {{ $domain->spf_status ? 'badge-success' : 'badge-warning' }}">SPF: {{ $domain->spf_status ? 'Pass' : 'Pending' }}</span>
                                            <span class="badge {{ $domain->dmarc_status ? 'badge-success' : 'badge-warning' }}">DMARC: {{ $domain->dmarc_status ? 'Pass' : 'Pending' }}</span>
                                            <div class="m-t-5">
                                                @if($domain->verified_at)
                                                    Verified {{ \Carbon\Carbon::parse($domain->verified_at)->format('M d, Y H:i') }}
                                                @else
                                                    Waiting for all checks
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ optional($domain->smtpServer)->server_name ?: 'Not linked' }}</td>
                                        <td>
                                            @if($domain->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ URL::to('admin/promo_mail/sending-domains/edit/'.$domain->id) }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ URL::to('admin/promo_mail/sending-domains/delete/'.$domain->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this sending domain?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No sending domains added yet.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $domains])
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

@endsection
