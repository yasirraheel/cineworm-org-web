@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30">{{ $page_title }}</h4>

                        {!! Form::open(array('url' => array('admin/news_ticker/save'),'class'=>'form-horizontal','name'=>'news_form','id'=>'news_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                        <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Headline *</label>
                            <div class="col-sm-8">
                                <input type="text" name="headline" value="{{ isset($edit) ? $edit->headline : old('headline') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Next Detail News</label>
                            <div class="col-sm-8">
                                <textarea id="elm1" name="details" class="form-control">{{ isset($edit) ? $edit->details : old('details') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Is Breaking</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="is_breaking">
                                    <option value="1" @if(isset($edit) && $edit->is_breaking==1) selected @endif>Yes</option>
                                    <option value="0" @if(isset($edit) && $edit->is_breaking==0) selected @endif>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{trans('words.status')}}</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="status">
                                    <option value="1" @if(isset($edit) && $edit->status==1) selected @endif>{{trans('words.active')}}</option>
                                    <option value="0" @if(isset($edit) && $edit->status==0) selected @endif>{{trans('words.inactive')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="offset-sm-3 col-sm-9 pl-1">
                                <button type="submit" class="btn btn-primary waves-effect waves-light"> {{trans('words.save')}}</button>
                                @if(isset($edit))
                                    <a href="{{ url('admin/news_ticker') }}" class="btn btn-secondary waves-effect waves-light"> Cancel</a>
                                @endif
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Headline</th>
                                    <th>Submitted By</th>
                                    <th>Breaking</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $item)
                                <tr>
                                    <td>{{ $item->headline }}</td>
                                    <td>
                                        @if($item->user)
                                            <span class="badge badge-info">{{ $item->user->name }}</span>
                                        @else
                                            <span class="badge badge-primary">Admin</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->is_breaking)
                                            <span class="badge badge-danger">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status)
                                            <span class="badge badge-success">{{trans('words.active')}} / Approved</span>
                                        @else
                                            <span class="badge badge-warning text-white">Pending / {{trans('words.inactive')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 0)
                                            <a href="{{ url('admin/news_ticker/approve/'.$item->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-r-5" data-toggle="tooltip" title="Approve">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        @else
                                            <a href="{{ url('admin/news_ticker/unapprove/'.$item->id) }}" class="btn btn-icon waves-effect waves-light btn-warning m-r-5" data-toggle="tooltip" title="Unapprove">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        @endif
                                        <a href="{{ url('admin/news_ticker/edit/'.$item->id) }}" class="btn btn-icon waves-effect waves-light btn-primary m-r-5" data-toggle="tooltip" title="{{trans('words.edit')}}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ url('admin/news_ticker/delete/'.$item->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-r-5" onclick="return confirm('{{trans('words.dlt_warning_text')}}')" data-toggle="tooltip" title="{{trans('words.remove')}}">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <nav>
                            {{ $list->links() }}
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include("admin.copyright")
</div>

@endsection
