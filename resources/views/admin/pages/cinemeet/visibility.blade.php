@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30"><i class="fa fa-eye text-primary"></i> CineMeet — Section Visibility</h4>

                            @if(session('flash_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('flash_message') }}
                                </div>
                            @endif
                            @if(session('flash_error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ session('flash_error') }}
                                </div>
                            @endif

                            <form action="{{ URL::to('admin/cinemeet/visibility') }}" method="POST" class="form-horizontal" role="form">
                                {{ csrf_field() }}

                                @php
                                    $isTrue = fn($key) => isset($settings[$key]) && strtolower($settings[$key]) === 'true';
                                @endphp

                                <h5 class="text-custom m-b-20"><i class="fa fa-star text-warning"></i> Sponsors & Advertisers</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Top Sponsors</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_TOP_SPONSORS" type="checkbox" name="SHOW_TOP_SPONSORS" {{ $isTrue('SHOW_TOP_SPONSORS') ? 'checked' : '' }}>
                                            <label for="SHOW_TOP_SPONSORS">Show "This project is proudly sponsored by" header block</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Current Sponsors</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_SPONSORS" type="checkbox" name="SHOW_SPONSORS" {{ $isTrue('SHOW_SPONSORS') ? 'checked' : '' }}>
                                            <label for="SHOW_SPONSORS">Show active sponsors logos</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Past Sponsors</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_PAST_SPONSORS" type="checkbox" name="SHOW_PAST_SPONSORS" {{ $isTrue('SHOW_PAST_SPONSORS') ? 'checked' : '' }}>
                                            <label for="SHOW_PAST_SPONSORS">Show previous sponsors</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Our Advertisers</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_ADVERTISERS" type="checkbox" name="SHOW_ADVERTISERS" {{ $isTrue('SHOW_ADVERTISERS') ? 'checked' : '' }}>
                                            <label for="SHOW_ADVERTISERS">Show partner advertisers section</label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-th-large text-info"></i> Landing Page Sections</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Features Section</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_FEATURES" type="checkbox" name="SHOW_FEATURES" {{ $isTrue('SHOW_FEATURES') ? 'checked' : '' }}>
                                            <label for="SHOW_FEATURES">Show feature cards (Screen Sharing, WebCam, etc.)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Teams Section</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_TEAMS" type="checkbox" name="SHOW_TEAMS" {{ $isTrue('SHOW_TEAMS') ? 'checked' : '' }}>
                                            <label for="SHOW_TEAMS">Show team members section</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Try Other Apps Section</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_TRY_EASIER" type="checkbox" name="SHOW_TRY_EASIER" {{ $isTrue('SHOW_TRY_EASIER') ? 'checked' : '' }}>
                                            <label for="SHOW_TRY_EASIER">Show related products block</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Support Us Section</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_SUPPORT_US" type="checkbox" name="SHOW_SUPPORT_US" {{ $isTrue('SHOW_SUPPORT_US') ? 'checked' : '' }}>
                                            <label for="SHOW_SUPPORT_US">Show donation & support section</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Active Rooms</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_ACTIVE_ROOMS" type="checkbox" name="SHOW_ACTIVE_ROOMS" {{ $isTrue('SHOW_ACTIVE_ROOMS') ? 'checked' : '' }}>
                                            <label for="SHOW_ACTIVE_ROOMS">Show active public rooms list on homepage</label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="m-t-30 m-b-30">

                                <h5 class="text-custom m-b-20"><i class="fa fa-columns text-purple"></i> Header & Footer</h5>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Powered By</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_POWERED_BY" type="checkbox" name="SHOW_POWERED_BY" {{ $isTrue('SHOW_POWERED_BY') ? 'checked' : '' }}>
                                            <label for="SHOW_POWERED_BY">Show "Powered by MiroTalk SFU" credit badge</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Footer</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox checkbox-primary">
                                            <input id="SHOW_FOOTER" type="checkbox" name="SHOW_FOOTER" {{ $isTrue('SHOW_FOOTER') ? 'checked' : '' }}>
                                            <label for="SHOW_FOOTER">Show full bottom footer block</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row m-t-30">
                                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="fa fa-save"></i> Save & Restart CineMeet
                                        </button>
                                        <a href="{{ URL::to('admin/cinemeet') }}" class="btn btn-secondary waves-effect m-l-5">Cancel</a>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
