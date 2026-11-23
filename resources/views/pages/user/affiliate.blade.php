@extends('site_app')

@section('head_title', 'Affiliate Program - ' . getcong('site_name'))

@section('content')
<div class="page-content">
    <div class="container">
        @if(session('affiliate_message')) <div class="alert alert-success">{{ session('affiliate_message') }}</div> @endif
        @if(session('affiliate_error')) <div class="alert alert-danger">{{ session('affiliate_error') }}</div> @endif

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="card-box">
                    <h1>{{ $settings->landing_title }}</h1>
                    <p>{{ $settings->landing_description }}</p>
                    <div class="form-group">
                        <label>Your referral link</label>
                        <div class="input-group">
                            <input id="affiliate-link" class="form-control" value="{{ $referralUrl }}" readonly>
                            <span class="input-group-btn"><button type="button" class="btn btn-primary" onclick="copyAffiliateLink()">Copy</button></span>
                        </div>
                    </div>
                    <p>Current tier: <strong>{{ $currentTier ? $currentTier->name . ' (' . $currentTier->commission_percent . '%)' : 'Not qualified yet' }}</strong></p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><div class="card-box"><h4>Referrals</h4><h2>{{ $account->referrals_count }}</h2></div></div>
            <div class="col-md-3"><div class="card-box"><h4>Available</h4><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format($availableBalance, 2) }}</h2></div></div>
            <div class="col-md-3"><div class="card-box"><h4>Total earned</h4><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format((float) $account->total_earned, 2) }}</h2></div></div>
            <div class="col-md-3"><div class="card-box"><h4>Total paid</h4><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format((float) $account->total_paid, 2) }}</h2></div></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card-box">
                    <h3>Request payout</h3>
                    <p>{{ $settings->payout_instructions }}</p>
                    <form method="POST" action="{{ route('affiliate.payout') }}">
                        @csrf
                        <div class="form-group"><label>Amount</label><input name="amount" class="form-control" type="number" min="0.01" step="0.01" required></div>
                        <div class="form-group"><label>Payment method</label><input name="method" class="form-control" placeholder="Bank, PayPal, etc." required></div>
                        <div class="form-group"><label>Account details</label><textarea name="account_details" class="form-control" rows="4" required></textarea></div>
                        <button class="vfx-item-btn-danger" type="submit">Request payout</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-box">
                    <h3>How it works</h3>
                    <p>Signup attribution is saved from your link. Commission is created only after a referred member completes a confirmed payment.</p>
                    <p>Tier commission: first confirmed payment. Recurring commission: {{ $settings->recurring_commission_percent }}% on later confirmed payments.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <h3>Referral activity</h3>
                    <div class="table-responsive"><table class="table"><thead><tr><th>User</th><th>Status</th><th>Date</th></tr></thead><tbody>
                    @forelse($referrals as $referral)
                        <tr><td>{{ optional($referral->user)->email ?: 'Member' }}</td><td>{{ ucfirst(str_replace('_', ' ', $referral->status)) }}</td><td>{{ $referral->created_at }}</td></tr>
                    @empty <tr><td colspan="3">No referrals yet.</td></tr> @endforelse
                    </tbody></table></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function copyAffiliateLink() {
    var field = document.getElementById('affiliate-link');
    field.select();
    document.execCommand('copy');
}
</script>
