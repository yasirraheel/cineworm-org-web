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

                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0">{{ $list->name }}</h4>
                                <p class="m-b-0">Import or manage mobile numbers for WhatsApp campaigns.</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ URL::to('admin/whatsapp/lists') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Lists
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Add Number</h4>
                                    <form method="post" action="{{ URL::to('admin/whatsapp/lists/'.$list->id.'/contacts/save') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Ali Khan">
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile Number</label>
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="923001234567" required>
                                            <small>Country code only, no plus sign.</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Company</label>
                                            <input type="text" name="company" class="form-control" value="{{ old('company') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Tags</label>
                                            <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="vip, press">
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fa fa-save"></i> Save Number
                                        </button>
                                    </form>
                                </div>

                                <div class="card-box">
                                    <h4 class="header-title m-t-0">Import Numbers</h4>
                                    <form method="post" action="{{ URL::to('admin/whatsapp/lists/'.$list->id.'/contacts/import') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label>CSV File</label>
                                            <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv">
                                        </div>
                                        <div class="form-group">
                                            <label>Paste Rows</label>
                                            <textarea name="import_source" rows="7" class="form-control" placeholder="name,phone,company,tags&#10;Ali Khan,923001234567,Cineworm,vip"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-upload"></i> Import
                                        </button>
                                        <a href="{{ URL::to('admin/whatsapp/lists/'.$list->id.'/contacts/sample-file') }}" class="btn btn-default">
                                            <i class="fa fa-download"></i> Sample
                                        </a>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card-box">
                                    <div class="row m-b-15">
                                        <div class="col-sm-7">
                                            <h4 class="header-title m-t-0">Numbers</h4>
                                        </div>
                                        <div class="col-sm-5 text-right">
                                            <a href="{{ URL::to('admin/whatsapp/campaigns/create') }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-send"></i> Create Campaign
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Tags</th>
                                                    <th>Status</th>
                                                    <th>Last Sent</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($contacts as $contact)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $contact->name ?: '-' }}</strong>
                                                        <div>{{ $contact->company ?: '' }}</div>
                                                    </td>
                                                    <td>{{ $contact->phone }}</td>
                                                    <td>{{ $contact->tags ?: '-' }}</td>
                                                    <td>
                                                        @if($contact->is_opted_out)
                                                            <span class="badge badge-default">Opted out</span>
                                                        @elseif($contact->status)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $contact->last_sent_at ? $contact->last_sent_at->format('M d, H:i') : '-' }}</td>
                                                    <td>
                                                        <a href="{{ URL::to('admin/whatsapp/lists/'.$list->id.'/contacts/delete/'.$contact->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this number?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center">No numbers in this list yet.</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav class="paging_simple_numbers">
                                        @include('admin.pagination', ['paginator' => $contacts])
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
