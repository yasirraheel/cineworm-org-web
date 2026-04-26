@extends("admin.admin_app")

@section("content")
@include('admin.pages.promo_mail.partials.content_styles')

@php
    $currentDomain = parse_url(URL::to('/'), PHP_URL_HOST) ?: request()->getHost();
    $domainName = old('domain', isset($domain->domain) ? $domain->domain : '');
    $selector = old('selector', isset($domain->selector) ? $domain->selector : 'xsender');
    $returnPathSubdomain = old('return_path_subdomain', isset($domain->return_path_subdomain) ? $domain->return_path_subdomain : 'mail');
    $dmarcPolicy = old('dmarc_policy', isset($domain->dmarc_policy) ? $domain->dmarc_policy : 'quarantine');
    $dmarcReportEmail = old('dmarc_report_email', isset($domain->dmarc_report_email) ? $domain->dmarc_report_email : 'dmarc@'.$currentDomain);
    $dmarcAlignment = old('dmarc_alignment', isset($domain->dmarc_alignment) ? $domain->dmarc_alignment : 'relaxed');
@endphp

<div class="content-page promo-mail-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        @include('admin.pages.promo_mail.partials.nav')

                        <div class="row m-b-20">
                            <div class="col-sm-6">
                                <a href="{{ URL::to('admin/promo_mail/sending-domains') }}">
                                    <h4 class="header-title m-t-0 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> Back To Sending Domains
                                    </h4>
                                </a>
                            </div>
                        </div>

                        {!! Form::open(array('url' => array('admin/promo_mail/sending-domains/save'),'name'=>'sending_domain_form','id'=>'sending_domain_form','role'=>'form')) !!}
                        <input type="hidden" name="id" value="{{ isset($domain->id) ? $domain->id : null }}">
                        <input type="hidden" name="dkim_type" value="TXT">

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
                                    <label class="col-sm-3 col-form-label">Domain*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="domain" value="{{ $domainName }}" class="form-control" placeholder="{{ $currentDomain }}">
                                        <small class="form-text text-muted">Use the same domain as the sender email whenever possible.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Selector*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="selector" value="{{ $selector }}" class="form-control" placeholder="xsender">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Return Path</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="return_path_subdomain" value="{{ $returnPathSubdomain }}" class="form-control" placeholder="mail">
                                        <small class="form-text text-muted">Optional bounce/return-path subdomain if your provider requires one.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DMARC Policy*</label>
                                    <div class="col-sm-9">
                                        <select name="dmarc_policy" class="form-control">
                                            <option value="none" @if($dmarcPolicy == 'none') selected @endif>none</option>
                                            <option value="quarantine" @if($dmarcPolicy == 'quarantine') selected @endif>quarantine</option>
                                            <option value="reject" @if($dmarcPolicy == 'reject') selected @endif>reject</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DMARC Report Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" name="dmarc_report_email" value="{{ $dmarcReportEmail }}" class="form-control" placeholder="dmarc@{{ $currentDomain }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Alignment</label>
                                    <div class="col-sm-9">
                                        <select name="dmarc_alignment" class="form-control">
                                            <option value="relaxed" @if($dmarcAlignment == 'relaxed') selected @endif>Relaxed</option>
                                            <option value="strict" @if($dmarcAlignment == 'strict') selected @endif>Strict</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="offset-sm-3 col-sm-9 pl-1">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">{{ isset($domain->id) ? 'Update & View DNS Records' : 'Save & Generate DNS Records' }}</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card-box">
                                    <h4 class="header-title">Configuration Guide</h4>
                                    <ul class="m-b-0 pl-3">
                                        <li>Use a dedicated campaign mailbox and match it with a verified domain.</li>
                                        <li>After saving, the system will generate DKIM, SPF, and DMARC records automatically.</li>
                                        <li>Publish the DNS records first, then use the verify button on the next screen.</li>
                                        <li>Only use the domain in campaigns after the DNS checks pass.</li>
                                    </ul>
                                </div>

                                <div class="card-box">
                                    <h4 class="header-title">What Happens Next</h4>
                                    <ol class="m-b-0 pl-3">
                                        <li>DNS records are generated for this domain.</li>
                                        <li>You get a dedicated DNS Records page with copy buttons.</li>
                                        <li>You can verify DNS later after propagation.</li>
                                    </ol>
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
