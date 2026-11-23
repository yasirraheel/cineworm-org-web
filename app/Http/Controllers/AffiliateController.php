<?php

namespace App\Http\Controllers;

use App\AffiliateCommission;
use App\AffiliatePayout;
use App\AffiliateTier;
use App\Services\AffiliateService;
use Auth;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function landing(Request $request)
    {
        if ($request->query('ref')) {
            AffiliateService::queueReferralCookie($request->query('ref'));
        }

        $settings = AffiliateService::settings();
        $tiers = AffiliateTier::where('active', true)->orderBy('sort_order')->get();
        $account = null;
        $referralUrl = null;
        $referrals = collect();
        $commissions = collect();
        $payouts = collect();
        $availableBalance = 0;
        $currentTier = null;

        if (Auth::check()) {
            $account = AffiliateService::ensureAccount(Auth::user());
            $referralUrl = route('affiliate.referral', ['code' => $account->referral_code]);
            $referrals = $account->referrals()->with('user')->latest()->get();
            $commissions = $account->commissions()->latest()->get();
            $payouts = $account->payouts()->latest()->get();
            $availableBalance = $this->availableBalance($account);
            $currentTier = AffiliateService::currentTier($account);
        }

        return view(Auth::check() ? 'pages.user.affiliate' : 'pages.affiliate.landing', compact(
            'settings', 'tiers', 'account', 'referralUrl', 'referrals', 'commissions',
            'payouts', 'availableBalance', 'currentTier'
        ));
    }

    public function referral($code)
    {
        AffiliateService::queueReferralCookie($code);
        return redirect()->route('affiliate.landing');
    }

    public function requestPayout(Request $request)
    {
        $settings = AffiliateService::settings();
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string|max:100',
            'account_details' => 'required|string|max:2000',
        ]);

        $account = AffiliateService::ensureAccount(Auth::user());
        $available = $this->availableBalance($account);
        if ((float) $data['amount'] > $available) {
            return redirect()->back()->with('affiliate_error', 'Requested amount is greater than your available balance.');
        }
        if ((float) $data['amount'] < (float) $settings->minimum_payout_amount) {
            return redirect()->back()->with('affiliate_error', 'The minimum payout amount is ' . $settings->minimum_payout_amount . '.');
        }

        AffiliatePayout::create([
            'affiliate_account_id' => $account->id,
            'amount' => $data['amount'],
            'currency' => getcong('currency_code') ?: 'USD',
            'method' => $data['method'],
            'account_details' => $data['account_details'],
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        return redirect()->back()->with('affiliate_message', 'Your payout request has been sent for admin review.');
    }

    private function availableBalance($account)
    {
        $approved = (float) $account->commissions()->where('status', 'approved')->sum('commission_amount');
        $requested = (float) $account->payouts()->whereIn('status', ['requested', 'approved', 'paid'])->sum('amount');
        return max(0, round($approved - $requested, 2));
    }
}
