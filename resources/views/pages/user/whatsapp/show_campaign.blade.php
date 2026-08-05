@extends('site_app')

@section('head_title', $page_title.' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>{{ $campaign->title }}</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/whatsapp') }}">WhatsApp</a></li>
                        <li><a href="{{ URL::to('user/whatsapp/campaigns') }}">Campaigns</a></li>
                        <li>Details</li>
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

                    @if(Session::has('flash_message'))
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            {{ Session::get('flash_message') }}
                        </div>
                    @endif

                    <div class="card mb-4" style="background:#161b26;border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:25px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 style="color:#25D366;font-weight:700;margin:0;">
                                <i class="fa fa-paper-plane"></i> {{ $campaign->title }}
                            </h4>
                            <div>
                                @if(in_array($campaign->status, ['draft', 'paused'], true))
                                    <form action="{{ URL::to('user/whatsapp/campaigns/'.$campaign->id.'/launch') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success me-2" style="background:#25D366;border-color:#25D366;font-weight:600;" onclick="return confirm('Launch this campaign now?')">
                                            <i class="fa fa-play"></i> Launch Campaign
                                        </button>
                                    </form>
                                @elseif($campaign->status == 'running')
                                    <form action="{{ URL::to('user/whatsapp/campaigns/'.$campaign->id.'/pause') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning me-2" style="font-weight:600;">
                                            <i class="fa fa-pause"></i> Pause Campaign
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ URL::to('user/whatsapp/campaigns/edit/'.$campaign->id) }}" class="btn btn-secondary"><i class="fa fa-edit"></i> Edit</a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3 col-6 mb-2">
                                <small class="text-muted text-uppercase d-block">Status</small>
                                <strong>{{ ucfirst($campaign->status) }}</strong>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <small class="text-muted text-uppercase d-block">Total Contacts</small>
                                <strong>{{ $campaign->total_contacts }}</strong>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <small class="text-muted text-uppercase d-block">Processed</small>
                                <strong>{{ $campaign->processed_contacts }}</strong>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <small class="text-muted text-uppercase d-block">Success / Failed</small>
                                <span class="text-success font-weight-bold">{{ $campaign->success_count }}</span> / <span class="text-danger font-weight-bold">{{ $campaign->failed_count }}</span>
                            </div>
                        </div>

                        @if($campaign->last_error)
                            <div class="alert alert-warning mb-3">
                                <strong>Last Log Message:</strong> {{ $campaign->last_error }}
                            </div>
                        @endif

                        <h5 style="color:#fff;font-weight:700;" class="mt-4 mb-3"><i class="fa fa-list-alt"></i> Message Delivery Logs</h5>
                        <div class="table-responsive">
                            <table class="table table-dark table-bordered" style="font-size:13px;">
                                <thead>
                                    <tr>
                                        <th>Recipient Phone</th>
                                        <th>Recipient Name</th>
                                        <th>Status</th>
                                        <th>Sent Time</th>
                                        <th>Log / Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sends as $send)
                                        <tr>
                                            <td><strong>+{{ $send->phone }}</strong></td>
                                            <td>{{ $send->contact->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($send->status == 'sent')
                                                    <span class="badge bg-success">Sent</span>
                                                @elseif($send->status == 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $send->sent_at ? $send->sent_at->format('Y-m-d H:i:s') : '-' }}</small></td>
                                            <td><small class="text-muted">{{ $send->error_message ?: 'Delivered' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No message delivery records found. Launch the campaign to start sending messages.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $sends->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
