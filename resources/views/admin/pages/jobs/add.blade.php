@extends("admin.admin_app")

@section("content")
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="{{ URL::to('admin/job_listings') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                        <h4 class="page-title">{{ $page_title }}</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {!! Form::open(array('url' => 'admin/job_listings/store','class'=>'form-horizontal','name'=>'job_form','id'=>'job_form','role'=>'form','enctype' => 'multipart/form-data')) !!}
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Job Title *</label>
                                <div class="col-sm-8">
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Company Name *</label>
                                <div class="col-sm-8">
                                    <input type="text" name="company" value="{{ old('company') }}" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Location *</label>
                                <div class="col-sm-8">
                                    <input type="text" name="location" value="{{ old('location') }}" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Salary (Optional)</label>
                                <div class="col-sm-8">
                                    <input type="text" name="salary" value="{{ old('salary') }}" class="form-control" placeholder="e.g. $50k - $70k">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Contact Details / Application Link</label>
                                <div class="col-sm-8">
                                    <input type="text" name="contact_details" value="{{ old('contact_details') }}" class="form-control" placeholder="Email or URL">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="status">
                                        <option value="1">Approved (Visible to all)</option>
                                        <option value="0">Pending Approval</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Description *</label>
                                <div class="col-sm-8">
                                    <textarea name="description" class="form-control" rows="8" required>{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-offset-3 col-sm-9 pl-3">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light"> Save Job Listing </button>
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
