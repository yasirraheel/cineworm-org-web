@extends('site_app')

@section('head_title', 'Newsletter Unsubscribe | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="page-content-area vfx-item-ptb" style="min-height: 60vh; display: flex; align-items: center; justify-content: center; background-color: #0b0b0e;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12" style="margin: 40px auto;">
                <div style="background: #18181c; border: 1px solid #28282e; border-radius: 10px; padding: 40px 30px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    @if(isset($is_resubscribed) && $is_resubscribed)
                        <div style="width: 70px; height: 70px; background: rgba(40, 167, 69, 0.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fa fa-check" style="font-size: 32px; color: #28a745;"></i>
                        </div>
                        <h2 style="color: #ffffff; font-size: 24px; margin-bottom: 12px;">Welcome Back!</h2>
                        <p style="color: #a6a6b5; font-size: 15px; line-height: 1.6; margin-bottom: 25px;">
                            {{ $message }}
                        </p>
                        <a href="{{ url('/') }}" class="btn btn-primary" style="background-color: #ff4d00; border-color: #ff4d00; padding: 10px 26px; border-radius: 6px; font-weight: 600; color: #fff;">
                            Return to Homepage
                        </a>
                    @elseif($success)
                        <div style="width: 70px; height: 70px; background: rgba(255, 77, 0, 0.12); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fa fa-envelope-o" style="font-size: 30px; color: #ff4d00;"></i>
                        </div>
                        <h2 style="color: #ffffff; font-size: 24px; margin-bottom: 12px;">Unsubscribed</h2>
                        <p style="color: #a6a6b5; font-size: 15px; line-height: 1.6; margin-bottom: 15px;">
                            {{ $message }}
                        </p>
                        @if($subscriber)
                            <p style="color: #727282; font-size: 13px; margin-bottom: 25px;">
                                Email: <strong style="color: #d1d1db;">{{ $subscriber->email }}</strong>
                            </p>
                            <form action="{{ url('newsletter/resubscribe/' . $subscriber->unsubscribe_token) }}" method="POST" style="margin-bottom: 15px;">
                                @csrf
                                <button type="submit" class="btn btn-outline-light" style="border: 1px solid #454552; color: #d1d1db; background: transparent; padding: 8px 20px; border-radius: 6px; font-size: 14px;">
                                    Did this by mistake? Re-subscribe
                                </button>
                            </form>
                        @endif
                        <div>
                            <a href="{{ url('/') }}" style="color: #ff4d00; font-size: 14px; text-decoration: none;">&larr; Return to Homepage</a>
                        </div>
                    @else
                        <div style="width: 70px; height: 70px; background: rgba(220, 53, 69, 0.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fa fa-exclamation-triangle" style="font-size: 30px; color: #dc3545;"></i>
                        </div>
                        <h2 style="color: #ffffff; font-size: 24px; margin-bottom: 12px;">Link Expired or Invalid</h2>
                        <p style="color: #a6a6b5; font-size: 15px; line-height: 1.6; margin-bottom: 25px;">
                            {{ $message }}
                        </p>
                        <a href="{{ url('/') }}" class="btn btn-primary" style="background-color: #ff4d00; border-color: #ff4d00; padding: 10px 26px; border-radius: 6px; font-weight: 600; color: #fff;">
                            Return to Homepage
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
