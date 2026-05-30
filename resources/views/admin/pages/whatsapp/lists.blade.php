@extends("admin.admin_app")

@section("content")
@include('admin.pages.whatsapp.partials.content_styles')

<div class="content-page whatsapp-admin-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        @include('admin.pages.whatsapp.partials.nav')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Create Mobile List</h4>
                                    <form method="post" action="{{ URL::to('admin/whatsapp/lists/save') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label>List Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Movie fans Pakistan" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" rows="4" class="form-control" placeholder="Audience note">{{ old('description') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fa fa-save"></i> Save List
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-box">
                                    <div class="row m-b-15">
                                        <div class="col-sm-8">
                                            <h4 class="header-title m-t-0">{{ $page_title }}</h4>
                                        </div>
                                        <div class="col-sm-4 text-right">
                                            <a href="{{ URL::to('admin/whatsapp/campaigns/create') }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-send"></i> New Campaign
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>List</th>
                                                    <th>Contacts</th>
                                                    <th>Status</th>
                                                    <th>Updated</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($lists as $list)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $list->name }}</strong>
                                                        <div>{{ $list->description ?: 'No description' }}</div>
                                                    </td>
                                                    <td>{{ $list->contacts_count }}</td>
                                                    <td>
                                                        @if($list->status)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $list->updated_at ? $list->updated_at->format('M d, Y') : '-' }}</td>
                                                    <td>
                                                        <a href="{{ URL::to('admin/whatsapp/lists/'.$list->id.'/contacts') }}" class="btn btn-success btn-sm">
                                                            <i class="fa fa-address-book"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center">No mobile lists yet.</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav class="paging_simple_numbers">
                                        @include('admin.pagination', ['paginator' => $lists])
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.copyright')
</div>

@include('admin.pages.whatsapp.partials.flash')
@endsection
