@extends('site_app')

@section('head_title', 'Post a Job | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Post a Job</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/jobs') }}">My Job Listings</a></li>
                        <li>Post a Job</li>
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

                    <div class="promo-panel">
                        <div class="promo-panel-header">
                            <div>
                                <h3><i class="fa fa-plus-circle" style="color:#ff0f28;margin-right:8px;"></i> Create Job Listing</h3>
                            </div>
                        </div>

                        <form method="post" action="{{ URL::to('user/jobs/store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="promo-form-group">
                                        <label class="promo-label">Job Title <span style="color:#ff0f28;">*</span></label>
                                        <input type="text" name="title" class="promo-input form-control" value="{{ old('title') }}" placeholder="e.g. Video Editor" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="promo-form-group">
                                        <label class="promo-label">Company Name <span style="color:#ff0f28;">*</span></label>
                                        <input type="text" name="company" class="promo-input form-control" value="{{ old('company') }}" placeholder="e.g. Warner Bros" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="promo-form-group">
                                        <label class="promo-label">Location <span style="color:#ff0f28;">*</span></label>
                                        <input type="text" name="location" class="promo-input form-control" value="{{ old('location') }}" placeholder="e.g. Los Angeles, CA or Remote" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="promo-form-group">
                                        <label class="promo-label">Salary <span style="color:rgba(255,255,255,0.3);font-weight:500;">(optional)</span></label>
                                        <input type="text" name="salary" class="promo-input form-control" value="{{ old('salary') }}" placeholder="e.g. $50k - $70k">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="promo-form-group">
                                        <label class="promo-label">Job Description</label>
                                        <textarea name="description" class="promo-textarea form-control" rows="8" placeholder="Describe the job requirements and responsibilities...">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:20px;">
                                <a href="{{ URL::to('user/jobs') }}" class="promo-btn promo-btn-ghost">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="promo-btn promo-btn-primary">
                                    <i class="fa fa-paper-plane"></i> Submit for Approval
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
