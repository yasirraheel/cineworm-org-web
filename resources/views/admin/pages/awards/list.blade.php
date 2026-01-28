@extends('admin.admin_app')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <h4 class="header-title m-t-0 m-b-30">Awards List</h4>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Movie</th>
                                    <th>Award Type</th>
                                    <th>Date Awarded</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($awards as $award)
                                <tr>
                                    <td>{{ $award->id }}</td>
                                    <td>
                                        @if($award->user)
                                            <a href="{{ url('admin/users/edit_user/'.$award->user->id) }}">{{ $award->user->name }}</a>
                                            <br>
                                            <small>{{ $award->user->email }}</small>
                                        @else
                                            Unknown User
                                        @endif
                                    </td>
                                    <td>
                                        @if($award->movie)
                                            <a href="{{ url('admin/movies/edit_movie/'.$award->movie->id) }}">{{ $award->movie->video_title }}</a>
                                        @else
                                            Unknown Movie
                                        @endif
                                    </td>
                                    <td>
                                        @if($award->award_type == '100_likes')
                                            <span class="badge badge-primary">100 Likes</span>
                                        @elseif($award->award_type == '1000_likes')
                                            <span class="badge badge-info">1000 Likes</span>
                                        @elseif($award->award_type == '10000_likes')
                                            <span class="badge badge-warning">10000 Likes</span>
                                        @else
                                            {{ $award->award_type }}
                                        @endif
                                    </td>
                                    <td>{{ $award->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $awards->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
