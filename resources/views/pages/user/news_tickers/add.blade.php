@extends('site_app')

@section('head_title', 'Submit News Ticker | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Submit News Ticker</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/news_tickers') }}">My News Tickers</a></li>
                        <li>Submit News Ticker</li>
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
                        
                        <h3 style="color:#fff;margin-bottom:20px;"><i class="fa fa-newspaper-o" style="color:#e50914;margin-right:8px;"></i> Submit News Ticker</h3>

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul style="margin-bottom: 0;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {!! Form::open(array('url' => 'user/news_tickers/store','class'=>'form-horizontal','name'=>'news_form','id'=>'news_form','role'=>'form')) !!}
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label style="color:#ccc;">Headline *</label>
                                    <input type="text" name="headline" value="{{ old('headline') }}" class="form-control" required style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label style="color:#ccc;">Details *</label>
                                    <textarea name="details" class="form-control" rows="5" required style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">{{ old('details') }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="checkbox" style="margin-top: 10px;">
                                        <label style="color:#ccc;">
                                            <input type="checkbox" name="is_breaking" value="1" @if(old('is_breaking')) checked @endif> 
                                            Mark as Breaking News
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <button type="submit" class="vfx-item-btn-danger text-uppercase" style="border:none; cursor:pointer;">
                                    <i class="fa fa-paper-plane"></i> Submit for Approval
                                </button>
                                <a href="{{ URL::to('user/news_tickers') }}" class="btn btn-secondary" style="margin-left: 10px; background: rgba(255,255,255,0.1); border: none; color: #fff; padding: 12px 25px; border-radius: 6px;">Cancel</a>
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
