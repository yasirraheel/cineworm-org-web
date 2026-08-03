@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Plan Features Management</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-success waves-effect waves-light m-b-20" data-toggle="modal" data-target="#featureModal" onclick="resetForm()">
                                    <i class="fa fa-plus"></i> Add New Feature
                                </button>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Icon</th>
                                        <th>Feature Name</th>
                                        <th>Feature Key</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($features as $feature)
                                        <tr>
                                            <td><i class="{{ $feature->icon }}"></i></td>
                                            <td><strong>{{ $feature->feature_name }}</strong></td>
                                            <td><code>{{ $feature->feature_key }}</code></td>
                                            <td>
                                                @if($feature->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" onclick='editFeature(@json($feature))' class="btn btn-icon waves-effect waves-light btn-warning m-b-5 m-r-5" data-toggle="tooltip" title="Edit"> <i class="fa fa-edit"></i> </a>
                                                <a href="{{ url('admin/plan_features/delete/'.$feature->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" onclick="return confirm('Are you sure you want to delete this feature?')" data-toggle="tooltip" title="Delete"> <i class="fa fa-remove"></i> </a>
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
    </div>
    @include("admin.copyright")
</div>

<!-- Modal for Add/Edit Feature -->
<div id="featureModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content pt-3 pb-3 pl-0 pr-0">
            <div class="modal-header pl-3 pr-3">
                <h4 class="modal-title mt-0" id="featureModalLabel">Add Plan Feature</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            {!! Form::open(array('url' => array('admin/plan_features/save'),'class'=>'form-horizontal','name'=>'feature_form','id'=>'feature_form','role'=>'form')) !!}
                <input type="hidden" name="id" id="feature_id">
                <div class="modal-body pl-3 pr-3 pt-3 pb-0">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Feature Name *</label>
                        <div class="col-sm-8">
                            <input type="text" name="feature_name" id="feature_name" class="form-control" placeholder="e.g. Film Editing Access" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Icon Class</label>
                        <div class="col-sm-8">
                            <input type="text" name="icon" id="feature_icon" class="form-control" placeholder="e.g. fa fa-cut or fa fa-star">
                            <small class="form-text text-muted">FontAwesome class. Defaults to <code>fa fa-check-circle</code>.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="status" id="feature_status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pl-3 pr-3">
                    <button type="submit" class="btn btn-primary waves-effect waves-light"> {{trans('words.save')}}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('featureModalLabel').innerText = 'Add Plan Feature';
    document.getElementById('feature_id').value = '';
    document.getElementById('feature_name').value = '';
    document.getElementById('feature_icon').value = 'fa fa-check-circle';
    document.getElementById('feature_status').value = '1';
}

function editFeature(feature) {
    document.getElementById('featureModalLabel').innerText = 'Edit Plan Feature';
    document.getElementById('feature_id').value = feature.id;
    document.getElementById('feature_name').value = feature.feature_name;
    document.getElementById('feature_icon').value = feature.icon || 'fa fa-check-circle';
    document.getElementById('feature_status').value = feature.status;
    $('#featureModal').modal('show');
}
</script>

@endsection
