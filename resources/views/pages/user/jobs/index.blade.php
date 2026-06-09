@extends('site_app')

@section('head_title', 'My Job Listings | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>My Job Listings</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li>My Job Listings</li>
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
                    @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ Session::get('flash_message') }}
                    </div>
                    @endif

                    <div class="promo-panel">
                        <div class="promo-panel-header">
                            <div>
                                <h3><i class="fa fa-briefcase" style="color:#ff0f28;margin-right:8px;"></i> Job Listings</h3>
                                <p class="promo-subtitle">Manage your job listings. All jobs require admin approval before they appear live.</p>
                            </div>
                            <div class="promo-panel-actions">
                                <a href="{{ URL::to('user/jobs/create') }}" class="promo-btn promo-btn-primary">
                                    <i class="fa fa-plus"></i> Post a Job
                                </a>
                            </div>
                        </div>

                        <div class="promo-table-wrap">
                            <table class="promo-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Company</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jobs as $job)
                                        <tr>
                                            <td style="color:rgba(255,255,255,0.85);font-weight:500;">{{ $job->title }}</td>
                                            <td style="color:rgba(255,255,255,0.55);font-size:13px;">{{ $job->company }}</td>
                                            <td style="color:rgba(255,255,255,0.45);font-size:13px;">{{ $job->location }}</td>
                                            <td>
                                                @if($job->status == 1)
                                                    <span class="promo-badge promo-badge-success">
                                                        <span class="promo-badge-dot"></span>
                                                        Approved
                                                    </span>
                                                @else
                                                    <span class="promo-badge promo-badge-warning">
                                                        <span class="promo-badge-dot"></span>
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="promo-table-actions">
                                                    <a href="{{ URL::to('user/jobs/edit/'.$job->id) }}" class="promo-btn promo-btn-ghost promo-btn-sm">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    <a href="{{ URL::to('user/jobs/delete/'.$job->id) }}" class="promo-btn promo-btn-danger-ghost promo-btn-sm" onclick="return confirm('Are you sure you want to delete this job listing?');">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="promo-table-empty">
                                                <i class="fa fa-briefcase" style="font-size:32px;display:block;margin-bottom:14px;opacity:0.18;"></i>
                                                No job listings yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('_particles.pagination', ['paginator' => $jobs])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
