@extends("admin.admin_app")

@section("content")
@include('admin.pages.whatsapp.partials.content_styles')

@php
    $currentStatus = $status['status'] ?? 'unavailable';
@endphp

<div class="content-page whatsapp-admin-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        @include('admin.pages.whatsapp.partials.nav')

                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0 m-b-10">{{ $page_title }}</h4>
                                <p class="text-muted m-b-0">
                                    Manage the WhatsApp Web session, mobile lists, campaign pacing, and delivery logs from one place.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span id="wa-status-badge" class="whatsapp-status-badge whatsapp-status-{{ $currentStatus }}">
                                    {{ str_replace('_', ' ', $currentStatus) }}
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $listsCount }}</h3>
                                    <p class="m-b-0">Mobile lists</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $contactsCount }}</h3>
                                    <p class="m-b-0">Imported numbers</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $campaignsCount }}</h3>
                                    <p class="m-b-0">Campaigns</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $runningCampaignsCount }}</h3>
                                    <p class="m-b-0">Running now</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Connection</h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 170px;">Status</th>
                                                <td id="wa-status-text">{{ ucfirst(str_replace('_', ' ', $currentStatus)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Connected Number</th>
                                                <td id="wa-connected-number">{{ $status['connectedNumber'] ?? 'Not connected' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Error</th>
                                                <td id="wa-last-error">{{ $status['lastError'] ?? 'None' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <form id="wa-connect-form" method="post" action="{{ URL::to('admin/whatsapp/connect') }}" style="{{ $currentStatus === 'connected' ? 'display:none;' : 'display:inline-block;' }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-qrcode"></i> Connect / QR
                                        </button>
                                    </form>

                                    <form id="wa-logout-form" method="post" action="{{ URL::to('admin/whatsapp/logout') }}" style="{{ $currentStatus === 'connected' ? 'display:inline-block;' : 'display:none;' }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa fa-sign-out"></i> Logout
                                        </button>
                                    </form>
                                </div>

                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Send Test Message</h4>
                                    <div id="wa-send-alert" class="whatsapp-inline-alert"></div>
                                    <form method="post" action="{{ URL::to('admin/whatsapp/send') }}" id="wa-send-form">
                                        @csrf
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" name="number" class="form-control" placeholder="923001234567" value="{{ old('number') }}" required>
                                            <small>Use country code without plus sign.</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea name="message" class="form-control" rows="4" required>{{ old('message') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary" id="wa-send-button">
                                            <i class="fa fa-send"></i> Send Message
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">QR Code</h4>
                                    <div class="whatsapp-qr-box">
                                        <img id="wa-qr-image" src="{{ $status['qrDataUrl'] ?? '' }}" alt="WhatsApp QR Code" style="{{ !empty($status['qrDataUrl']) ? '' : 'display:none;' }}">
                                        <p id="wa-qr-empty" class="text-muted m-b-0" style="{{ !empty($status['qrDataUrl']) ? 'display:none;' : '' }}">
                                            Click Connect / QR. The QR code will appear here when WhatsApp is ready.
                                        </p>
                                    </div>
                                    <p class="helper-text m-t-10 m-b-0">
                                        Campaigns only send while this session is connected. The server validates numbers and uses paced batches for safer delivery.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-7">
                                <div class="card-box">
                                    <div class="row m-b-15">
                                        <div class="col-sm-7">
                                            <h4 class="header-title m-t-0">Recent Campaigns</h4>
                                        </div>
                                        <div class="col-sm-5 text-right">
                                            <a href="{{ URL::to('admin/whatsapp/campaigns/create') }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-plus"></i> New Campaign
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Campaign</th>
                                                    <th>Status</th>
                                                    <th>Progress</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($campaigns as $campaign)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $campaign->name }}</strong>
                                                        <div>{{ optional($campaign->contactList)->name ?: 'List missing' }}</div>
                                                    </td>
                                                    <td><span class="badge badge-{{ $campaign->getStatusBadgeClass() }}">{{ ucfirst($campaign->status) }}</span></td>
                                                    <td>
                                                        <div>{{ $campaign->processed_contacts }}/{{ $campaign->total_contacts }}</div>
                                                        <div class="whatsapp-progress"><span style="width: {{ $campaign->getProgressPercentage() }}%;"></span></div>
                                                    </td>
                                                    <td>
                                                        <a href="{{ URL::to('admin/whatsapp/campaigns/'.$campaign->id) }}" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center">No WhatsApp campaigns yet.</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Recent Delivery Log</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Phone</th>
                                                    <th>Status</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($recentSends as $send)
                                                <tr>
                                                    <td>{{ $send->phone }}</td>
                                                    <td><span class="badge badge-{{ $send->status === 'sent' ? 'success' : ($send->status === 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($send->status) }}</span></td>
                                                    <td>{{ $send->updated_at ? $send->updated_at->format('M d, H:i') : '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center">No delivery logs yet.</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-box" style="border: 1px dashed rgba(255,255,255,0.22);">
                            <h4 class="header-title">Ban Protection Defaults</h4>
                            <ul class="m-b-0">
                                <li>Use country-code numbers only, and import opted-in contacts.</li>
                                <li>Campaigns send in batches, use random delay windows, pause after every group, and respect daily limits.</li>
                                <li>The sidecar checks whether a number exists on WhatsApp before sending and avoids link previews by default.</li>
                                <li>Keep the first campaigns small, then raise limits slowly after delivery looks healthy.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.copyright')
</div>

<script type="text/javascript">
    (function () {
        var statusUrl = "{{ URL::to('admin/whatsapp/status') }}";
        var badge = document.getElementById('wa-status-badge');
        var statusText = document.getElementById('wa-status-text');
        var connectedNumber = document.getElementById('wa-connected-number');
        var lastError = document.getElementById('wa-last-error');
        var qrImage = document.getElementById('wa-qr-image');
        var qrEmpty = document.getElementById('wa-qr-empty');
        var connectForm = document.getElementById('wa-connect-form');
        var logoutForm = document.getElementById('wa-logout-form');
        var sendForm = document.getElementById('wa-send-form');
        var sendButton = document.getElementById('wa-send-button');
        var sendAlert = document.getElementById('wa-send-alert');

        function label(value) {
            return String(value || 'unavailable').replace(/_/g, ' ');
        }

        function updateStatus(data) {
            var current = data.status || 'unavailable';
            badge.className = 'whatsapp-status-badge whatsapp-status-' + current;
            badge.textContent = label(current);
            statusText.textContent = label(current);
            connectedNumber.textContent = data.connectedNumber || 'Not connected';
            lastError.textContent = data.lastError || 'None';

            if (connectForm && logoutForm) {
                if (current === 'connected') {
                    connectForm.style.display = 'none';
                    logoutForm.style.display = 'inline-block';
                } else {
                    connectForm.style.display = 'inline-block';
                    logoutForm.style.display = 'none';
                }
            }

            if (data.qrDataUrl) {
                qrImage.src = data.qrDataUrl;
                qrImage.style.display = '';
                qrEmpty.style.display = 'none';
            } else {
                qrImage.style.display = 'none';
                qrEmpty.style.display = '';
            }
        }

        function pollStatus() {
            fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
                .then(function (response) { return response.json(); })
                .then(updateStatus)
                .catch(function () {
                    updateStatus({ status: 'unavailable', lastError: 'Unable to reach Laravel status endpoint.' });
                });
        }

        function showSendAlert(type, message) {
            sendAlert.className = 'whatsapp-inline-alert ' + type;
            sendAlert.textContent = message;
        }

        pollStatus();
        setInterval(pollStatus, 5000);

        if (sendForm) {
            sendForm.addEventListener('submit', function (event) {
                event.preventDefault();
                sendButton.disabled = true;
                sendButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
                sendAlert.className = 'whatsapp-inline-alert';
                sendAlert.textContent = '';

                fetch(sendForm.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(sendForm)
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok || !data.ok) { throw data; }
                            return data;
                        });
                    })
                    .then(function () {
                        showSendAlert('success', 'WhatsApp message sent successfully.');
                        sendForm.querySelector('textarea[name="message"]').value = '';
                        pollStatus();
                    })
                    .catch(function (error) {
                        showSendAlert('error', error && error.error ? error.error : 'Message could not be sent.');
                    })
                    .finally(function () {
                        sendButton.disabled = false;
                        sendButton.innerHTML = '<i class="fa fa-send"></i> Send Message';
                    });
            });
        }
    })();
</script>

@include('admin.pages.whatsapp.partials.flash')
@endsection
