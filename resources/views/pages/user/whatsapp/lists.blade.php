@extends('site_app')

@section('head_title', 'WhatsApp Contact Lists | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>WhatsApp Contact Lists</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/whatsapp') }}">WhatsApp</a></li>
                        <li>Contact Lists</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                @include('pages.user._sidebar')
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    @include('pages.user.whatsapp._nav')

                    @if(Session::has('flash_message'))
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            {{ Session::get('flash_message') }}
                        </div>
                    @endif

                    <div class="card mb-4" style="background:#161b26;border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:20px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 style="color:#fff;font-weight:700;margin:0;"><i class="fa fa-list-ul"></i> Contact Lists</h4>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#listModal" onclick="resetListForm()" style="background:#25D366;border-color:#25D366;font-weight:600;">
                                <i class="fa fa-plus"></i> Create New List
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-bordered" style="font-size:14px;">
                                <thead>
                                    <tr>
                                        <th>List Name</th>
                                        <th>Description</th>
                                        <th>Contacts</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lists as $list)
                                        <tr>
                                            <td><strong>{{ $list->name }}</strong></td>
                                            <td><small class="text-muted">{{ $list->description ?: 'N/A' }}</small></td>
                                            <td><span class="badge bg-success" style="font-size:13px;">{{ $list->contacts_count }} Contacts</span></td>
                                            <td>{{ $list->created_at ? $list->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ URL::to('user/whatsapp/lists/'.$list->id.'/contacts') }}" class="btn btn-sm btn-info me-1" title="Manage Contacts"><i class="fa fa-users"></i> Manage</a>
                                                <button class="btn btn-sm btn-warning me-1" onclick='editList(@json($list))' title="Edit List"><i class="fa fa-edit"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No contact lists created yet. Click "Create New List" to get started.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $lists->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Create/Edit List -->
<div class="modal fade" id="listModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ URL::to('user/whatsapp/lists/save') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="list_id">
            <div class="modal-content" style="background:#1a2234;color:#fff;border:1px solid rgba(255,255,255,0.1);">
                <div class="modal-header">
                    <h5 class="modal-title" id="listModalTitle" style="color:#25D366;font-weight:700;">Create Contact List</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">List Name *</label>
                        <input type="text" name="name" id="list_name" class="form-control" placeholder="e.g. VIP Subscribers" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="list_description" class="form-control" rows="3" placeholder="Optional list notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,0.1);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" style="background:#25D366;border-color:#25D366;">Save List</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function resetListForm() {
    $('#listModalTitle').text('Create Contact List');
    $('#list_id').val('');
    $('#list_name').val('');
    $('#list_description').val('');
}

function editList(list) {
    $('#listModalTitle').text('Edit Contact List');
    $('#list_id').val(list.id);
    $('#list_name').val(list.name);
    $('#list_description').val(list.description || '');
    $('#listModal').modal('show');
}
</script>
@endsection
