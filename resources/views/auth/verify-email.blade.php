@extends('site_app')

@section('head_title', 'Email Verification | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

<!-- Email Verification Main Wrapper Start -->
<div id="main-wrapper">
  <div class="container-fluid px-0 m-0 h-100 mx-auto">
    <div class="row g-0 min-vh-100 overflow-hidden">
      <!-- Welcome Text -->
      <div class="col-md-12">
        <div class="hero-wrap d-flex align-items-center h-100">
          <div class="hero-mask"></div>
          <div class="hero-bg hero-bg-scroll" style="background-image:url('{{ URL::asset('site_assets/images/login-signup-bg-img.jpg') }}');"></div>
          <div class="hero-content mx-auto w-100 h-100 d-flex flex-column justify-content-center">
            <div class="row">
              <div class="col-12 col-lg-5 col-xl-5 mx-auto">
                <div class="logo mt-40 mb-20 mb-md-0 justify-content-center d-flex text-center">

                  @if(getcong('site_logo'))
                    <a href="{{ URL::to('/') }}" title="logo"><img src="{{ URL::asset('/'.getcong('site_logo')) }}" alt="logo" title="logo" class="login-signup-logo"></a>
                  @else
                    <a href="{{ URL::to('/') }}" title="logo"><img src="{{ URL::asset('site_assets/images/logo.png') }}" alt="logo" title="logo" class="login-signup-logo"></a>
                  @endif

                </div>
              </div>
            </div>
            <!-- Verification Form -->
        <div class="col-lg-5 col-md-7 col-sm-8 mx-auto d-flex align-items-center login-item-block">
        <div class="container login-part">
          <div class="row">
          <div class="col-12 col-lg-12 col-xl-12 mx-auto">
            <h2 class="form-title-item mb-4">Verify Your Email Address</h2>

            @if (session('flash_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('flash_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('flash_error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('flash_error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="verification-message mb-4">
                <p style="color: #fff; font-size: 16px; line-height: 1.6;">
                    Before proceeding, please check your email for a verification link.
                </p>
                <p style="color: #fff; font-size: 16px; line-height: 1.6;">
                    If you did not receive the email, you can request a new verification link below.
                </p>
            </div>

            {!! Form::open(array('url' => route('verification.resend'),'class'=>'','id'=>'resendform','role'=>'form', 'method' => 'POST')) !!}

            <div class="form-group mb-3">
              <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control" placeholder="Enter your email" required>
            </div>

            <button class="btn-submit btn-block my-4 mb-4" type="submit">Resend Verification Email</button>
            {!! Form::close() !!}

            <p class="text-3 text-center mb-3">
                <a href="{{ url('login') }}" class="btn-link" title="login">Back to Login</a>
            </p>
          </div>
          </div>
        </div>
        </div>
       </div>
      </div>
    </div>
  </div>
</div>
<!-- Login Main Wrapper End -->
@endsection
