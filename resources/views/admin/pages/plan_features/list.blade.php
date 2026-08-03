@extends("admin.admin_app")

@section("content")

<style>
  .card-box.table-responsive {
    background: #1a2234 !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 8px !important;
    padding: 24px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
  }
  .table-custom {
    color: #ffffff !important;
    background-color: #161b26 !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    margin-bottom: 0 !important;
    width: 100% !important;
  }
  .table-custom thead th {
    background-color: #10141d !important;
    color: #fe0278 !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    font-size: 13px !important;
    letter-spacing: 0.5px !important;
    border-bottom: 2px solid rgba(254, 2, 120, 0.4) !important;
    border-top: none !important;
    padding: 14px 16px !important;
  }
  .table-custom tbody tr {
    background-color: #1a2234 !important;
    transition: all 0.2s ease !important;
  }
  .table-custom tbody tr:nth-of-type(even) {
    background-color: #151c2b !important;
  }
  .table-custom tbody tr:hover {
    background-color: rgba(254, 2, 120, 0.12) !important;
  }
  .table-custom td {
    color: #f1f5f9 !important;
    border-color: rgba(255, 255, 255, 0.06) !important;
    vertical-align: middle !important;
    padding: 14px 16px !important;
    font-size: 14px !important;
  }
  .icon-preview {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 6px;
    background: rgba(254, 2, 120, 0.15);
    color: #fe0278;
    font-size: 16px;
  }
  .feature-key-code {
    background: rgba(0, 0, 0, 0.4) !important;
    color: #00e676 !important;
    padding: 4px 8px !important;
    border-radius: 4px !important;
    font-family: monospace !important;
    font-size: 13px !important;
    border: 1px solid rgba(0, 230, 118, 0.25) !important;
    display: inline-block;
  }
  .target-url-text {
    color: #94a3b8 !important;
    font-size: 13px !important;
  }
  /* Action Buttons Styling */
  .action-btn-group {
    display: inline-flex !important;
    gap: 8px !important;
    align-items: center !important;
    white-space: nowrap !important;
  }
  .btn-action-edit {
    background: rgba(53, 184, 224, 0.15) !important;
    border: 1px solid rgba(53, 184, 224, 0.35) !important;
    color: #35b8e0 !important;
    border-radius: 6px !important;
    padding: 6px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
  }
  .btn-action-edit:hover {
    background: #35b8e0 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(53, 184, 224, 0.3) !important;
    transform: translateY(-1px) !important;
  }
  .btn-action-delete {
    background: rgba(255, 91, 91, 0.15) !important;
    border: 1px solid rgba(255, 91, 91, 0.35) !important;
    color: #ff5b5b !important;
    border-radius: 6px !important;
    padding: 6px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
  }
  .btn-action-delete:hover {
    background: #ff5b5b !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(255, 91, 91, 0.3) !important;
    transform: translateY(-1px) !important;
  }
  /* Modal styling */
  .modal-content {
    background-color: #1a2234 !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5) !important;
  }
  .modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
  }
  .modal-header .modal-title {
    color: #fe0278 !important;
    font-weight: 700 !important;
  }
  .modal-header .close {
    color: #ffffff !important;
    opacity: 0.8;
  }
  .modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
  }
  .modal-body label {
    color: #cbd5e1 !important;
    font-weight: 600 !important;
  }
  .modal-body .form-control {
    background-color: #10141d !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    border-radius: 4px !important;
  }
  .modal-body .form-control:focus {
    border-color: #fe0278 !important;
    box-shadow: 0 0 0 2px rgba(254, 2, 120, 0.25) !important;
  }
  .modal-body code {
    background: rgba(0,0,0,0.3);
    color: #fe0278;
    padding: 2px 5px;
    border-radius: 3px;
  }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <h4 class="header-title m-t-0 m-b-30 text-primary" style="font-size: 20px; color: #fe0278 !important;">
                                    <i class="fa fa-list-alt"></i> Plan Features Management
                                </h4>
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-success waves-effect waves-light pull-right" data-toggle="modal" data-target="#featureModal" onclick="resetForm()" style="background-color: #fe0278 !important; border-color: #fe0278 !important;">
                                    <i class="fa fa-plus"></i> Add New Feature
                                </button>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success" style="background-color: rgba(0, 230, 118, 0.2); border-color: #00e676; color: #00e676;">
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif

                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th width="80">Icon</th>
                                    <th>Feature Name</th>
                                    <th>Feature Key</th>
                                    <th>Target URL</th>
                                    <th width="100">Status</th>
                                    <th width="180" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($features as $feature)
                                    <tr>
                                        <td>
                                            <span class="icon-preview"><i class="{{ $feature->icon }}"></i></span>
                                        </td>
                                        <td><strong>{{ $feature->feature_name }}</strong></td>
                                        <td><span class="feature-key-code">{{ $feature->feature_key }}</span></td>
                                        <td><span class="target-url-text">{{ $feature->url ?: 'javascript:void(0);' }}</span></td>
                                        <td>
                                            @if($feature->status == 1)
                                                <span class="badge badge-success" style="background-color: #10c469 !important; padding: 5px 8px;">Active</span>
                                            @else
                                                <span class="badge badge-danger" style="background-color: #ff5b5b !important; padding: 5px 8px;">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="action-btn-group">
                                                <button class="btn-action-edit" onclick='editFeature(@json($feature))'>
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <a href="{{ url('admin/plan_features/delete/'.$feature->id) }}" class="btn-action-delete" onclick="return confirm('Are you sure you want to delete this feature?')">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

<!-- Modal for Add/Edit Feature -->
<div class="modal fade" id="featureModal" tabindex="-1" role="dialog" aria-labelledby="featureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ url('admin/plan_features/save') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="feature_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="featureModalLabel">Add Plan Feature</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Feature Name *</label>
                        <input type="text" name="feature_name" id="feature_name" class="form-control" placeholder="e.g. Film Editing Access" required>
                    </div>
                    <div class="form-group">
                        <label>Target URL / Path</label>
                        <input type="text" name="url" id="feature_url" class="form-control" placeholder="e.g. reel2reel/ or user/films">
                        <small class="form-text text-muted" style="color: #94a3b8 !important;">Use relative paths like <code>reel2reel/</code> or full URLs. Leave blank for default modal.</small>
                    </div>
                    <div class="form-group">
                        <label>Icon Class (FontAwesome)</label>
                        <input type="text" name="icon" id="feature_icon" class="form-control" placeholder="e.g. fa fa-cut or fa fa-star">
                        <small class="form-text text-muted" style="color: #94a3b8 !important;">FontAwesome icon class. Defaults to <code>fa fa-check-circle</code>.</small>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="feature_status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #475569; border-color: #475569; color: #fff;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #fe0278; border-color: #fe0278; color: #fff;">Save Feature</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('featureModalLabel').innerText = 'Add Plan Feature';
    document.getElementById('feature_id').value = '';
    document.getElementById('feature_name').value = '';
    document.getElementById('feature_url').value = '';
    document.getElementById('feature_icon').value = 'fa fa-check-circle';
    document.getElementById('feature_status').value = '1';
}

function editFeature(feature) {
    document.getElementById('featureModalLabel').innerText = 'Edit Plan Feature';
    document.getElementById('feature_id').value = feature.id;
    document.getElementById('feature_name').value = feature.feature_name;
    document.getElementById('feature_url').value = feature.url || '';
    document.getElementById('feature_icon').value = feature.icon || 'fa fa-check-circle';
    document.getElementById('feature_status').value = feature.status;
    $('#featureModal').modal('show');
}
</script>

@endsection

