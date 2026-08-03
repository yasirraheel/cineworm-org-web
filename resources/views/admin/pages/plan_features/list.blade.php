@extends("admin.admin_app")

@section("content")

<style>
.icon-preview {
    display: inline-block;
    width: 28px;
    text-align: center;
    font-size: 16px;
    margin-right: 8px;
}
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 class="header-title m-t-0 m-b-30 text-primary" style="font-size: 20px;">
                                    <i class="fa fa-list"></i> Plan Features Management
                                </h4>
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-success waves-effect waves-light pull-right" data-toggle="modal" data-target="#featureModal" onclick="resetForm()">
                                    <i class="fa fa-plus"></i> Add New Feature
                                </button>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Icon</th>
                                    <th>Feature Name</th>
                                    <th>Feature Key</th>
                                    <th>Target URL</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($features as $feature)
                                    <tr>
                                        <td>
                                            <span class="icon-preview"><i class="{{ $feature->icon }}"></i></span>
                                        </td>
                                        <td><strong>{{ $feature->feature_name }}</strong></td>
                                        <td><code>{{ $feature->feature_key }}</code></td>
                                        <td><small>{{ $feature->url ?: 'javascript:void(0);' }}</small></td>
                                        <td>
                                            @if($feature->status == 1)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick='editFeature(@json($feature))'>
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <a href="{{ url('admin/plan_features/delete/'.$feature->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this feature?')">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
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
                        <small class="form-text text-muted">Use relative paths like <code>reel2reel/</code> or full URLs. Leave blank for default modal.</small>
                    </div>
                    <div class="form-group">
                        <label>Icon Class (FontAwesome)</label>
                        <input type="text" name="icon" id="feature_icon" class="form-control" placeholder="e.g. fa fa-cut or fa fa-star">
                        <small class="form-text text-muted">FontAwesome icon class. Defaults to <code>fa fa-check-circle</code>.</small>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Feature</button>
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
