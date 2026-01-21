@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">

                        <div class="row">
                            <div class="col-md-3">
                                <h3>Comments List</h3>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Comment</th>
                                        <th>Status</th>
                                        <th>Posted At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($comments_list as $comment)
                                    <tr>
                                        <td>{{ $comment->user->name ?? 'Unknown' }}</td>
                                        <td>
                                            <div style="max-width: 400px; word-wrap: break-word;">
                                                {{ $comment->comment }}
                                                <br>
                                                <small class="text-muted">
                                                    On: {{ $comment->commentable_type }} (ID: {{ $comment->commentable_id }})
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($comment->status == 1)
                                                <span class="badge badge-success">Approved</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $comment->created_at }}</td>
                                        <td>
                                            @if($comment->status == 0)
                                                <a href="{{ url('admin/comments/approve/'.$comment->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5" data-toggle="tooltip" title="Approve"> <i class="fa fa-check"></i> </a>
                                            @else
                                                <a href="{{ url('admin/comments/unapprove/'.$comment->id) }}" class="btn btn-icon waves-effect waves-light btn-warning m-b-5 m-r-5" data-toggle="tooltip" title="Unapprove"> <i class="fa fa-ban"></i> </a>
                                            @endif
                                            <a href="{{ url('admin/comments/delete/'.$comment->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" onclick="return confirm('Are you sure you want to delete this comment?')" data-toggle="tooltip" title="Delete"> <i class="fa fa-remove"></i> </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $comments_list])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

@endsection
