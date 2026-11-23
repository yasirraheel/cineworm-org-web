@extends('site_app')

@section('head_title', $settings->landing_title . ' - ' . getcong('site_name'))

@section('content')
<div class="page-content-area vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="vfx-item-section"><h3>{{ $settings->landing_title }}</h3></div>
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <div class="member-ship-option">
                    <h5 class="color-up">Share Cineworm and earn</h5>
                    <p>{{ $settings->landing_description }}</p>
                    <p>Commission is created after a referred member completes a confirmed payment.</p>
                    <a href="{{ url('login') }}" class="vfx-item-btn-danger text-uppercase">Login</a>
                    <a href="{{ url('signup') }}" class="vfx-item-btn-danger text-uppercase">Create account</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="member-ship-option">
                    <h5 class="color-up">Commission tiers</h5>
                    <div class="row">
                        @foreach($tiers as $tier)
                            <div class="col-xs-4"><strong>{{ $tier->name }}:</strong></div>
                            <div class="col-xs-8"><span class="premuim-memplan-bold-text"><span>{{ $tier->min_referrals }}{{ $tier->max_referrals ? ' - ' . $tier->max_referrals : '+' }} · {{ $tier->commission_percent }}%</span></span></div>
                        @endforeach
                        <div class="col-xs-4"><strong>Recurring:</strong></div>
                        <div class="col-xs-8"><span class="premuim-memplan-bold-text"><span>{{ $settings->recurring_commission_percent }}%</span></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
