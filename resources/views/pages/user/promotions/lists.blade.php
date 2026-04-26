@extends('site_app')

@section('head_title', 'Email Lists | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12"><h2>Email Lists</h2></div></div></div>
</div>
<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @include('pages.user.promotions._nav')

        <div class="row">
            <div class="col-md-5">
                <div class="promotion-panel">
                    <div class="promotion-header">
                        <div>
                            <h3>Create Email List</h3>
                            <p class="promotion-help-text">Create a list first, then open it to add contacts manually or import them by CSV.</p>
                        </div>
                    </div>
                    <form method="post" action="{{ URL::to('promotions/lists/save') }}">
                        @csrf
                        <div class="form-group">
                            <label class="promotion-label">List Name</label>
                            <input type="text" name="name" class="form-control promotion-input" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="promotion-label">Description</label>
                            <textarea name="description" class="form-control promotion-textarea" rows="4">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Save List</button>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="promotion-panel">
                    <div class="promotion-header">
                        <div>
                            <h3>Your Lists</h3>
                            <p class="promotion-help-text">Use the action buttons to open manual add and CSV import for any list.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table promotion-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contacts</th>
                                    <th>Description</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lists as $list)
                                    <tr>
                                        <td><strong>{{ $list->name }}</strong></td>
                                        <td>{{ $list->contacts_count }}</td>
                                        <td>{{ $list->description ?: '-' }}</td>
                                        <td class="text-right">
                                            <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts') }}" class="btn btn-sm btn-danger">Add / Import Contacts</a>
                                            <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts/sample-file') }}" class="btn btn-sm btn-default">Sample CSV</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No email lists found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('_particles.pagination', ['paginator' => $lists])
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.user.promotions._flash')
@endsection
