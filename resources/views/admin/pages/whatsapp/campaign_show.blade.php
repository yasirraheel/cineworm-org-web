@extends("admin.admin_app")

@section("content")
@include('admin.pages.whatsapp.partials.content_styles')

<div class="content-page whatsapp-admin-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        @include('admin.pages.whatsapp.partials.nav')

                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0">{{ $campaign->name }}</h4>
                                <p class="m-b-0">
                                    <span class="badge badge-{{ $campaign->getStatusBadgeClass() }}">{{ ucfirst($campaign->status) }}</span>
                                    {{ optional($campaign->contactList)->name ?: 'List missing' }}
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ URL::to('admin/whatsapp/campaigns/edit/'.$campaign->id) }}" class="btn btn-success">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ URL::to('admin/whatsapp/campaigns') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Campaigns
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $campaign->total_contacts }}</h3>
                                    <p class="m-b-0">Queued</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $campaign->success_count }}</h3>
                                    <p class="m-b-0">Sent</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $campaign->failed_count }}</h3>
                                    <p class="m-b-0">Failed</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card-box whatsapp-metric">
                                    <h3 class="m-t-0">{{ $campaign->getProgressPercentage() }}%</h3>
                                    <p class="m-b-0">Progress</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Controls</h4>
                                    <div class="m-b-15">
                                        @if(in_array($campaign->status, ['draft', 'paused', 'failed'], true))
                                            <form method="post" action="{{ URL::to('admin/whatsapp/campaigns/'.$campaign->id.'/launch') }}" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-play"></i> Launch
                                                </button>
                                            </form>
                                        @endif

                                        @if($campaign->status === 'running')
                                            <form method="post" action="{{ URL::to('admin/whatsapp/campaigns/'.$campaign->id.'/pause') }}" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fa fa-pause"></i> Pause
                                                </button>
                                            </form>
                                            <form method="post" action="{{ URL::to('admin/whatsapp/campaigns/'.$campaign->id.'/process') }}" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-refresh"></i> Process Now
                                                </button>
                                            </form>
                                        @endif

                                        @if($campaign->status === 'paused')
                                            <form method="post" action="{{ URL::to('admin/whatsapp/campaigns/'.$campaign->id.'/resume') }}" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-info">
                                                    <i class="fa fa-play"></i> Resume
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr><th>Batch Size</th><td>{{ $campaign->batch_size }}</td></tr>
                                            <tr><th>Delay</th><td>{{ $campaign->min_delay_seconds }} - {{ $campaign->max_delay_seconds }} seconds</td></tr>
                                            <tr><th>Pause Rule</th><td>{{ $campaign->pause_duration_seconds }} seconds after {{ $campaign->pause_after_messages }} messages</td></tr>
                                            <tr><th>Daily Limit</th><td>{{ $campaign->daily_limit }}</td></tr>
                                            <tr><th>Quiet Hours</th><td>{{ $campaign->quiet_hours_start ?: '-' }} to {{ $campaign->quiet_hours_end ?: '-' }}</td></tr>
                                            <tr><th>Last Error</th><td>{{ $campaign->last_error ?: 'None' }}</td></tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Message Preview</h4>
                                    <div style="white-space: pre-wrap; background: #1f2025; border: 1px solid rgba(255,255,255,0.12); padding: 15px; border-radius: 4px;">{{ $campaign->message }}</div>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Delivery Log</h4>
                                    <div class="whatsapp-progress m-b-15">
                                        <span style="width: {{ $campaign->getProgressPercentage() }}%;"></span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Phone</th>
                                                    <th>Contact</th>
                                                    <th>Status</th>
                                                    <th>Attempts</th>
                                                    <th>Error</th>
                                                    <th>Updated</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($recentSends as $send)
                                                <tr>
                                                    <td>{{ $send->phone }}</td>
                                                    <td>{{ optional($send->contact)->name ?: '-' }}</td>
                                                    <td><span class="badge badge-{{ $send->status === 'sent' ? 'success' : ($send->status === 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($send->status) }}</span></td>
                                                    <td>{{ $send->attempts }}</td>
                                                    <td>{{ $send->error_message ?: '-' }}</td>
                                                    <td>{{ $send->updated_at ? $send->updated_at->format('M d, H:i') : '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center">No delivery rows yet. Launch the campaign to build the queue.</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav class="paging_simple_numbers">
                                        @include('admin.pagination', ['paginator' => $recentSends])
                                    </nav>
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

@include('admin.pages.whatsapp.partials.flash')
@endsection
