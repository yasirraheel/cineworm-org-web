@extends('site_app')

@section('head_title', 'Affiliate Program - ' . getcong('site_name'))

@section('content')
<div class="page-content-area vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @if(session('affiliate_message')) <div class="alert alert-success">{{ session('affiliate_message') }}</div> @endif
        @if(session('affiliate_error')) <div class="alert alert-danger">{{ session('affiliate_error') }}</div> @endif

        <div class="vfx-item-section">
            <h3>Affiliate Program</h3>
            <span class="view-more"><a href="{{ url('dashboard') }}">Back to dashboard</a></span>
        </div>

        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 mb-4">
                <div class="member-ship-option">
                    <h5 class="color-up">{{ $settings->landing_title }}</h5>
                    <p>{{ $settings->landing_description }}</p>
                    <p>Share your Cineworm link and earn commission when referred members complete confirmed payments.</p>
                    <div class="contact-form">
                        <div class="form-group">
                            <label>Your referral link</label>
                            <div class="input-group">
                                <input id="affiliate-link" class="form-control" value="{{ $referralUrl }}" readonly>
                                <span class="input-group-btn"><button type="button" class="vfx-item-btn-danger" onclick="copyAffiliateLink()">Copy</button></span>
                            </div>
                        </div>
                    </div>
                    <span class="premuim-memplan-bold-text"><strong>Current tier:</strong><span>{{ $currentTier ? $currentTier->name . ' (' . $currentTier->commission_percent . '%)' : 'Not qualified yet' }}</span></span>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 mb-4">
                <div class="member-ship-option">
                    <h5 class="color-up">Commission tiers</h5>
                    @foreach($tiers as $tier)
                        <span class="premuim-memplan-bold-text"><strong style="display:inline-block; width:90px;">{{ $tier->name }}:</strong><span>{{ $tier->min_referrals }}{{ $tier->max_referrals ? ' - ' . $tier->max_referrals : '+' }} referrals · {{ $tier->commission_percent }}%</span></span>
                    @endforeach
                    <span class="premuim-memplan-bold-text"><strong style="display:inline-block; width:90px;">Recurring:</strong><span>{{ $settings->recurring_commission_percent }}%</span></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4"><div class="member-ship-option"><h5 class="color-up">Referrals</h5><h2>{{ $account->referrals_count }}</h2></div></div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4"><div class="member-ship-option"><h5 class="color-up">Available</h5><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format($availableBalance, 2) }}</h2></div></div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4"><div class="member-ship-option"><h5 class="color-up">Total earned</h5><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format((float) $account->total_earned, 2) }}</h2></div></div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-4"><div class="member-ship-option"><h5 class="color-up">Total paid</h5><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format((float) $account->total_paid, 2) }}</h2></div></div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-4">
                <div class="member-ship-option">
                    <h5 class="color-up">Request payout</h5>
                    <p>{{ $settings->payout_instructions ?: 'Submit your request and the admin team will review it.' }}</p>
                    <div class="contact-form">
                        <form method="POST" action="{{ route('affiliate.payout') }}">
                            @csrf
                            <div class="form-group"><label>Amount</label><input name="amount" class="form-control" type="number" min="0.01" step="0.01" required></div>
                            <div class="form-group"><label>Payment method</label><input name="method" class="form-control" placeholder="Bank, PayPal, etc." required></div>
                            <div class="form-group"><label>Account details</label><textarea name="account_details" class="form-control" rows="4" required></textarea></div>
                            <button class="vfx-item-btn-danger text-uppercase" type="submit">Request payout</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-4">
                <div class="member-ship-option">
                    <h5 class="color-up">How it works</h5>
                    <p>Signup attribution is saved from your link. Commission is created only after a referred member completes a confirmed payment.</p>
                    <p>Tier commission applies to the first confirmed payment. Recurring commission is {{ $settings->recurring_commission_percent }}% on later confirmed payments.</p>
                </div>
            </div>
        </div>

        <div class="vfx-item-section"><h3>Referral activity</h3></div>
        <div class="table-wrapper"><table class="fl-table"><thead><tr><th>User</th><th>Status</th><th>Date</th></tr></thead><tbody>
            @forelse($referrals as $referral)
                <tr><td>{{ optional($referral->user)->email ?: 'Member' }}</td><td>{{ ucfirst(str_replace('_', ' ', $referral->status)) }}</td><td>{{ $referral->created_at }}</td></tr>
            @empty <tr><td colspan="3">No referrals yet.</td></tr> @endforelse
        </tbody></table></div>
    </div>
</div>
<script>
function copyAffiliateLink() {
    var field = document.getElementById('affiliate-link');
    field.select();
    document.execCommand('copy');
}
</script>
@endsection
