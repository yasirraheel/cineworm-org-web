@extends("admin.admin_app")

@section("content")
@include('admin.pages.promo_mail.partials.content_styles')

@php
    $currentDomain = parse_url(URL::to('/'), PHP_URL_HOST) ?: request()->getHost();
    $domainName = old('domain', isset($domain->domain) ? $domain->domain : '');
    $selector = old('selector', isset($domain->selector) ? $domain->selector : 'default');
    $returnPathSubdomain = old('return_path_subdomain', isset($domain->return_path_subdomain) ? $domain->return_path_subdomain : 'mail');
    $dmarcPolicy = old('dmarc_policy', isset($domain->dmarc_policy) ? $domain->dmarc_policy : 'quarantine');
    $dmarcReportEmail = old('dmarc_report_email', isset($domain->dmarc_report_email) ? $domain->dmarc_report_email : '');
    $dmarcAlignment = old('dmarc_alignment', isset($domain->dmarc_alignment) ? $domain->dmarc_alignment : 'relaxed');
    $spfValue = old('spf_value', isset($domain->spf_value) ? $domain->spf_value : 'v=spf1 include:'.$currentDomain.' ~all');
    $dmarcValue = 'v=DMARC1; p='.$dmarcPolicy.'; adkim=' . ($dmarcAlignment == 'strict' ? 's' : 'r') . '; aspf=' . ($dmarcAlignment == 'strict' ? 's' : 'r');
    if($dmarcReportEmail){
        $dmarcValue .= '; rua=mailto:'.$dmarcReportEmail;
    }
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
                                <a href="{{ URL::to('admin/promo_mail/sending-domains') }}">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> Back To Sending Domains
                                    </h4>
                                </a>
                            </div>
                        </div>

                        {!! Form::open(array('url' => array('admin/promo_mail/sending-domains/save'),'name'=>'sending_domain_form','id'=>'sending_domain_form','role'=>'form')) !!}
                        <input type="hidden" name="id" value="{{ isset($domain->id) ? $domain->id : null }}">

                        <div class="row">
                            <div class="col-md-12">
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
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Selector*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="selector" value="{{ $selector }}" class="form-control" placeholder="default">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DKIM Record Type*</label>
                                    <div class="col-sm-9">
                                        <select name="dkim_type" class="form-control">
                                            <option value="TXT" @if(old('dkim_type', isset($domain->dkim_type) ? $domain->dkim_type : 'TXT') == 'TXT') selected @endif>TXT</option>
                                            <option value="CNAME" @if(old('dkim_type', isset($domain->dkim_type) ? $domain->dkim_type : 'TXT') == 'CNAME') selected @endif>CNAME</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DKIM Value</label>
                                    <div class="col-sm-9">
                                        <textarea name="dkim_value" class="form-control" rows="4" placeholder="Paste the DKIM TXT value or CNAME target here">{{ old('dkim_value', isset($domain->dkim_value) ? $domain->dkim_value : '') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Return Path Subdomain</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="return_path_subdomain" value="{{ $returnPathSubdomain }}" class="form-control" placeholder="mail">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">SPF Value</label>
                                    <div class="col-sm-9">
                                        <textarea name="spf_value" class="form-control" rows="3">{{ $spfValue }}</textarea>
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
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Verification</label>
                                    <div class="col-sm-9">
                                        <div class="checkbox checkbox-success">
                                            <input id="dkim_status" type="checkbox" name="dkim_status" value="1" @if(old('dkim_status', isset($domain->dkim_status) ? $domain->dkim_status : 0)) checked @endif>
                                            <label for="dkim_status">DKIM verified</label>
                                        </div>
                                        <div class="checkbox checkbox-success">
                                            <input id="spf_status" type="checkbox" name="spf_status" value="1" @if(old('spf_status', isset($domain->spf_status) ? $domain->spf_status : 0)) checked @endif>
                                            <label for="spf_status">SPF verified</label>
                                        </div>
                                        <div class="checkbox checkbox-success">
                                            <input id="dmarc_status" type="checkbox" name="dmarc_status" value="1" @if(old('dmarc_status', isset($domain->dmarc_status) ? $domain->dmarc_status : 0)) checked @endif>
                                            <label for="dmarc_status">DMARC verified</label>
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
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save Sending Domain</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box">
                                    <h4 class="header-title">DNS Records To Publish</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered m-b-0">
                                            <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Host / Name</th>
                                                <th>Value</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>{{ old('dkim_type', isset($domain->dkim_type) ? $domain->dkim_type : 'TXT') }}</td>
                                                <td>{{ $selector }}._domainkey.{{ $domainName ?: $currentDomain }}</td>
                                                <td style="word-break: break-all;">{{ old('dkim_value', isset($domain->dkim_value) ? $domain->dkim_value : 'Paste your DKIM value here') }}</td>
                                            </tr>
                                            <tr>
                                                <td>TXT</td>
                                                <td>{{ $domainName ?: $currentDomain }}</td>
                                                <td style="word-break: break-all;">{{ $spfValue }}</td>
                                            </tr>
                                            <tr>
                                                <td>TXT</td>
                                                <td>_dmarc.{{ $domainName ?: $currentDomain }}</td>
                                                <td style="word-break: break-all;">{{ $dmarcValue }}</td>
                                            </tr>
                                            <tr>
                                                <td>CNAME/TXT</td>
                                                <td>{{ $returnPathSubdomain }}.{{ $domainName ?: $currentDomain }}</td>
                                                <td style="word-break: break-all;">Return-path / bounce domain provided by your SMTP vendor</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box">
                                    <h4 class="header-title">Setup Guide</h4>
                                    <ol class="m-b-0 pl-3">
                                        <li>Use the same domain as your sender email whenever possible.</li>
                                        <li>Add DKIM, SPF, and DMARC records in DNS and wait for propagation.</li>
                                        <li>Mark checks as verified only after testing with your provider tools.</li>
                                        <li>Use `quarantine` or `reject` DMARC once the mailbox is stable.</li>
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
