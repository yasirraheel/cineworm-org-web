@extends('site_app')

@section('head_title', $campaign->name.' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12"><h2>{{ $campaign->name }}</h2></div></div></div>
</div>
<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @include('pages.user.promotions._nav')

        <div class="promotion-panel">
            <div class="row">
                <div class="col-md-8">
                    <h3 style="color:#fff;margin-top:0;">{{ $campaign->name }}</h3>
                    <p class="promotion-help-text">{{ $campaign->subject }}</p>
                    <p><span class="label label-{{ $campaign->getStatusBadgeClass() }}">{{ ucfirst($campaign->status) }}</span></p>
                </div>
                <div class="col-md-4 text-right">
                    @if(in_array($campaign->status, [\App\PromotionalCampaign::STATUS_DRAFT, \App\PromotionalCampaign::STATUS_SCHEDULED], true))
                        <a href="{{ URL::to('promotions/campaigns/edit/'.$campaign->id) }}" class="btn btn-default">Edit</a>
                        <form action="{{ URL::to('promotions/campaigns/'.$campaign->id.'/launch') }}" method="post" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-danger">{{ $campaign->scheduled_at ? 'Schedule Campaign' : 'Launch Now' }}</button>
                        </form>
                    @elseif($campaign->status === \App\PromotionalCampaign::STATUS_RUNNING)
                        <form action="{{ URL::to('promotions/campaigns/'.$campaign->id.'/pause') }}" method="post" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-warning">Pause</button>
                        </form>
                    @elseif($campaign->status === \App\PromotionalCampaign::STATUS_PAUSED)
                        <form action="{{ URL::to('promotions/campaigns/'.$campaign->id.'/resume') }}" method="post" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Resume</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><div class="promotion-card"><div class="promotion-stat-value">{{ $campaign->total_contacts }}</div><div class="promotion-stat-label">Total Contacts</div></div></div>
            <div class="col-md-3"><div class="promotion-card"><div class="promotion-stat-value">{{ $campaign->processed_contacts }}</div><div class="promotion-stat-label">Processed</div></div></div>
            <div class="col-md-3"><div class="promotion-card"><div class="promotion-stat-value">{{ $campaign->success_count }}</div><div class="promotion-stat-label">Sent</div></div></div>
            <div class="col-md-3"><div class="promotion-card"><div class="promotion-stat-value">{{ $campaign->failed_count }}</div><div class="promotion-stat-label">Failed</div></div></div>
        </div>

        <div class="promotion-panel">
            <div class="row">
                <div class="col-md-6">
                    <p><strong style="color:#fff;">List:</strong> <span class="promotion-help-text">{{ optional($campaign->contactList)->name ?: '-' }}</span></p>
                    <p><strong style="color:#fff;">SMTP Server:</strong> <span class="promotion-help-text">{{ optional($campaign->smtpServer)->server_name ?: '-' }}</span></p>
                    <p><strong style="color:#fff;">Sending Domain:</strong> <span class="promotion-help-text">{{ optional($campaign->sendingDomain)->domain ?: '-' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong style="color:#fff;">From:</strong> <span class="promotion-help-text">{{ $campaign->from_name }} &lt;{{ $campaign->from_email }}&gt;</span></p>
                    <p><strong style="color:#fff;">Scheduled At:</strong> <span class="promotion-help-text">{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y H:i') : 'Send immediately' }}</span></p>
                    <p><strong style="color:#fff;">Progress:</strong> <span class="promotion-help-text">{{ $campaign->getProgressPercentage() }}%</span></p>
                </div>
            </div>
            @if($campaign->last_error)
                <div class="alert alert-danger" style="margin-top:15px;">{{ $campaign->last_error }}</div>
            @endif
        </div>

        <div class="promotion-panel">
            <h3 style="color:#fff;margin-top:0;">Recent Delivery Log</h3>
            <div class="table-responsive">
                <table class="table promotion-table">
                    <thead>
                        <tr>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSends as $send)
                            <tr>
                                <td>{{ optional($send->contact)->name ?: '-' }}</td>
                                <td>{{ $send->email }}</td>
                                <td><span class="label label-{{ $send->status === 'sent' ? 'success' : ($send->status === 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($send->status) }}</span></td>
                                <td>{{ $send->sent_at ? $send->sent_at->format('M d, Y H:i') : '-' }}</td>
                                <td>{{ $send->error_message ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No send logs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('_particles.pagination', ['paginator' => $recentSends])
        </div>
    </div>
</div>
@include('pages.user.promotions._flash')
@endsection
