@extends('site_app')

@section('head_title', 'Campaigns | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12"><h2>Email Campaigns</h2></div></div></div>
</div>
<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @include('pages.user.promotions._nav')

        <div class="promotion-panel">
            <div class="promotion-header">
                <div>
                    <h3>Your Campaigns</h3>
                    <p class="promotion-help-text">Manage drafted, scheduled, running, and completed promotional email campaigns.</p>
                </div>
                <div class="promotion-actions">
                    <a href="{{ URL::to('promotions/campaigns/create') }}" class="btn btn-danger">Create Campaign</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table promotion-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>List</th>
                            <th>Status</th>
                            <th>Sent</th>
                            <th>Failed</th>
                            <th>Schedule</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                            <tr>
                                <td>
                                    <strong>{{ $campaign->name }}</strong>
                                    <div class="promotion-help-text">{{ $campaign->subject }}</div>
                                </td>
                                <td>{{ optional($campaign->contactList)->name ?: '-' }}</td>
                                <td><span class="label label-{{ $campaign->getStatusBadgeClass() }}">{{ ucfirst($campaign->status) }}</span></td>
                                <td>{{ $campaign->success_count }}</td>
                                <td>{{ $campaign->failed_count }}</td>
                                <td>{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y H:i') : 'Send Now' }}</td>
                                <td class="text-right">
                                    <a href="{{ URL::to('promotions/campaigns/'.$campaign->id) }}" class="btn btn-sm btn-danger">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No campaigns created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('_particles.pagination', ['paginator' => $campaigns])
        </div>
    </div>
</div>
@include('pages.user.promotions._flash')
@endsection
