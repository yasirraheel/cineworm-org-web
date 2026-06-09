@extends("admin.admin_app")

@section("content")
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
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
                                                    <a href="{{ URL::to('admin/job_listings/show/'.$job->id) }}" class="btn btn-icon waves-effect waves-light btn-info m-b-5 m-r-5" data-toggle="tooltip" title="View Details"> <i class="fa fa-eye"></i> </a>
                                                    @if($job->status == 1)
                                                        <a href="{{ URL::to('admin/job_listings/unapprove/'.$job->id) }}" class="btn btn-icon waves-effect waves-light btn-warning m-b-5 m-r-5" data-toggle="tooltip" title="Unapprove"> <i class="fa fa-times"></i> </a>
                                                    @else
                                                        <a href="{{ URL::to('admin/job_listings/approve/'.$job->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5" data-toggle="tooltip" title="Approve"> <i class="fa fa-check"></i> </a>
                                                    @endif
                                                    <a href="{{ URL::to('admin/job_listings/delete/'.$job->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5 m-r-5" onclick="return confirm('Are you sure?');" data-toggle="tooltip" title="Delete"><i class="fa fa-remove"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <nav class="paging_simple_numbers">
                                @include('admin.pagination', ['paginator' => $jobs])
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
