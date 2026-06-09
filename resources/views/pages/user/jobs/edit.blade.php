@extends('site_app')

@section('head_title', 'Edit Job Listing | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Edit Job Listing</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/jobs') }}">My Job Listings</a></li>
                        <li>Edit Job</li>
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
                    
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="edit-profile-form">
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-12">
                                <h3 style="color:#fff;margin-bottom:10px;"><i class="fa fa-pencil" style="color:#e50914;margin-right:8px;"></i> Edit Job Listing</h3>
                            </div>
                        </div>

                        <form method="post" action="{{ URL::to('user/jobs/update/'.$job->id) }}" class="row">
                            @csrf
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group mb-3">
                                    <label>Job Title *</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $job->title) }}" placeholder="e.g. Video Editor" required>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group mb-3">
                                    <label>Company Name *</label>
                                    <input type="text" name="company" class="form-control" value="{{ old('company', $job->company) }}" placeholder="e.g. Warner Bros" required>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group mb-3">
                                    <label>Location *</label>
                                    <input type="text" name="location" class="form-control" value="{{ old('location', $job->location) }}" placeholder="e.g. Los Angeles, CA or Remote" required>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group mb-3">
                                    <label>Salary (optional)</label>
                                    <input type="text" name="salary" class="form-control" value="{{ old('salary', $job->salary) }}" placeholder="e.g. $50k - $70k">
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group mb-3">
                                    <label>Job Description</label>
                                    <textarea name="description" class="form-control" rows="8" placeholder="Describe the job requirements and responsibilities...">{{ old('description', $job->description) }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-30 text-right" style="text-align: right;">
                                <a href="{{ URL::to('user/jobs') }}" class="btn btn-default" style="color: #fff; border: 1px solid #fff; padding: 10px 20px; text-transform: uppercase; margin-right: 10px; text-decoration: none;">Cancel</a>
                                <button type="submit" class="vfx-item-btn-danger text-uppercase">Update Listing</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
