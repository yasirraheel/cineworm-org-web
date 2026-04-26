@extends("admin.admin_app")

@section("content")
<style>
    .cron-monitor-page .card-box,
    .cron-monitor-page .card-box p,
    .cron-monitor-page .card-box li,
    .cron-monitor-page .card-box label,
    .cron-monitor-page .card-box strong,
    .cron-monitor-page .card-box span,
    .cron-monitor-page .card-box td,
    .cron-monitor-page .card-box th,
    .cron-monitor-page .card-box div {
        color: #f2f4f8;
    }

    .cron-monitor-page .text-muted,
    .cron-monitor-page .helper-text,
    .cron-monitor-page .card-box .text-muted {
        color: #c7d2e0 !important;
    }

    .cron-monitor-page .card-box ul,
    .cron-monitor-page .card-box ol {
        padding-left: 22px;
    }

    .cron-monitor-page .card-box li {
        margin-bottom: 8px;
    }

    .cron-monitor-page code {
        background: #ffffff;
        color: #d61f3e;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 90%;
    }

    .cron-monitor-page .form-control[readonly],
    .cron-monitor-page textarea.form-control[readonly],
    .cron-monitor-page input.form-control[readonly] {
        background: #35353a;
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.12);
    }

    .cron-monitor-page .table-bordered > tbody > tr > td,
    .cron-monitor-page .table-bordered > tbody > tr > th,
    .cron-monitor-page .table-bordered > thead > tr > th,
    .cron-monitor-page .table-bordered > thead > tr > td {
        border-color: rgba(255, 255, 255, 0.12);
    }
</style>

<div class="content-page cron-monitor-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0 m-b-10">{{ $page_title }}</h4>
                                <p class="text-muted m-b-0">
                                    Scheduled campaigns and background promotional sending only move forward when server cron runs.
                                    Use this page to copy the correct command, test the cron manually, and monitor the last execution.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <form method="post" action="{{ URL::to('admin/cron_monitor/run-now') }}" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-play"></i> Run Cron Now
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="row m-b-20">
                            <div class="col-md-3">
                                <div class="card-box">
                                    <h4 class="m-b-5">{{ ucfirst($status['last_status']) }}</h4>
                                    <p class="mb-0">Last Run Status</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box">
                                    <h4 class="m-b-5">{{ $campaignStats['scheduled_due'] }}</h4>
                                    <p class="mb-0">Due Scheduled Campaigns</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box">
                                    <h4 class="m-b-5">{{ $campaignStats['running_total'] }}</h4>
                                    <p class="mb-0">Running Campaigns</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box">
                                    <h4 class="m-b-5">{{ $campaignStats['completed_today'] }}</h4>
                                    <p class="mb-0">Completed Today</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Execution Status</h4>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th style="width: 180px;">Last Trigger</th>
                                                <td>{{ $status['last_trigger'] ?: 'Not recorded yet' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Started</th>
                                                <td>{{ $status['last_started_at'] ?: 'Never' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Finished</th>
                                                <td>{{ $status['last_finished_at'] ?: 'Never' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Duration</th>
                                                <td>{{ $status['last_duration_seconds'] !== null ? $status['last_duration_seconds'].' sec' : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Message</th>
                                                <td>{{ $status['last_message'] ?: 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Why Campaigns Stay Scheduled</h4>
                                    <ul class="m-b-0 helper-text">
                                        <li>If server cron is not running every minute, scheduled campaigns will never start.</li>
                                        <li>If Laravel scheduler is configured, it will call <code>task:cron</code> automatically.</li>
                                        <li>If your hosting only supports URL cron, use the secure trigger URL shown below.</li>
                                        <li>If due scheduled campaigns are above zero for long periods, your cron is not firing correctly.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-box">
                            <h4 class="header-title m-t-0">Copyable Cron Setup</h4>
                            <div class="form-group">
                                <label>Project Directory</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $projectPath }}" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $projectPath }}"><i class="fa fa-copy"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Recommended Linux Cron Command</label>
                                <div class="input-group">
                                    <textarea class="form-control" rows="3" readonly>{{ $scheduleCommand }}</textarea>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $scheduleCommand }}"><i class="fa fa-copy"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Direct Task Cron Command</label>
                                <div class="input-group">
                                    <textarea class="form-control" rows="3" readonly>{{ $taskCommand }}</textarea>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $taskCommand }}"><i class="fa fa-copy"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group m-b-0">
                                <label>Secure URL Trigger For cPanel / HTTP Cron</label>
                                <div class="input-group">
                                    <textarea class="form-control" rows="2" readonly>{{ $triggerUrl }}</textarea>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary copy-btn" type="button" data-copy="{{ $triggerUrl }}"><i class="fa fa-copy"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Setup Guide</h4>
                                    <ol class="m-b-0 helper-text">
                                        <li>Prefer a server cron that runs every minute.</li>
                                        <li>Use the recommended <code>schedule:run</code> command if shell cron is available.</li>
                                        <li>If shell cron is unavailable, paste the secure URL trigger into your hosting cron URL field.</li>
                                        <li>After setup, click <strong>Run Cron Now</strong> once to confirm status changes on this page.</li>
                                        <li>When cron is healthy, due scheduled campaigns should start moving automatically.</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Campaign Queue Snapshot</h4>
                                    <table class="table table-bordered m-b-0">
                                        <tbody>
                                            <tr>
                                                <th style="width: 220px;">Total Scheduled</th>
                                                <td>{{ $campaignStats['scheduled_total'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Due Scheduled Right Now</th>
                                                <td>{{ $campaignStats['scheduled_due'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Running</th>
                                                <td>{{ $campaignStats['running_total'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Failed</th>
                                                <td>{{ $campaignStats['failed_total'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card-box">
                            <h4 class="header-title m-t-0">Latest Campaign Activity</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Campaign</th>
                                            <th>User</th>
                                            <th>Status</th>
                                            <th>Scheduled At</th>
                                            <th>Progress</th>
                                            <th>Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestCampaigns as $campaign)
                                            <tr>
                                                <td>{{ $campaign->name }}</td>
                                                <td>{{ optional($campaign->user)->name ?: '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $campaign->getStatusBadgeClass() }}">
                                                        {{ ucfirst($campaign->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y h:i A') : '-' }}</td>
                                                <td>{{ $campaign->processed_contacts }}/{{ $campaign->total_contacts }}</td>
                                                <td>{{ $campaign->updated_at ? $campaign->updated_at->format('M d, Y h:i A') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No promotional campaigns found yet.</td>
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
    @include("admin.copyright")
</div>

<script type="text/javascript">
    function cronMonitorToast(icon, title) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            icon: icon,
            title: title
        });
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('.copy-btn');

        if (!button) {
            return;
        }

        var value = button.getAttribute('data-copy') || '';

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(value).then(function () {
                cronMonitorToast('success', 'Copied to clipboard');
            }).catch(function () {
                cronMonitorToast('error', 'Copy failed');
            });
            return;
        }

        var textarea = document.createElement('textarea');
        textarea.value = value;
        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            cronMonitorToast('success', 'Copied to clipboard');
        } catch (error) {
            cronMonitorToast('error', 'Copy failed');
        }

        document.body.removeChild(textarea);
    });

    @if(Session::has('flash_message'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'success',
        title: '{{ Session::get('flash_message') }}'
    });
    @endif

    @if(Session::has('error_flash_message'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        icon: 'error',
        title: '{{ Session::get('error_flash_message') }}'
    });
    @endif
</script>
@endsection
