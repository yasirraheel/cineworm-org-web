@extends("admin.admin_app")

@section("content")
@include('admin.pages.promo_mail.partials.content_styles')

@php
    $trackingDomainName = old('domain', isset($domain->domain) ? $domain->domain : '');
    $cnameTarget = old('cname_target', isset($domain->cname_target) ? $domain->cname_target : 'track.your-provider.com');
@endphp

<div class="content-page promo-mail-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        @include('admin.pages.promo_mail.partials.nav')

                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ URL::to('admin/promo_mail/tracking-domains') }}">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> Back To Tracking Domains
                                    </h4>
                                </a>
                            </div>
                        </div>

                        {!! Form::open(array('url' => array('admin/promo_mail/tracking-domains/save'),'name'=>'tracking_domain_form','id'=>'tracking_domain_form','role'=>'form')) !!}
                        <input type="hidden" name="id" value="{{ isset($domain->id) ? $domain->id : null }}">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">SMTP Server</label>
                                    <div class="col-sm-9">
                                        <select name="smtp_server_id" class="form-control">
                                            <option value="">Not linked yet</option>
                                            @foreach($servers as $server)
                                                <option value="{{ $server->id }}" @if(old('smtp_server_id', isset($domain->smtp_server_id) ? $domain->smtp_server_id : '') == $server->id) selected @endif>{{ $server->server_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Tracking Domain*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="domain" value="{{ $trackingDomainName }}" class="form-control" placeholder="tracking.example.com">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">CNAME Target*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="cname_target" value="{{ $cnameTarget }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Notes</label>
                                    <div class="col-sm-9">
                                        <textarea name="notes" class="form-control elm1_editor" rows="6">{{ old('notes', isset($domain->notes) ? $domain->notes : '') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Verification</label>
                                    <div class="col-sm-9">
                                        <div class="checkbox checkbox-success" style="padding-top: 8px;">
                                            <input id="is_verified" type="checkbox" name="is_verified" value="1" @if(old('is_verified', isset($domain->verified_at) ? (int) !empty($domain->verified_at) : 0)) checked @endif>
                                            <label for="is_verified">Mark CNAME as verified</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Status</label>
                                    <div class="col-sm-9">
                                        <select name="status" class="form-control">
                                            <option value="1" @if(old('status', isset($domain->status) ? $domain->status : 1)==1) selected @endif>Active</option>
                                            <option value="0" @if(old('status', isset($domain->status) ? $domain->status : 1)==0) selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="offset-sm-3 col-sm-9 pl-1">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save Tracking Domain</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card-box">
                                    <h4 class="header-title">Tracking Setup Instructions</h4>
                                    <ol class="pl-3">
                                        <li>Create a subdomain like <strong>track</strong> or <strong>links</strong>.</li>
                                        <li>Point it with a CNAME record to your email provider tracking target.</li>
                                        <li>Wait for DNS propagation, then mark it verified in admin.</li>
                                        <li>Future campaign links and open tracking should use this branded domain.</li>
                                    </ol>
                                </div>
                                <div class="card-box">
                                    <h4 class="header-title">DNS Record Preview</h4>
                                    <table class="table table-bordered m-b-0">
                                        <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Host</th>
                                            <th>Value</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>CNAME</td>
                                            <td>{{ $trackingDomainName ?: 'tracking.yourdomain.com' }}</td>
                                            <td style="word-break: break-all;">{{ $cnameTarget }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

<script type="text/javascript">
    @if (count($errors) > 0)
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: '<p>@foreach ($errors->all() as $error) {{$error}}<br/> @endforeach</p>',
        showConfirmButton: true,
        confirmButtonColor: '#10c469',
        background:"#1a2234",
        color:"#fff"
    });
    @endif
</script>

@endsection
