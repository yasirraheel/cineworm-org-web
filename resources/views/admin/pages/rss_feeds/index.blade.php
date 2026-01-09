@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30">{{ $page_title }}</h4>

                        {!! Form::open(array('url' => array('admin/rss_feeds/save'),'class'=>'form-horizontal','name'=>'rss_form','id'=>'rss_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                        <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Feed Name *</label>
                            <div class="col-sm-8">
                                <input type="text" name="name" value="{{ isset($edit) ? $edit->name : old('name') }}" class="form-control" placeholder="e.g., Good News Network" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Feed URL *</label>
                            <div class="col-sm-8">
                                <input type="url" name="url" value="{{ isset($edit) ? $edit->url : old('url') }}" class="form-control" placeholder="https://example.com/feed.xml" required>
                                <small class="form-text text-muted">Enter the full RSS feed URL</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Description</label>
                            <div class="col-sm-8">
                                <textarea name="description" class="form-control" rows="3" placeholder="Optional description about this RSS feed">{{ isset($edit) ? $edit->description : old('description') }}</textarea>
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
                                    <a href="{{ url('admin/rss_feeds') }}" class="btn btn-secondary waves-effect waves-light"> Cancel</a>
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
                                    <th>Feed Name</th>
                                    <th>Feed URL</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <a href="{{ $item->url }}" target="_blank" class="text-primary">
                                            {{ Str::limit($item->url, 50) }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($item->status)
                                            <span class="badge badge-success">{{trans('words.active')}}</span>
                                        @else
                                            <span class="badge badge-danger">{{trans('words.inactive')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('admin/rss_feeds/edit/'.$item->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-r-5" data-toggle="tooltip" title="{{trans('words.edit')}}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ url('admin/rss_feeds/delete/'.$item->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-r-5" onclick="return confirm('{{trans('words.dlt_warning_text')}}')" data-toggle="tooltip" title="{{trans('words.remove')}}">
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
