@extends("admin.admin_app")

@section("content")
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ URL::to('admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">{{ $page_title }}</li>
                            </ol>
                        </div>
                        <h4 class="page-title">{{ $page_title }}</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            
                            @if(Session::has('flash_message'))
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ Session::get('flash_message') }}
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>User</th>
                                            <th>Company</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jobs as $job)
                                            <tr>
                                                <td>{{ $job->title }}</td>
                                                <td>{{ optional($job->user)->name }}<br><small>{{ optional($job->user)->email }}</small></td>
                                                <td>{{ $job->company }}</td>
                                                <td>{{ $job->location }}</td>
                                                <td>
                                                    @if($job->status == 1)
                                                        <span class="badge badge-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($job->status == 1)
                                                        <a href="{{ URL::to('admin/job_listings/unapprove/'.$job->id) }}" class="btn btn-warning btn-sm" title="Unapprove"><i class="fa fa-times"></i> Unapprove</a>
                                                    @else
                                                        <a href="{{ URL::to('admin/job_listings/approve/'.$job->id) }}" class="btn btn-success btn-sm" title="Approve"><i class="fa fa-check"></i> Approve</a>
                                                    @endif
                                                    <a href="{{ URL::to('admin/job_listings/delete/'.$job->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');" title="Delete"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                @include('_particles.pagination', ['paginator' => $jobs])
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
