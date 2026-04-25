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
                                <h4 class="header-title m-t-0">Tracking Domains</h4>
                                <p>
                                    Use branded subdomains for email opens, click tracking, and unsubscribe redirects instead of
                                    generic vendor URLs. This helps deliverability and brand trust.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ URL::to('admin/promo_mail/tracking-domains/add') }}" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Add Tracking Domain
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Domain</th>
                                    <th>CNAME Target</th>
                                    <th>Server</th>
                                    <th>Status</th>
                                    <th>Verified At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($domains as $domain)
                                    <tr>
                                        <td>{{ $domain->domain }}</td>
                                        <td style="word-break: break-all;">{{ $domain->cname_target }}</td>
                                        <td>{{ optional($domain->smtpServer)->server_name ?: 'Not linked' }}</td>
                                        <td>
                                            @if($domain->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $domain->verified_at ? \Carbon\Carbon::parse($domain->verified_at)->format('M d, Y H:i') : 'Pending' }}</td>
                                        <td>
                                            <a href="{{ URL::to('admin/promo_mail/tracking-domains/edit/'.$domain->id) }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ URL::to('admin/promo_mail/tracking-domains/delete/'.$domain->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this tracking domain?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No tracking domains added yet.</td>
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

<script type="text/javascript">
    @if(Session::has('flash_message'))
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
    });

    Toast.fire({
        icon: 'success',
        title: '{{ Session::get('flash_message') }}'
    });
    @endif
</script>

@endsection
