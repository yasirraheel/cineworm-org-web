@extends('site_app')

@section('head_title', $settings->landing_title . ' - ' . getcong('site_name'))

@section('content')
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="card-box">
                    <h1>{{ $settings->landing_title }}</h1>
                    <p>{{ $settings->landing_description }}</p>
                    <p>Share your Cineworm referral link and earn commission when referred members complete confirmed payments.</p>
                    <p>
                        <a href="{{ url('login') }}" class="vfx-item-btn-danger">Login</a>
                        <a href="{{ url('signup') }}" class="vfx-item-btn-danger">Create account</a>
                    </p>
                </div>
            </div>
        </div>

        @if($tiers->count())
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="card-box">
                    <h3>Commission tiers</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>Confirmed referrals</th><th>Commission</th></tr></thead>
                            <tbody>
                            @foreach($tiers as $tier)
                                <tr>
                                    <td>{{ $tier->min_referrals }}{{ $tier->max_referrals ? ' - ' . $tier->max_referrals : '+' }}</td>
                                    <td>{{ $tier->commission_percent }}%</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p>Recurring commission: {{ $settings->recurring_commission_percent }}%</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
