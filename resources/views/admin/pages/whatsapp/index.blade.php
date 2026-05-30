@extends("admin.admin_app")

@section("content")
<style>
    .whatsapp-admin-page .card-box,
    .whatsapp-admin-page .card-box p,
    .whatsapp-admin-page .card-box label,
    .whatsapp-admin-page .card-box strong,
    .whatsapp-admin-page .card-box span,
    .whatsapp-admin-page .card-box td,
    .whatsapp-admin-page .card-box th,
    .whatsapp-admin-page .card-box div {
        color: #f2f4f8;
    }

    .whatsapp-admin-page .text-muted,
    .whatsapp-admin-page .helper-text {
        color: #c7d2e0 !important;
    }

    .whatsapp-admin-page .form-control {
        background: #25262b;
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.12);
    }

    .whatsapp-admin-page .form-control:focus {
        background: #2d2f36;
        color: #ffffff;
        border-color: #10c469;
    }

    .whatsapp-status-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0;
    }

    .whatsapp-status-connected {
        background: #10c469;
        color: #ffffff;
    }

    .whatsapp-status-qr,
    .whatsapp-status-connecting {
        background: #f9c851;
        color: #1f1f1f;
    }

    .whatsapp-status-unavailable,
    .whatsapp-status-error,
    .whatsapp-status-disconnected,
    .whatsapp-status-logged_out {
        background: #ff5b5b;
        color: #ffffff;
    }

    .whatsapp-qr-box {
        min-height: 350px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: #1f2025;
        border-radius: 4px;
        padding: 20px;
    }

    .whatsapp-qr-box img {
        width: 320px;
        max-width: 100%;
        background: #ffffff;
        padding: 12px;
        border-radius: 4px;
    }

    .whatsapp-inline-alert {
        display: none;
        padding: 10px 12px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .whatsapp-inline-alert.success {
        display: block;
        background: rgba(16, 196, 105, 0.16);
        border: 1px solid rgba(16, 196, 105, 0.45);
        color: #ffffff;
    }

    .whatsapp-inline-alert.error {
        display: block;
        background: rgba(255, 91, 91, 0.16);
        border: 1px solid rgba(255, 91, 91, 0.45);
        color: #ffffff;
    }
</style>

@php
    $currentStatus = $status['status'] ?? 'unavailable';
@endphp

<div class="content-page whatsapp-admin-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0 m-b-10">{{ $page_title }}</h4>
                                <p class="text-muted m-b-0">
                                    Connect a WhatsApp Web session, scan the QR code, and send a basic test message.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span id="wa-status-badge" class="whatsapp-status-badge whatsapp-status-{{ $currentStatus }}">
                                    {{ str_replace('_', ' ', $currentStatus) }}
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Connection</h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 180px;">Status</th>
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

                                    <form method="post" action="{{ URL::to('admin/whatsapp/connect') }}" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-qrcode"></i> Connect / Generate QR
                                        </button>
                                    </form>

                                    <form method="post" action="{{ URL::to('admin/whatsapp/logout') }}" style="display:inline-block;">
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
                                            <small class="helper-text">Use country code without plus sign, for example 923001234567.</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary" id="wa-send-button">
                                            <i class="fa fa-send"></i> Send Message
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">QR Code</h4>
                                    <div class="whatsapp-qr-box">
                                        <img id="wa-qr-image" src="{{ $status['qrDataUrl'] ?? '' }}" alt="WhatsApp QR Code" style="{{ !empty($status['qrDataUrl']) ? '' : 'display:none;' }}">
                                        <p id="wa-qr-empty" class="text-muted m-b-0" style="{{ !empty($status['qrDataUrl']) ? 'display:none;' : '' }}">
                                            Click Connect / Generate QR. The QR code will appear here when WhatsApp is ready.
                                        </p>
                                    </div>
                                    <p class="helper-text m-t-10 m-b-0">
                                        Open WhatsApp on your phone, use Linked Devices, and scan this QR code.
                                    </p>
                                </div>
                            </div>
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
            fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(function (response) { return response.json(); })
                .then(updateStatus)
                .catch(function () {
                    updateStatus({ status: 'unavailable', lastError: 'Unable to reach Laravel status endpoint.' });
                });
        }

        pollStatus();
        setInterval(pollStatus, 5000);

        function showSendAlert(type, message) {
            sendAlert.className = 'whatsapp-inline-alert ' + type;
            sendAlert.textContent = message;
        }

        if (sendForm) {
            sendForm.addEventListener('submit', function (event) {
                event.preventDefault();

                sendButton.disabled = true;
                sendButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
                sendAlert.className = 'whatsapp-inline-alert';
                sendAlert.textContent = '';

                fetch(sendForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(sendForm)
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok || !data.ok) {
                                throw data;
                            }

                            return data;
                        });
                    })
                    .then(function () {
                        showSendAlert('success', 'WhatsApp message sent successfully.');
                        sendForm.querySelector('textarea[name="message"]').value = '';
                        pollStatus();
                    })
                    .catch(function (error) {
                        var message = error && error.error ? error.error : 'Message could not be sent.';
                        showSendAlert('error', message);
                    })
                    .finally(function () {
                        sendButton.disabled = false;
                        sendButton.innerHTML = '<i class="fa fa-send"></i> Send Message';
                    });
            });
        }
    })();
</script>

<script type="text/javascript">
    @if (Session::has('flash_message'))
        Swal.fire({
            icon: 'success',
            title: '{{ Session::get('flash_message') }}',
            showConfirmButton: false,
            timer: 3000,
            background: "#1a2234",
            color: "#fff"
        });
    @endif

    @if (Session::has('error_flash_message'))
        Swal.fire({
            icon: 'error',
            title: '{{ Session::get('error_flash_message') }}',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background: "#1a2234",
            color: "#fff"
        });
    @endif

    @if (count($errors) > 0)
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '<p>@foreach ($errors->all() as $error) {{ $error }}<br/> @endforeach</p>',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background: "#1a2234",
            color: "#fff"
        });
    @endif
</script>
@endsection
