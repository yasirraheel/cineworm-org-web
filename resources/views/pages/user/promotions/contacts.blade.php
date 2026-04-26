@extends('site_app')

@section('head_title', 'Manage Contacts | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12"><h2>{{ $list->name }} Contacts</h2></div></div></div>
</div>
<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @include('pages.user.promotions._nav')

        <div class="row">
            <div class="col-md-5">
                <div class="promotion-panel">
                    <div class="clearfix" style="margin-bottom:18px;">
                        <h3 style="color:#fff;margin-top:0;float:left;">Add Contact Manually</h3>
                        <span class="promotion-help-text pull-right" style="margin-top:8px;">Add one email at a time</span>
                    </div>
                    <form method="post" action="{{ URL::to('promotions/lists/'.$list->id.'/contacts/save') }}">
                        @csrf
                        <div class="form-group">
                            <label class="promotion-label">Name</label>
                            <input type="text" name="name" class="form-control promotion-input" value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label class="promotion-label">Email</label>
                            <input type="email" name="email" class="form-control promotion-input" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="promotion-label">Company</label>
                            <input type="text" name="company" class="form-control promotion-input" value="{{ old('company') }}">
                        </div>
                        <div class="form-group">
                            <label class="promotion-label">Tags</label>
                            <input type="text" name="tags" class="form-control promotion-input" value="{{ old('tags') }}" placeholder="vip, investors, press">
                        </div>
                        <button type="submit" class="btn btn-danger">Save Contact</button>
                    </form>
                </div>

                <div class="promotion-panel">
                    <div class="clearfix" style="margin-bottom:18px;">
                        <h3 style="color:#fff;margin-top:0;float:left;">Import Contacts</h3>
                        <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts/sample-file') }}" class="btn btn-sm promotion-btn-secondary pull-right">Download Sample CSV</a>
                    </div>
                    <p class="promotion-help-text">Upload a CSV file or paste CSV rows using this format: <strong>name, email, company, tags</strong>.</p>
                    <form method="post" action="{{ URL::to('promotions/lists/'.$list->id.'/contacts/import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="promotion-label">CSV File</label>
                            <input type="file" name="csv_file" class="form-control promotion-input">
                        </div>
                        <div class="form-group">
                            <label class="promotion-label">Paste CSV Rows</label>
                            <textarea name="import_source" rows="7" class="form-control promotion-textarea" placeholder="name,email,company,tags&#10;John Doe,john@example.com,Studio X,investor"></textarea>
                        </div>
                        <button type="submit" class="btn promotion-btn-secondary">Import Contacts</button>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="promotion-panel">
                    <div class="clearfix">
                        <h3 style="color:#fff;margin-top:0;float:left;">List Contacts</h3>
                        <a href="{{ URL::to('promotions/campaigns/create') }}" class="btn btn-danger pull-right">Create Campaign</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table promotion-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Tags</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $contact)
                                    <tr>
                                        <td>{{ $contact->name ?: '-' }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ $contact->company ?: '-' }}</td>
                                        <td>{{ $contact->tags ?: '-' }}</td>
                                        <td class="text-right">
                                            <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts/delete/'.$contact->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this contact?');">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">No contacts in this list yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('_particles.pagination', ['paginator' => $contacts])
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.user.promotions._flash')
@endsection
