@extends('site_app')

@section('head_title', 'WhatsApp Contacts - '.$list->name.' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Manage Contacts: {{ $list->name }}</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/whatsapp') }}">WhatsApp</a></li>
                        <li><a href="{{ URL::to('user/whatsapp/lists') }}">Contact Lists</a></li>
                        <li>{{ $list->name }}</li>
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

                    @include('pages.user.whatsapp._flash')

                    <div class="card mb-4" style="background:#161b26;border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:20px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 style="color:#fff;font-weight:700;margin:0;"><i class="fa fa-users"></i> Contacts ({{ $list->name }})</h4>
                            <div>
                                <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#importModal" style="font-weight:600;">
                                    <i class="fa fa-upload"></i> Import CSV
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#contactModal" onclick="resetContactForm()" style="background:#25D366;border-color:#25D366;font-weight:600;">
                                    <i class="fa fa-plus"></i> Add Contact
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-bordered" style="font-size:14px;">
                                <thead>
                                    <tr>
                                        <th>Phone Number</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Tags</th>
                                        <th>Last Sent</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contacts as $contact)
                                        <tr>
                                            <td><strong>+{{ $contact->phone }}</strong></td>
                                            <td>{{ $contact->name ?: 'N/A' }}</td>
                                            <td><small class="text-muted">{{ $contact->company ?: 'N/A' }}</small></td>
                                            <td>
                                                @if($contact->tags)
                                                    <span class="badge bg-secondary">{{ $contact->tags }}</span>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $contact->last_sent_at ? $contact->last_sent_at->format('Y-m-d H:i') : 'Never' }}</small></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" onclick='editContact(@json($contact))' title="Edit Contact"><i class="fa fa-edit"></i></button>
                                                <a href="{{ URL::to('user/whatsapp/lists/'.$list->id.'/contacts/delete/'.$contact->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this contact?')" title="Delete"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No contacts in this list yet. Click "Add Contact" or "Import CSV" to upload contacts.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $contacts->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Create/Edit Contact -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{ URL::to('user/whatsapp/lists/'.$list->id.'/contacts/save') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="contact_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalTitle" style="color:#25D366;font-weight:700;">Add Contact</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number * (with Country Code)</label>
                            <input type="text" name="phone" id="contact_phone" class="form-control" placeholder="e.g. 15551234567 or 447700900077" required>
                            <small class="text-muted d-block mt-1">Include country code without + or spaces.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Name</label>
                            <input type="text" name="name" id="contact_name" class="form-control" placeholder="e.g. John Doe">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company / Organization</label>
                            <input type="text" name="company" id="contact_company" class="form-control" placeholder="e.g. Acme Corp">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tags</label>
                            <input type="text" name="tags" id="contact_tags" class="form-control" placeholder="e.g. VIP, Client">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" style="background:#25D366;border-color:#25D366;font-weight:600;">Save Contact</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal for CSV Import -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{ URL::to('user/whatsapp/lists/'.$list->id.'/contacts/import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color:#38bdf8;font-weight:700;"><i class="fa fa-upload"></i> Import Contacts from CSV</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select CSV File *</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                    </div>
                    <div class="p-3 mb-3 rounded" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);font-size:13px;">
                        <strong style="color:#38bdf8;">CSV Format Requirements:</strong><br>
                        - Column headers: <code>phone</code> (required), <code>name</code>, <code>company</code>, <code>tags</code>.<br>
                        - Phone numbers must include country code.
                    </div>
                    <a href="{{ URL::to('user/whatsapp/lists/sample-file') }}" class="btn btn-sm btn-outline-info" style="font-weight:600;"><i class="fa fa-download"></i> Download Sample CSV Template</a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info" style="font-weight:600;">Upload & Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function resetContactForm() {
    $('#contactModalTitle').text('Add Contact');
    $('#contact_id').val('');
    $('#contact_phone').val('');
    $('#contact_name').val('');
    $('#contact_company').val('');
    $('#contact_tags').val('');
}

function editContact(contact) {
    $('#contactModalTitle').text('Edit Contact');
    $('#contact_id').val(contact.id);
    $('#contact_phone').val(contact.phone);
    $('#contact_name').val(contact.name || '');
    $('#contact_company').val(contact.company || '');
    $('#contact_tags').val(contact.tags || '');
    $('#contactModal').modal('show');
}
</script>
@endsection
