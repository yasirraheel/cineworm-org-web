@extends('site_app')

@section('head_title', 'WhatsApp Web & Campaigns | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>WhatsApp Web & Campaigns</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li>WhatsApp Web & Campaigns</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                @include('pages.user._sidebar')
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    @include('pages.user.whatsapp._nav')

                    @include('pages.user.whatsapp._flash')

                    {{-- ── Device Connection Card ── --}}
                    <div class="card mb-4" style="background:#161b26;border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:20px;box-shadow:0 10px 25px rgba(0,0,0,0.3);">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h4 style="color:#25D366;font-weight:700;margin-top:0;" id="waStatusTitle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#25D366" style="vertical-align:-3px;margin-right:6px;"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg> WhatsApp Device Connection
                                </h4>
                                <p style="color:#94a3b8;font-size:14px;margin-bottom:10px;" id="waStatusDesc">
                                    Connect your WhatsApp account to enable bulk messaging and automatic campaign delivery directly from your own WhatsApp number.
                                </p>
                                <div id="waStatusBadge" class="mb-3">
                                    @if(($status['connected'] ?? false))
                                        <span class="badge bg-success" style="font-size:14px;padding:8px 14px;"><i class="fa fa-check-circle"></i> Connected ({{ $status['connectedNumber'] ?? 'Active' }})</span>
                                    @else
                                        <span class="badge bg-warning text-dark" style="font-size:14px;padding:8px 14px;"><i class="fa fa-exclamation-triangle"></i> Disconnected / Pairing Required</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-5 text-end">
                                <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:flex-end; min-height:42px;">
                                    <button type="button" id="btnTestWa" class="btn btn-outline-info waves-effect waves-light" onclick="openTestMsgModal()" style="font-weight:600;margin:0;display:{{ ($status['connected'] ?? false) ? 'inline-flex' : 'none' }};align-items:center;gap:6px;">
                                        <i class="fa fa-paper-plane"></i> Send Test Msg
                                    </button>
                                    <button type="button" id="btnConnectWa" class="btn btn-success waves-effect waves-light" onclick="connectWhatsApp()" style="background-color:#25D366;border-color:#25D366;font-weight:600;margin:0;display:inline-flex;align-items:center;gap:6px;">
                                        <i class="fa fa-qrcode"></i> Connect / Scan QR
                                    </button>
                                    <button type="button" id="btnLogoutWa" class="btn btn-outline-danger waves-effect waves-light" onclick="logoutWhatsApp()" style="font-weight:600;margin:0;display:{{ ($status['connected'] ?? false) ? 'inline-flex' : 'none' }};align-items:center;gap:6px;">
                                        <i class="fa fa-power-off"></i> Disconnect
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Stat Cards ── --}}
                    <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card p-3 text-center" style="background:#1a2234;border:1px solid rgba(255,255,255,0.06);border-radius:8px;">
                                <div style="font-size:24px;color:#25D366;"><i class="fa fa-list-ul"></i></div>
                                <h3 style="color:#fff;margin:5px 0;font-weight:700;">{{ $listsCount }}</h3>
                                <small style="color:#94a3b8;text-transform:uppercase;font-size:11px;font-weight:600;">Contact Lists</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card p-3 text-center" style="background:#1a2234;border:1px solid rgba(255,255,255,0.06);border-radius:8px;">
                                <div style="font-size:24px;color:#38bdf8;"><i class="fa fa-users"></i></div>
                                <h3 style="color:#fff;margin:5px 0;font-weight:700;">{{ $contactsCount }}</h3>
                                <small style="color:#94a3b8;text-transform:uppercase;font-size:11px;font-weight:600;">Total Contacts</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card p-3 text-center" style="background:#1a2234;border:1px solid rgba(255,255,255,0.06);border-radius:8px;">
                                <div style="font-size:24px;color:#fe0278;"><i class="fa fa-paper-plane"></i></div>
                                <h3 style="color:#fff;margin:5px 0;font-weight:700;">{{ $campaignsCount }}</h3>
                                <small style="color:#94a3b8;text-transform:uppercase;font-size:11px;font-weight:600;">Campaigns</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card p-3 text-center" style="background:#1a2234;border:1px solid rgba(255,255,255,0.06);border-radius:8px;">
                                <div style="font-size:24px;color:#10b981;"><i class="fa fa-bolt"></i></div>
                                <h3 style="color:#fff;margin:5px 0;font-weight:700;">{{ $runningCampaignsCount }}</h3>
                                <small style="color:#94a3b8;text-transform:uppercase;font-size:11px;font-weight:600;">Running Now</small>
                            </div>
                        </div>
                    </div>

                    {{-- ── Recent Campaigns ── --}}
                    <div class="card mb-4" style="background:#161b26;border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:20px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 style="color:#fff;font-weight:700;margin:0;"><i class="fa fa-history"></i> Recent Campaigns</h5>
                            <a href="{{ URL::to('user/whatsapp/campaigns/create') }}" class="btn btn-sm btn-success" style="background:#25D366;border-color:#25D366;font-weight:600;">
                                <i class="fa fa-plus"></i> New Campaign
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-bordered" style="font-size:14px;">
                                <thead>
                                    <tr>
                                        <th>Campaign Title</th>
                                        <th>Contact List</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($campaigns as $campaign)
                                        <tr>
                                            <td><strong>{{ $campaign->title }}</strong></td>
                                            <td>{{ $campaign->contactList->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="progress" style="height:18px;background:rgba(255,255,255,0.1);">
                                                    @php
                                                        $pct = $campaign->total_contacts > 0 ? round(($campaign->processed_contacts / $campaign->total_contacts) * 100) : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">{{ $pct }}%</div>
                                                </div>
                                                <small class="text-muted">{{ $campaign->processed_contacts }} / {{ $campaign->total_contacts }} sent</small>
                                            </td>
                                            <td>
                                                @if($campaign->status == 'running')
                                                    <span class="badge bg-success">Running</span>
                                                @elseif($campaign->status == 'completed')
                                                    <span class="badge bg-info text-dark">Completed</span>
                                                @elseif($campaign->status == 'paused')
                                                    <span class="badge bg-warning text-dark">Paused</span>
                                                @else
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ URL::to('user/whatsapp/campaigns/'.$campaign->id) }}" class="btn btn-sm btn-info" title="View Log"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No WhatsApp campaigns created yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for WhatsApp QR Code Pairing -->
<div class="modal fade" id="waQrModal" tabindex="-1" role="dialog" aria-labelledby="waQrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background-color:#1a2234;color:#ffffff;border:1px solid rgba(255,255,255,0.1);box-shadow:0 15px 40px rgba(0,0,0,0.5);">
            <div class="modal-header">
                <h5 class="modal-title" id="waQrModalLabel" style="color:#25D366;font-weight:700;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#25D366" style="vertical-align:-3px;margin-right:6px;"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg> Pair Your WhatsApp Device</h5>
                <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="waQrLoading" class="my-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Initializing WhatsApp pairing session...</p>
                </div>
                <div id="waQrContainer" style="display:none;">
                    <img id="waQrImage" src="" alt="WhatsApp QR Code" class="img-fluid rounded border p-2 bg-white" style="max-width:280px;">
                    <p class="mt-3 text-light" style="font-size:14px;">
                        1. Open <strong>WhatsApp</strong> on your phone.<br>
                        2. Tap <strong>Menu</strong> or <strong>Settings</strong> &gt; <strong>Linked Devices</strong>.<br>
                        3. Tap <strong>Link a Device</strong> and scan this QR code.
                    </p>
                </div>
                <div id="waConnectedState" style="display:none;" class="my-4">
                    <i class="fa fa-check-circle text-success" style="font-size:50px;"></i>
                    <h4 class="mt-3 text-success font-weight-bold">WhatsApp Device Connected!</h4>
                    <p class="text-muted" id="waConnectedNumberText"></p>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Sending Test WhatsApp Message -->
<div class="modal fade" id="waTestModal" tabindex="-1" role="dialog" aria-labelledby="waTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background-color:#161b26;color:#ffffff;border:1px solid rgba(255,255,255,0.08);box-shadow:0 10px 30px rgba(0,0,0,0.5);border-radius:10px;">
            <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,0.08);padding:15px 20px;">
                <h5 class="modal-title" id="waTestModalLabel" style="color:#25D366;font-weight:700;margin:0;">
                    <i class="fa fa-paper-plane"></i> Send Test WhatsApp Message
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color:#fff;opacity:0.8;font-size:24px;background:none;border:none;">&times;</button>
            </div>
            <form id="waTestForm" onsubmit="sendTestMessageSubmit(event)">
                <div class="modal-body p-4 text-start">
                    <div id="waTestAlert" style="display:none;" class="alert mb-3"></div>
                    
                    <div class="form-group mb-3">
                        <label for="testPhone" style="color:#cbd5e1;font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Recipient WhatsApp Number (With Country Code)</label>
                        <input type="text" class="form-control" id="testPhone" placeholder="e.g. 447956675381" required style="background:rgba(255,255,255,0.05);color:#fff;border:1px solid rgba(255,255,255,0.12);border-radius:6px;padding:10px 12px;">
                        <small style="color:#94a3b8;font-size:12px;margin-top:4px;display:block;">Include country code without spaces or dashes.</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="testMessage" style="color:#cbd5e1;font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Message Content</label>
                        <textarea class="form-control" id="testMessage" rows="3" required style="background:rgba(255,255,255,0.05);color:#fff;border:1px solid rgba(255,255,255,0.12);border-radius:6px;padding:10px 12px;">Hello! This is a test message sent from CineWorm WhatsApp Web.</textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,0.08);padding:15px 20px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" style="font-weight:600;">Cancel</button>
                    <button type="submit" id="btnSubmitTestMsg" class="btn btn-success" style="background-color:#25D366;border-color:#25D366;font-weight:600;color:#ffffff;">
                        <i class="fa fa-paper-plane"></i> Send Test Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let qrPollTimer = null;

function connectWhatsApp() {
    $('#waQrModal').modal('show');
    $('#waQrLoading').show();
    $('#waQrContainer').hide();
    $('#waConnectedState').hide();

    $.post("{{ URL::to('user/whatsapp/connect') }}", {_token: "{{ csrf_token() }}"}, function(res) {
        if (res.ok) {
            pollQrStatus();
        } else {
            alert(res.error || 'Failed to start pairing session.');
            $('#waQrModal').modal('hide');
        }
    }).fail(function() {
        alert('Server error starting WhatsApp pairing.');
        $('#waQrModal').modal('hide');
    });
}

function pollQrStatus() {
    if (qrPollTimer) clearInterval(qrPollTimer);

    qrPollTimer = setInterval(function() {
        $.get("{{ URL::to('user/whatsapp/qr') }}", function(res) {
            var isConn = res.connected || res.status === 'connected';
            var rawNum = res.connectedNumber || res.number || '';
            var cleanNum = rawNum.toString().replace('@s.whatsapp.net', '').split(':')[0];
            if (cleanNum && !cleanNum.startsWith('+')) cleanNum = '+' + cleanNum;

            if (isConn) {
                clearInterval(qrPollTimer);
                $('#waQrLoading').hide();
                $('#waQrContainer').hide();
                $('#waConnectedState').show();
                $('#waConnectedNumberText').text('Connected Phone: ' + (cleanNum || 'Active'));
                updatePageStatus(true, cleanNum);

                setTimeout(function() {
                    $('#waQrModal').modal('hide');
                }, 1500);
            } else if (res.qrDataUrl) {
                $('#waQrLoading').hide();
                $('#waConnectedState').hide();
                $('#waQrContainer').show();
                $('#waQrImage').attr('src', res.qrDataUrl);
                updatePageStatus(false);
            }
        });
    }, 2500);
}

function logoutWhatsApp() {
    if (!confirm('Are you sure you want to disconnect your WhatsApp account?')) return;

    $.post("{{ URL::to('user/whatsapp/logout') }}", {_token: "{{ csrf_token() }}"}, function(res) {
        updatePageStatus(false);
        location.reload();
    });
}

function updatePageStatus(connected, number = '') {
    var cleanNum = (number || '').toString().replace('@s.whatsapp.net', '').split(':')[0];
    if (cleanNum && !cleanNum.startsWith('+')) cleanNum = '+' + cleanNum;

    if (connected) {
        $('#waStatusBadge').html('<span class="badge bg-success" style="font-size:14px;padding:8px 14px;"><i class="fa fa-check-circle"></i> Connected (' + (cleanNum || 'Active') + ')</span>');
        $('#btnLogoutWa').css('display', 'inline-flex');
        $('#btnTestWa').css('display', 'inline-flex');
    } else {
        $('#waStatusBadge').html('<span class="badge bg-warning text-dark" style="font-size:14px;padding:8px 14px;"><i class="fa fa-exclamation-triangle"></i> Disconnected / Pairing Required</span>');
        $('#btnLogoutWa').hide();
        $('#btnTestWa').hide();
    }
}

function openTestMsgModal() {
    $('#waTestAlert').hide();
    $('#waTestModal').modal('show');
}

function sendTestMessageSubmit(e) {
    e.preventDefault();
    var phone = $('#testPhone').val().trim();
    var message = $('#testMessage').val().trim();

    if (!phone || !message) return;

    $('#btnSubmitTestMsg').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
    $('#waTestAlert').hide();

    $.post("{{ URL::to('user/whatsapp/send-test') }}", {
        _token: "{{ csrf_token() }}",
        phone: phone,
        message: message
    }, function(res) {
        $('#btnSubmitTestMsg').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Test Message');
        if (res.ok) {
            $('#waTestAlert').removeClass('alert-danger').addClass('alert-success').text(res.message).show();
            setTimeout(function() {
                $('#waTestModal').modal('hide');
            }, 2500);
        } else {
            $('#waTestAlert').removeClass('alert-success').addClass('alert-danger').text(res.error || 'Failed to send message.').show();
        }
    }).fail(function(xhr) {
        $('#btnSubmitTestMsg').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Test Message');
        var err = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Server error sending test message.';
        $('#waTestAlert').removeClass('alert-success').addClass('alert-danger').text(err).show();
    });
}
</script>
@endsection
