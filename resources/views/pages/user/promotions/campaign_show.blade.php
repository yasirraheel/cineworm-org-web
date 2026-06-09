@extends('site_app')

@section('head_title', $campaign->name.' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>{{ $campaign->name }}</h2>
        <nav id="breadcrumbs"><ul>
            <li><a href="{{ URL::to('/') }}">Home</a></li>
            <li><a href="{{ URL::to('promotions') }}">Promotions</a></li>
            <li><a href="{{ URL::to('promotions/campaigns') }}">Campaigns</a></li>
            <li>{{ Str::limit($campaign->name, 30) }}</li>
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

        {{-- ── Header ── --}}
        <div class="promo-panel">
            <div class="promo-panel-header">
                <div>
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:6px;">
                        <h3 style="margin:0;">{{ $campaign->name }}</h3>
                        <span class="promo-badge {{ $bClass }}">
                            <span class="promo-badge-dot"></span>
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <p class="promo-subtitle">{{ $campaign->subject }}</p>
                </div>
                <div class="promo-panel-actions">
                    @if(in_array($campaign->status, [\App\PromotionalCampaign::STATUS_DRAFT, \App\PromotionalCampaign::STATUS_SCHEDULED], true))
                        <a href="{{ URL::to('promotions/campaigns/edit/'.$campaign->id) }}" class="promo-btn promo-btn-ghost">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                        <form action="{{ URL::to('promotions/campaigns/'.$campaign->id.'/launch') }}" method="post" style="display:inline;">
                            @csrf
                            <button type="submit" class="promo-btn promo-btn-primary">
                                <i class="fa fa-rocket"></i>
                                {{ $campaign->scheduled_at ? 'Schedule Campaign' : 'Launch Now' }}
                            </button>
                        </form>
                    @elseif($campaign->status === \App\PromotionalCampaign::STATUS_RUNNING)
                        <form action="{{ URL::to('promotions/campaigns/'.$campaign->id.'/pause') }}" method="post" style="display:inline;">
                            @csrf
                            <button type="submit" class="promo-btn promo-btn-warning">
                                <i class="fa fa-pause"></i> Pause Campaign
                            </button>
                        </form>
                    @elseif($campaign->status === \App\PromotionalCampaign::STATUS_PAUSED)
                        <form action="{{ URL::to('promotions/campaigns/'.$campaign->id.'/resume') }}" method="post" style="display:inline;">
                            @csrf
                            <button type="submit" class="promo-btn promo-btn-primary">
                                <i class="fa fa-play"></i> Resume Campaign
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Stats ── --}}
        <div class="promo-stat-grid" style="grid-template-columns:repeat(4,1fr);">
            <div class="promo-stat-card">
                <div class="promo-stat-icon"><i class="fa fa-users"></i></div>
                <div class="promo-stat-value">{{ $campaign->total_contacts }}</div>
                <div class="promo-stat-label">Total Contacts</div>
            </div>
            <div class="promo-stat-card">
                <div class="promo-stat-icon" style="background:rgba(99,102,241,0.14);color:#818cf8;"><i class="fa fa-spin fa-refresh" style="{{ $campaign->status === 'running' ? '' : 'animation:none;' }}"></i></div>
                <div class="promo-stat-value">{{ $campaign->processed_contacts }}</div>
                <div class="promo-stat-label">Processed</div>
            </div>
            <div class="promo-stat-card">
                <div class="promo-stat-icon" style="background:rgba(16,185,129,0.14);color:#10b981;"><i class="fa fa-check"></i></div>
                <div class="promo-stat-value">{{ $campaign->success_count }}</div>
                <div class="promo-stat-label">Sent Successfully</div>
            </div>
            <div class="promo-stat-card">
                <div class="promo-stat-icon" style="background:rgba(239,68,68,0.14);color:#ef4444;"><i class="fa fa-times"></i></div>
                <div class="promo-stat-value">{{ $campaign->failed_count }}</div>
                <div class="promo-stat-label">Failed</div>
            </div>
        </div>

        {{-- ── Progress ── --}}
        @php $pct = $campaign->total_contacts > 0 ? round($campaign->processed_contacts / $campaign->total_contacts * 100) : 0; @endphp
        <div class="promo-panel" style="padding:22px 28px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <span style="color:rgba(255,255,255,0.55);font-size:13px;font-weight:600;">Overall Progress</span>
                <span style="color:#fff;font-weight:700;font-size:14px;">{{ $pct }}%</span>
            </div>
            <div class="promo-progress-bar-wrap" style="height:10px;">
                <div class="promo-progress-bar" style="width:{{ $pct }}%;"></div>
            </div>
        </div>

        {{-- ── Campaign Info ── --}}
        <div class="promo-panel">
            <h3 style="color:#fff;margin-top:0;margin-bottom:20px;font-size:16px;">
                <i class="fa fa-info-circle" style="color:#ff0f28;margin-right:8px;"></i>Campaign Information
            </h3>
            <div class="promo-meta-grid">
                <div class="promo-meta-item">
                    <div class="promo-meta-key">Email List</div>
                    <div class="promo-meta-val">{{ optional($campaign->contactList)->name ?: '—' }}</div>
                </div>
                <div class="promo-meta-item">
                    <div class="promo-meta-key">From</div>
                    <div class="promo-meta-val">{{ $campaign->from_name }} &lt;{{ $campaign->from_email }}&gt;</div>
                </div>
                <div class="promo-meta-item">
                    <div class="promo-meta-key">SMTP Server</div>
                    <div class="promo-meta-val">{{ optional($campaign->smtpServer)->server_name ?: '—' }}</div>
                </div>
                <div class="promo-meta-item">
                    <div class="promo-meta-key">Scheduled At</div>
                    <div class="promo-meta-val">{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y H:i') : 'Send Immediately' }}</div>
                </div>
                <div class="promo-meta-item">
                    <div class="promo-meta-key">Sending Domain</div>
                    <div class="promo-meta-val">{{ optional($campaign->sendingDomain)->domain ?: '—' }}</div>
                </div>
                <div class="promo-meta-item">
                    <div class="promo-meta-key">Tracking Domain</div>
                    <div class="promo-meta-val">{{ optional($campaign->trackingDomain)->domain ?: '—' }}</div>
                </div>
            </div>
            @if($campaign->last_error)
                <div class="promo-alert promo-alert-danger" style="margin-top:20px;">
                    <i class="fa fa-exclamation-triangle" style="flex-shrink:0;"></i>
                    <span>{{ $campaign->last_error }}</span>
                </div>
            @endif
        </div>

        {{-- ── Delivery Log ── --}}
        <div class="promo-panel">
            <div class="promo-panel-header">
                <div>
                    <h3><i class="fa fa-history" style="color:#ff0f28;margin-right:8px;"></i>Recent Delivery Log</h3>
                    <p class="promo-subtitle">Track recent sends, failures, and delivery activity for this campaign.</p>
                </div>
            </div>
            <div class="promo-table-wrap">
                <table class="promo-table">
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
                            @php
                                $sBadge = $send->status === 'sent'
                                    ? 'promo-badge-success'
                                    : ($send->status === 'failed' ? 'promo-badge-danger' : 'promo-badge-warning');
                            @endphp
                            <tr>
                                <td style="color:rgba(255,255,255,0.75);">{{ optional($send->contact)->name ?: '—' }}</td>
                                <td style="color:rgba(255,255,255,0.55);font-size:13px;">{{ $send->email }}</td>
                                <td>
                                    <span class="promo-badge {{ $sBadge }}">
                                        <span class="promo-badge-dot"></span>
                                        {{ ucfirst($send->status) }}
                                    </span>
                                </td>
                                <td style="color:rgba(255,255,255,0.45);font-size:13px;">{{ $send->sent_at ? $send->sent_at->format('M d, H:i') : '—' }}</td>
                                <td style="color:#ef4444;font-size:12.5px;max-width:220px;word-break:break-word;">{{ $send->error_message ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="promo-table-empty">
                                    <i class="fa fa-history" style="font-size:28px;display:block;margin-bottom:12px;opacity:0.2;"></i>
                                    No send logs yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('_particles.pagination', ['paginator' => $recentSends])
        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.user.promotions._flash')
@endsection
