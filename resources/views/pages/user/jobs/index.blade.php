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

<div class="edit-profile-area vfx-item-ptb vfx-item-info">
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

                    <div class="edit-profile-form">
                        
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-6">
                                <h3 style="color:#fff;margin-bottom:5px;"><i class="fa fa-briefcase" style="color:#e50914;margin-right:8px;"></i> Job Listings</h3>
                                <p style="color:#ccc;font-size:14px;">Manage your job listings. All jobs require admin approval.</p>
                            </div>
                            <div class="col-md-6 text-right" style="text-align: right; padding-top: 10px;">
                                <a href="{{ URL::to('user/jobs/create') }}" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none;">
                                    <i class="fa fa-plus"></i> Post a Job
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" style="color: #fff; border-color: rgba(255,255,255,0.1);">
                                <thead>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1);">Title</th>
                                        <th style="border-color: rgba(255,255,255,0.1);">Company</th>
                                        <th style="border-color: rgba(255,255,255,0.1);">Location</th>
                                        <th style="border-color: rgba(255,255,255,0.1);">Status</th>
                                        <th style="border-color: rgba(255,255,255,0.1);">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jobs as $job)
                                        <tr>
                                            <td style="border-color: rgba(255,255,255,0.1);">{{ $job->title }}</td>
                                            <td style="border-color: rgba(255,255,255,0.1);">{{ $job->company }}</td>
                                            <td style="border-color: rgba(255,255,255,0.1);">{{ $job->location }}</td>
                                            <td style="border-color: rgba(255,255,255,0.1);">
                                                @if($job->status == 1)
                                                    <span class="badge" style="background-color: #28a745; padding: 5px 10px;">Approved</span>
                                                @else
                                                    <span class="badge" style="background-color: #ffc107; color:#000; padding: 5px 10px;">Pending</span>
                                                @endif
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.1);">
                                                <a href="{{ URL::to('user/jobs/edit/'.$job->id) }}" class="btn btn-sm btn-info" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="{{ URL::to('user/jobs/delete/'.$job->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job listing?');" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center" style="border-color: rgba(255,255,255,0.1); padding: 30px;">
                                                <i class="fa fa-briefcase" style="font-size:32px;display:block;margin-bottom:14px;opacity:0.2;"></i>
                                                No job listings yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div style="margin-top:20px;">
                            @include('_particles.pagination', ['paginator' => $jobs])
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
