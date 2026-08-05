@extends('site_app')

@section('head_title', 'WhatsApp Campaigns | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>WhatsApp Campaigns</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/whatsapp') }}">WhatsApp</a></li>
                        <li>Campaigns</li>
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

                    <div class="card mb-4" style="background:#161b26;border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:20px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 style="color:#fff;font-weight:700;margin:0;"><i class="fa fa-paper-plane"></i> WhatsApp Campaigns</h4>
                            <a href="{{ URL::to('user/whatsapp/campaigns/create') }}" class="btn btn-success" style="background:#25D366;border-color:#25D366;font-weight:600;">
                                <i class="fa fa-plus"></i> Create Campaign
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-bordered" style="font-size:14px;">
                                <thead>
                                    <tr>
                                        <th>Campaign Title</th>
                                        <th>Target List</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($campaigns as $campaign)
                                        <tr>
                                            <td><strong>{{ $campaign->title }}</strong></td>
                                            <td>{{ $campaign->contactList->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="progress mb-1" style="height:16px;background:rgba(255,255,255,0.1);">
                                                    @php
                                                        $pct = $campaign->total_contacts > 0 ? round(($campaign->processed_contacts / $campaign->total_contacts) * 100) : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">{{ $pct }}%</div>
                                                </div>
                                                <small class="text-muted">{{ $campaign->processed_contacts }} / {{ $campaign->total_contacts }} ({{ $campaign->success_count }} sent, {{ $campaign->failed_count }} failed)</small>
                                            </td>
                                            <td>
                                                @if($campaign->status == 'running')
                                                    <span class="badge bg-success">Running</span>
                                                @elseif($campaign->status == 'completed')
                                                    <span class="badge bg-info text-dark">Completed</span>
                                                @elseif($campaign->status == 'paused')
                                                    <span class="badge bg-warning text-dark">Paused</span>
                                                @elseif($campaign->status == 'scheduled')
                                                    <span class="badge bg-primary">Scheduled</span>
                                                @else
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $campaign->created_at ? $campaign->created_at->format('Y-m-d H:i') : 'N/A' }}</small></td>
                                            <td>
                                                <a href="{{ URL::to('user/whatsapp/campaigns/'.$campaign->id) }}" class="btn btn-sm btn-info me-1" title="View Delivery Log"><i class="fa fa-eye"></i></a>
                                                @if(in_array($campaign->status, ['draft', 'paused'], true))
                                                    <form action="{{ URL::to('user/whatsapp/campaigns/'.$campaign->id.'/launch') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success me-1" title="Launch Campaign" onclick="return confirm('Launch this WhatsApp campaign now?')"><i class="fa fa-play"></i></button>
                                                    </form>
                                                @elseif($campaign->status == 'running')
                                                    <form action="{{ URL::to('user/whatsapp/campaigns/'.$campaign->id.'/pause') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning me-1" title="Pause Campaign"><i class="fa fa-pause"></i></button>
                                                    </form>
                                                @endif
                                                <a href="{{ URL::to('user/whatsapp/campaigns/edit/'.$campaign->id) }}" class="btn btn-sm btn-secondary" title="Edit"><i class="fa fa-edit"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No WhatsApp campaigns created yet. Click "Create Campaign" to launch your first message campaign.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $campaigns->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
