@extends('site_app')

@section('head_title', 'Promotion Services | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Promotion Services</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li>Promotion Services</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @include('pages.user.promotions._nav')

        <div class="row">
            <div class="col-md-3">
                <div class="promotion-card">
                    <div class="promotion-stat-value">{{ $listsCount }}</div>
                    <div class="promotion-stat-label">Email Lists</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="promotion-card">
                    <div class="promotion-stat-value">{{ $contactsCount }}</div>
                    <div class="promotion-stat-label">Contacts</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="promotion-card">
                    <div class="promotion-stat-value">{{ $campaignsCount }}</div>
                    <div class="promotion-stat-label">Campaigns</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="promotion-card">
                    <div class="promotion-stat-value">{{ $runningCampaignsCount }}</div>
                    <div class="promotion-stat-label">Running Campaigns</div>
                </div>
            </div>
        </div>

        <div class="promotion-panel">
            <div class="row">
                <div class="col-md-8">
                    <h3 style="color:#fff;margin-top:0;">Email Promotion Workspace</h3>
                    <p class="promotion-help-text">Build contact lists, compose rich email campaigns with TinyMCE, and send them through the promotional SMTP servers configured by the admin team.</p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ URL::to('promotions/lists') }}" class="btn btn-default">Manage Lists</a>
                    <a href="{{ URL::to('promotions/campaigns/create') }}" class="btn btn-danger">Create Campaign</a>
                </div>
            </div>
        </div>

        <div class="promotion-panel">
            <h3 style="color:#fff;margin-top:0;">Recent Campaigns</h3>
            <div class="table-responsive">
                <table class="table promotion-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>List</th>
                            <th>Status</th>
                            <th>Progress</th>
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
                                <td>{{ $campaign->processed_contacts }}/{{ $campaign->total_contacts }}</td>
                                <td class="text-right"><a href="{{ URL::to('promotions/campaigns/'.$campaign->id) }}" class="btn btn-sm btn-danger">Open</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No campaigns created yet.</td>
                            </tr>
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
