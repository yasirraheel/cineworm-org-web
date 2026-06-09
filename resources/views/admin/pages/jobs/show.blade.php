@extends("admin.admin_app")

@section("content")
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ URL::to('admin/job_listings') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                        <h4 class="page-title">{{ $page_title }}</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">{{ $job->title }}</h4>

                            <div class="row mb-3">
                                <div class="col-md-3"><strong>User:</strong></div>
                                <div class="col-md-9">{{ optional($job->user)->name }} ({{ optional($job->user)->email }})</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Company:</strong></div>
                                <div class="col-md-9">{{ $job->company }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Location:</strong></div>
                                <div class="col-md-9">{{ $job->location }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Salary:</strong></div>
                                <div class="col-md-9">{{ $job->salary ?? 'N/A' }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Contact Details:</strong></div>
                                <div class="col-md-9">{{ $job->contact_details ?? 'N/A' }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Status:</strong></div>
                                <div class="col-md-9">
                                    @if($job->status == 1)
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Description:</strong></div>
                                <div class="col-md-9">{!! nl2br(e($job->description)) !!}</div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
