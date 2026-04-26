@extends("admin.admin_app")

@section("content")
@include('admin.pages.promo_mail.partials.content_styles')

@php
    $verifiedCount = ($dnsRecords['dkim']['verified'] ? 1 : 0) + ($dnsRecords['spf']['verified'] ? 1 : 0) + ($dnsRecords['dmarc']['verified'] ? 1 : 0);
@endphp

<div class="content-page promo-mail-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
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
                            <div class="col-sm-6 text-right">
                                <a href="{{ URL::to('admin/promo_mail/sending-domains/edit/'.$domain->id) }}" class="btn btn-primary">
                                    <i class="fa fa-pencil"></i> Edit Domain
                                </a>
                            </div>
                        </div>

                        <div class="card-box m-b-20">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="m-t-0 m-b-10">{{ $domain->domain }}</h3>
                                    <p class="m-b-5">
                                        @if($domain->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                        @if($domain->verified_at)
                                            <span class="m-l-10">Verified: {{ $domain->verified_at->diffForHumans() }}</span>
                                        @endif
                                    </p>
                                    <p class="m-b-0">SMTP Server: {{ optional($domain->smtpServer)->server_name ?: 'Not linked yet' }}</p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-primary m-b-10" id="verifyDnsBtn" data-url="{{ URL::to('admin/promo_mail/sending-domains/verify/'.$domain->id) }}">
                                        <i class="fa fa-shield"></i> Verify DNS Records
                                    </button>
                                    {!! Form::open(array('url' => array('admin/promo_mail/sending-domains/regenerate/'.$domain->id),'method'=>'post','style'=>'display:inline-block;')) !!}
                                        <button type="submit" class="btn btn-warning m-b-10">
                                            <i class="fa fa-refresh"></i> Regenerate Keys
                                        </button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>

                        <div class="row m-b-20">
                            <div class="col-md-4">
                                <div class="card-box text-center">
                                    <div class="m-b-15"><i class="fa fa-check-circle text-success" style="font-size: 42px;"></i></div>
                                    <h4 class="m-b-10">DKIM</h4>
                                    <p>Email Signing</p>
                                    <span class="badge {{ $dnsRecords['dkim']['verified'] ? 'badge-success' : 'badge-warning' }}" id="dkim-status">{{ $dnsRecords['dkim']['verified'] ? 'Verified' : 'Awaiting Setup' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-box text-center">
                                    <div class="m-b-15"><i class="fa fa-check-circle text-success" style="font-size: 42px;"></i></div>
                                    <h4 class="m-b-10">SPF</h4>
                                    <p>Sender Authorization</p>
                                    <span class="badge {{ $dnsRecords['spf']['verified'] ? 'badge-success' : 'badge-warning' }}" id="spf-status">{{ $dnsRecords['spf']['verified'] ? 'Verified' : 'Awaiting Setup' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-box text-center">
                                    <div class="m-b-15"><i class="fa fa-check-circle text-success" style="font-size: 42px;"></i></div>
                                    <h4 class="m-b-10">DMARC</h4>
                                    <p>Delivery Policy</p>
                                    <span class="badge {{ $dnsRecords['dmarc']['verified'] ? 'badge-success' : 'badge-warning' }}" id="dmarc-status">{{ $dnsRecords['dmarc']['verified'] ? 'Verified' : 'Awaiting Setup' }}</span>
                                </div>
                            </div>
                        </div>

                        @if($opensslCheck)
                            <div class="card-box m-b-20">
                                <h4 class="header-title">Server Diagnostic</h4>
                                <p>OpenSSL must be working on the server before DKIM keys can be generated properly.</p>
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>OpenSSL Extension</td>
                                        <td>{{ $opensslCheck['openssl_loaded'] ? 'Installed' : 'Not Installed' }}</td>
                                    </tr>
                                    <tr>
                                        <td>OpenSSL Version</td>
                                        <td>{{ $opensslCheck['openssl_version'] ?: 'Not detected' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Config File</td>
                                        <td>{{ $opensslCheck['config_path'] ?: 'Not found' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Key Generation Test</td>
                                        <td>{{ $opensslCheck['ready'] ? 'Working' : ($opensslCheck['error'] ?: 'Failed') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="card-box">
                            <h4 class="header-title">DNS Records To Configure</h4>
                            <p class="m-b-30">Add these DNS records in your domain DNS zone, then wait for propagation and click verify.</p>

                            <div class="card-box m-b-20">
                                <div class="row m-b-15">
                                    <div class="col-md-8">
                                        <h4 class="m-t-0">DKIM Record <span class="badge badge-danger">Required</span></h4>
                                        <p>DKIM signs your emails so inbox providers can trust that mail is really coming from your domain.</p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        @if($dnsRecords['dkim']['verified'])
                                            <span class="badge badge-success">Verified</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Type</label>
                                        <input type="text" class="form-control" value="{{ $dnsRecords['dkim']['type'] }}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Hostname / Name</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $dnsRecords['dkim']['hostname'] }}" readonly>
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $dnsRecords['dkim']['hostname'] }}"><i class="fa fa-copy"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Value / Content</label>
                                        <div class="input-group">
                                            <textarea class="form-control dns-record-textarea" rows="5" readonly>{{ $dnsRecords['dkim']['value'] }}</textarea>
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $dnsRecords['dkim']['value'] }}"><i class="fa fa-copy"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-box m-b-20">
                                <div class="row m-b-15">
                                    <div class="col-md-8">
                                        <h4 class="m-t-0">SPF Record <span class="badge badge-info">Recommended</span></h4>
                                        <p>SPF tells inbox providers which servers are allowed to send campaigns from your domain.</p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        @if($dnsRecords['spf']['verified'])
                                            <span class="badge badge-success">Verified</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Type</label>
                                        <input type="text" class="form-control" value="{{ $dnsRecords['spf']['type'] }}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Hostname / Name</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $dnsRecords['spf']['hostname'] }}" readonly>
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $dnsRecords['spf']['hostname'] }}"><i class="fa fa-copy"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Value / Content</label>
                                        <div class="input-group">
                                            <textarea class="form-control dns-record-textarea" rows="3" readonly>{{ $dnsRecords['spf']['value'] }}</textarea>
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $dnsRecords['spf']['value'] }}"><i class="fa fa-copy"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-box">
                                <div class="row m-b-15">
                                    <div class="col-md-8">
                                        <h4 class="m-t-0">DMARC Record <span class="badge badge-info">Recommended</span></h4>
                                        <p>DMARC tells inbox providers what to do when authentication fails and where to send reports.</p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        @if($dnsRecords['dmarc']['verified'])
                                            <span class="badge badge-success">Verified</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Type</label>
                                        <input type="text" class="form-control" value="{{ $dnsRecords['dmarc']['type'] }}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Hostname / Name</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $dnsRecords['dmarc']['hostname'] }}" readonly>
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $dnsRecords['dmarc']['hostname'] }}"><i class="fa fa-copy"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Value / Content</label>
                                        <div class="input-group">
                                            <textarea class="form-control dns-record-textarea" rows="3" readonly>{{ $dnsRecords['dmarc']['value'] }}</textarea>
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $dnsRecords['dmarc']['value'] }}"><i class="fa fa-copy"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-box m-t-20" id="verificationMessages" style="display:none;">
                            <h4 class="header-title">Verification Results</h4>
                            <ul id="messageList" class="m-b-0 p-l-20"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

<script type="text/javascript">
    document.addEventListener('click', function (event) {
        var button = event.target.closest('.copy-btn');
        if (!button) {
            return;
        }

        var value = button.getAttribute('data-copy') || '';
        navigator.clipboard.writeText(value).then(function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Copied',
                showConfirmButton: false,
                timer: 1800
            });
        });
    });

    var verifyButton = document.getElementById('verifyDnsBtn');
    if (verifyButton) {
        verifyButton.addEventListener('click', function () {
            var url = this.getAttribute('data-url');
            verifyButton.disabled = true;

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                verifyButton.disabled = false;

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: data.resp_status === 'success' ? 'success' : 'error',
                    title: data.resp_msg,
                    showConfirmButton: false,
                    timer: 3500
                });

                if (data.resp_status !== 'success') {
                    return;
                }

                document.getElementById('dkim-status').className = 'badge ' + (data.dkim ? 'badge-success' : 'badge-warning');
                document.getElementById('dkim-status').innerText = data.dkim ? 'Verified' : 'Awaiting Setup';
                document.getElementById('spf-status').className = 'badge ' + (data.spf ? 'badge-success' : 'badge-warning');
                document.getElementById('spf-status').innerText = data.spf ? 'Verified' : 'Awaiting Setup';
                document.getElementById('dmarc-status').className = 'badge ' + (data.dmarc ? 'badge-success' : 'badge-warning');
                document.getElementById('dmarc-status').innerText = data.dmarc ? 'Verified' : 'Awaiting Setup';

                var messagesBox = document.getElementById('verificationMessages');
                var messageList = document.getElementById('messageList');
                messageList.innerHTML = '';

                if (Array.isArray(data.messages)) {
                    data.messages.forEach(function (message) {
                        var item = document.createElement('li');
                        item.textContent = message;
                        messageList.appendChild(item);
                    });
                }

                messagesBox.style.display = 'block';
            }).catch(function () {
                verifyButton.disabled = false;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Verification request failed.',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        });
    }

    @if(Session::has('flash_message'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ Session::get('flash_message') }}',
        showConfirmButton: false,
        timer: 3000
    });
    @endif

    @if(Session::has('error_flash_message'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: '{{ Session::get('error_flash_message') }}',
        showConfirmButton: false,
        timer: 3500
    });
    @endif
</script>

@endsection
