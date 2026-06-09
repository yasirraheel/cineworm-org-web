@extends('site_app')

@section('head_title', 'Email Campaigns | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>Email Campaigns</h2>
        <nav id="breadcrumbs"><ul>
            <li><a href="{{ URL::to('/') }}">Home</a></li>
            <li><a href="{{ URL::to('promotions') }}">Promotions</a></li>
            <li>Campaigns</li>
        </ul></nav>
    </div></div></div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                @include('pages.user._sidebar')
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    @include('pages.user.promotions._nav')

        <div class="promo-panel">
            <div class="promo-panel-header">
                <div>
                    <h3>Your Campaigns</h3>
                    <p class="promo-subtitle">Manage drafted, scheduled, running and completed email campaigns.</p>
                </div>
                <div class="promo-panel-actions">
                    <a href="{{ URL::to('promotions/campaigns/create') }}" class="promo-btn promo-btn-primary">
                        <i class="fa fa-plus"></i> New Campaign
                    </a>
                </div>
            </div>

            <div class="promo-table-wrap">
                <table class="promo-table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
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
                            @php
                                $badgeMap = [
                                    'success'  => 'promo-badge-success',
                                    'danger'   => 'promo-badge-danger',
                                    'warning'  => 'promo-badge-warning',
                                    'info'     => 'promo-badge-info',
                                    'default'  => 'promo-badge-default',
                                    'primary'  => 'promo-badge-info',
                                ];
                                $bClass = $badgeMap[$campaign->getStatusBadgeClass()] ?? 'promo-badge-default';
                            @endphp
                            <tr>
                                <td>
                                    <div class="promo-table-name">
                                        <strong>{{ $campaign->name }}</strong>
                                        <div class="promo-table-sub">{{ Str::limit($campaign->subject, 55) }}</div>
                                    </div>
                                </td>
                                <td style="color:rgba(255,255,255,0.6);">{{ optional($campaign->contactList)->name ?: '—' }}</td>
                                <td>
                                    <span class="promo-badge {{ $bClass }}">
                                        <span class="promo-badge-dot"></span>
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                </td>
                                <td style="color:#10b981;font-weight:600;">{{ $campaign->success_count }}</td>
                                <td style="color:{{ $campaign->failed_count > 0 ? '#ef4444' : 'rgba(255,255,255,0.4)' }};font-weight:600;">{{ $campaign->failed_count }}</td>
                                <td style="color:rgba(255,255,255,0.55);font-size:13px;">
                                    @if($campaign->scheduled_at)
                                        <i class="fa fa-clock-o" style="margin-right:5px;"></i>{{ $campaign->scheduled_at->format('M d, Y H:i') }}
                                    @else
                                        <span style="color:rgba(255,255,255,0.28);">Immediate</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="promo-table-actions">
                                        <a href="{{ URL::to('promotions/campaigns/'.$campaign->id) }}" class="promo-btn promo-btn-ghost promo-btn-sm">
                                            <i class="fa fa-eye"></i> Open
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="promo-table-empty">
                                    <i class="fa fa-paper-plane" style="font-size:32px;display:block;margin-bottom:14px;opacity:0.18;"></i>
                                    No campaigns created yet.<br>
                                    <a href="{{ URL::to('promotions/campaigns/create') }}" style="color:#ff0f28;font-weight:600;">Create your first campaign →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('_particles.pagination', ['paginator' => $campaigns])
        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.user.promotions._flash')
@endsection
