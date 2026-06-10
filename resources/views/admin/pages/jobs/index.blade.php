@extends("admin.admin_app")

@section("content")
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ URL::to('admin/job_listings/add') }}" class="btn btn-danger btn-sm"><i class="fa fa-plus"></i> Add Job</a>
                        </div>
                        <h4 class="page-title">{{ $page_title }}</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            


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
                                                    <a href="{{ URL::to('admin/job_listings/delete/'.$job->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5 m-r-5 data_remove_link" data-toggle="tooltip" title="Delete"><i class="fa fa-remove"></i></a>
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
</div>

<script type="text/javascript">
    @if(Session::has('flash_message'))     
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
      })

      Toast.fire({
        icon: 'success',
        title: '{{ Session::get('flash_message') }}'
      })     
    @endif

    @if (count($errors) > 0)
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '<p>@foreach ($errors->all() as $error) {{$error}}<br/> @endforeach</p>',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background:"#1a2234",
            color:"#fff"
           }) 
    @endif

    $(".data_remove_link").click(function (e) {
      e.preventDefault();
      var href = $(this).attr("href");
      Swal.fire({
        title: '{{trans('words.dlt_warning')}}',
        text: "{{trans('words.dlt_warning_text')}}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{trans('words.dlt_confirm')}}',
        cancelButtonText: "{{trans('words.btn_cancel')}}",
        background:"#1a2234",
        color:"#fff"
      }).then((result) => {
        if(result.isConfirmed) {
           window.location.href = href;
        }
      })
    });
</script>

@endsection
