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
                    <h3 style="color:#fff;margin-top:0;">Create Email List</h3>
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
                    <h3 style="color:#fff;margin-top:0;">Your Lists</h3>
                    <div class="table-responsive">
                        <table class="table promotion-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contacts</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lists as $list)
                                    <tr>
                                        <td><strong>{{ $list->name }}</strong></td>
                                        <td>{{ $list->contacts_count }}</td>
                                        <td>{{ $list->description ?: '-' }}</td>
                                        <td class="text-right">
                                            <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts') }}" class="btn btn-sm btn-danger">Manage Contacts</a>
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
