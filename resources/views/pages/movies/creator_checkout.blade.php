@extends('site_app')

@section('head_title', 'Buy ' . stripslashes($movie->video_title) . ' | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="vfx-item-ptb">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="table-wrapper">
                    <h2>Buy this creator film</h2>
                    <h4 class="mt-3">{{ stripslashes($movie->video_title) }}</h4>
                    <div class="mt-4">
                        <p><strong>Film price:</strong> {{ $totals['currency'] }} {{ number_format($totals['film_amount'], 2) }}</p>
                        @if(!$totals['fee_included'])
                            <p><strong>Platform fee:</strong> {{ $totals['currency'] }} {{ number_format($totals['platform_fee'], 2) }}</p>
                        @endif
                        <p class="h4"><strong>Total:</strong> {{ $totals['currency'] }} {{ number_format($totals['total_amount'], 2) }}</p>
                    </div>

                    @if(Session::has('error_flash_message'))
                        <div class="alert alert-danger mt-3">{{ Session::get('error_flash_message') }}</div>
                    @endif

                    <div class="mt-4 d-flex flex-wrap" style="gap:10px;">
                        @if($paypalEnabled)
                            <a href="{{ route('creator-films.paypal.pay', $movie->id) }}" class="vfx-item-btn-danger text-uppercase">Pay with PayPal</a>
                        @endif
                        @if($stripeEnabled)
                            <a href="{{ route('creator-films.stripe.pay', $movie->id) }}" class="vfx-item-btn text-uppercase">Pay with card</a>
                        @endif
                    </div>
                    @if(!$paypalEnabled && !$stripeEnabled)
                        <p class="mt-3">Online payment is not available yet. Please contact the administrator.</p>
                    @endif
                    <p class="mt-4"><a href="{{ route('creator-films.show', ['slug' => $movie->video_slug, 'id' => $movie->id]) }}">Back to film</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
