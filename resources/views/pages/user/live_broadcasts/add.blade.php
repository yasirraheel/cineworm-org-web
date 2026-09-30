@extends('site_app')

@section('head_title', 'Create CineMeet Room | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>CineMeet</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/live_broadcasts') }}">CineMeet</a></li>
                        <li>Create Room</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="edit-profile-area vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                @include('pages.user._sidebar')
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    <div class="edit-profile-form">
                        
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-12">
                                <h3 style="color:#fff;margin-bottom:5px;"><i class="fa fa-video-camera" style="color:#e50914;margin-right:8px;"></i> Create New CineMeet Room</h3>
                                <p style="color:#ccc;font-size:14px;">Fill out the form below to create a new CineMeet room.</p>
                            </div>
                        </div>

                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif
                        @if(Session::has('error_flash_message'))
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {{ Session::get('error_flash_message') }}
                            </div>
                        @endif

                        {!! Form::open(array('url' => 'user/live_broadcasts/create','class'=>'form-horizontal','name'=>'cinemeet_form','id'=>'cinemeet_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>CineMeet Room Title *</label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" placeholder="E.g. Weekly Strategy Sync, Film Review...">
                                    @if (isset($errors) && $errors->has('title'))
                                        <span class="help-block text-danger">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-12">
                                <button type="submit" class="vfx-item-btn-danger text-uppercase">
                                    <i class="fa fa-check"></i> Create CineMeet Room
                                </button>
                                <a href="{{ URL::to('user/live_broadcasts') }}" class="vfx-item-btn-danger text-uppercase" style="background-color: #555; margin-left: 10px;">Cancel</a>
                            </div>
                        </div>

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
