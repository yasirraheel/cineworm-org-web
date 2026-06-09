@extends('site_app')

@section('head_title', 'Job Details | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Job Details</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('/user/jobs') }}">My Job Listings</a></li>
                        <li>Job Details</li>
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
                    <div class="edit-profile-form">
                        
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-6">
                                <h3 style="color:#fff;margin-bottom:5px;">{{ $job->title }}</h3>
                                <p style="color:#ccc;font-size:14px;">{{ $job->company }}</p>
                            </div>
                            <div class="col-md-6 text-right" style="text-align: right; padding-top: 10px;">
                                <a href="{{ URL::to('user/jobs') }}" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none; background: #555;">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                                <a href="{{ URL::to('user/jobs/edit/'.$job->id) }}" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none;">
                                    <i class="fa fa-edit"></i> Edit Job
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive" style="margin-top: 20px;">
                            <table class="table table-bordered" style="color: #fff; border-color: rgba(255,255,255,0.1);">
                                <tbody>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); width: 25%; color: #ccc;">Title</th>
                                        <td style="border-color: rgba(255,255,255,0.1);">{{ $job->title }}</td>
                                    </tr>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color: #ccc;">Company</th>
                                        <td style="border-color: rgba(255,255,255,0.1);">{{ $job->company }}</td>
                                    </tr>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color: #ccc;">Location</th>
                                        <td style="border-color: rgba(255,255,255,0.1);">{{ $job->location }}</td>
                                    </tr>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color: #ccc;">Salary</th>
                                        <td style="border-color: rgba(255,255,255,0.1);">{{ $job->salary ? html_entity_decode(getCurrencySymbols(getcong('currency_code'))).' '.$job->salary : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color: #ccc;">Contact Details</th>
                                        <td style="border-color: rgba(255,255,255,0.1);">{{ $job->contact_details ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color: #ccc;">Status</th>
                                        <td style="border-color: rgba(255,255,255,0.1);">
                                            @if($job->status == 1)
                                                <span class="badge" style="background-color: #28a745; padding: 5px 10px;">Approved</span>
                                            @else
                                                <span class="badge" style="background-color: #ffc107; color:#000; padding: 5px 10px;">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color: #ccc; vertical-align: top;">Description</th>
                                        <td style="border-color: rgba(255,255,255,0.1);">{!! nl2br(e($job->description)) !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
