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

        {{-- ── Stat Cards ── --}}
        <div class="promo-stat-grid">
            <div class="promo-stat-card">
                <div class="promo-stat-icon"><i class="fa fa-list-ul"></i></div>
                <div class="promo-stat-value">{{ $listsCount }}</div>
                <div class="promo-stat-label">Email Lists</div>
            </div>
            <div class="promo-stat-card">
                <div class="promo-stat-icon"><i class="fa fa-users"></i></div>
                <div class="promo-stat-value">{{ $contactsCount }}</div>
                <div class="promo-stat-label">Total Contacts</div>
            </div>
            <div class="promo-stat-card">
                <div class="promo-stat-icon"><i class="fa fa-paper-plane"></i></div>
                <div class="promo-stat-value">{{ $campaignsCount }}</div>
                <div class="promo-stat-label">Campaigns</div>
            </div>
            <div class="promo-stat-card">
                <div class="promo-stat-icon" style="background:rgba(16,185,129,0.14);color:#10b981;"><i class="fa fa-bolt"></i></div>
                <div class="promo-stat-value">{{ $runningCampaignsCount }}</div>
                <div class="promo-stat-label">Running Now</div>
            </div>
        </div>

        {{-- ── Quick Actions ── --}}
        <div class="promo-panel">
            <div class="promo-panel-header">
                <div>
                    <h3>Email Promotion Workspace</h3>
                    <p class="promo-subtitle">Build contact lists, compose campaigns with TinyMCE, and send via admin-configured SMTP servers.</p>
                </div>
                <div class="promo-panel-actions">
                    <a href="{{ URL::to('promotions/lists') }}" class="promo-btn promo-btn-ghost">
                        <i class="fa fa-list-ul"></i> Manage Lists
                    </a>
                    <a href="{{ URL::to('promotions/campaigns/create') }}" class="promo-btn promo-btn-primary">
                        <i class="fa fa-plus"></i> Create Campaign
                    </a>
                </div>
            </div>

            <hr class="promo-divider">

            {{-- Workflow steps --}}
            <div class="row" style="margin-left:-10px;margin-right:-10px;">
                <div class="col-md-4" style="padding:0 10px;margin-bottom:12px;">
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:18px 20px;height:100%;">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,15,40,0.18);color:#ff0f28;font-weight:800;font-size:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <strong style="color:#fff;font-size:14px;">Create a List</strong>
                        </div>
                        <p style="color:rgba(255,255,255,0.45);font-size:13px;margin:0;">Go to <strong style="color:rgba(255,255,255,0.7);">Email Lists</strong> and create a named group for your contacts.</p>
                    </div>
                </div>
                <div class="col-md-4" style="padding:0 10px;margin-bottom:12px;">
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:18px 20px;height:100%;">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,15,40,0.18);color:#ff0f28;font-weight:800;font-size:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                            <strong style="color:#fff;font-size:14px;">Add Contacts</strong>
                        </div>
                        <p style="color:rgba(255,255,255,0.45);font-size:13px;margin:0;">Open a list and add contacts one by one, or bulk-import via CSV file.</p>
                    </div>
                </div>
                <div class="col-md-4" style="padding:0 10px;margin-bottom:12px;">
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:18px 20px;height:100%;">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,15,40,0.18);color:#ff0f28;font-weight:800;font-size:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">3</div>
                            <strong style="color:#fff;font-size:14px;">Launch Campaign</strong>
                        </div>
                        <p style="color:rgba(255,255,255,0.45);font-size:13px;margin:0;">Create a campaign, write your email, pick a list and SMTP server, then launch or schedule it.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Recent Campaigns ── --}}
        <div class="promo-panel">
            <div class="promo-panel-header">
                <div>
                    <h3>Recent Campaigns</h3>
                    <p class="promo-subtitle">Your latest 5 campaigns at a glance.</p>
                </div>
                <div class="promo-panel-actions">
                    <a href="{{ URL::to('promotions/campaigns') }}" class="promo-btn promo-btn-ghost promo-btn-sm">View All</a>
                </div>
            </div>
            <div class="promo-table-wrap">
                <table class="promo-table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>List</th>
                            <th>Status</th>
                            <th>Progress</th>
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
                                $pct = $campaign->total_contacts > 0 ? round($campaign->processed_contacts / $campaign->total_contacts * 100) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="promo-table-name">
                                        <strong>{{ $campaign->name }}</strong>
                                        <div class="promo-table-sub">{{ Str::limit($campaign->subject, 50) }}</div>
                                    </div>
                                </td>
                                <td style="color:rgba(255,255,255,0.6);">{{ optional($campaign->contactList)->name ?: '—' }}</td>
                                <td>
                                    <span class="promo-badge {{ $bClass }}">
                                        <span class="promo-badge-dot"></span>
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                </td>
                                <td style="min-width:110px;">
                                    <div style="margin-bottom:5px;font-size:12px;color:rgba(255,255,255,0.5);">{{ $campaign->processed_contacts }}/{{ $campaign->total_contacts }}</div>
                                    <div class="promo-progress-bar-wrap">
                                        <div class="promo-progress-bar" style="width:{{ $pct }}%"></div>
                                    </div>
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
                                <td colspan="5" class="promo-table-empty">
                                    <i class="fa fa-paper-plane" style="font-size:28px;display:block;margin-bottom:12px;opacity:0.2;"></i>
                                    No campaigns yet. <a href="{{ URL::to('promotions/campaigns/create') }}" style="color:#ff0f28;">Create your first campaign →</a>
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

@include('pages.user.promotions._flash')
@endsection
