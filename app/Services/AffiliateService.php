<?php

namespace App\Services;

use App\AffiliateAccount;
use App\AffiliateCommission;
use App\AffiliateReferral;
use App\AffiliateSetting;
use App\AffiliateTier;
use App\Transactions;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateService
{
    public static function settings(): AffiliateSetting
    {
        return AffiliateSetting::first() ?: new AffiliateSetting([
            'enabled' => true,
            'landing_title' => 'Earn more with Cineworm referrals',
            'landing_description' => 'Share Cineworm with your audience and earn commission on confirmed subscriptions.',
            'recurring_commission_percent' => 5,
            'max_referrals_per_cycle' => 15,
            'minimum_payout_amount' => 0,
            'reset_on_payout' => true,
        ]);
    }

    public static function queueReferralCookie(?string $code): void
    {
        $code = strtoupper(trim((string) $code));
        if ($code !== '' && AffiliateAccount::where('referral_code', $code)->exists()) {
            Cookie::queue(cookie('affiliate_referral_code', $code, 60 * 24 * 30));
        }
    }

    public static function ensureAccount(User $user): AffiliateAccount
    {
        $account = AffiliateAccount::firstOrCreate(
            ['user_id' => $user->id],
            ['referral_code' => self::newCode(), 'cycle_number' => 1]
        );

        return $account;
    }

    public static function recordSignup(User $user, ?string $code = null): ?AffiliateReferral
    {
        if (!self::settings()->enabled) {
            return null;
        }

        $code = strtoupper(trim((string) ($code ?: request()->cookie('affiliate_referral_code'))));
        if ($code === '') {
            return null;
        }

        $account = AffiliateAccount::where('referral_code', $code)->first();
        if (!$account || (int) $account->user_id === (int) $user->id) {
            return null;
        }

        return AffiliateReferral::firstOrCreate(
            ['referred_user_id' => $user->id],
            [
                'affiliate_account_id' => $account->id,
                'referral_code' => $code,
                'status' => 'pending',
                'cycle_number' => $account->cycle_number,
            ]
        );
    }

    public static function recordTransaction(Transactions $transaction): void
    {
        if (!$transaction->user_id || (float) $transaction->payment_amount <= 0 || !self::settings()->enabled) {
            return;
        }

        $referral = AffiliateReferral::where('referred_user_id', $transaction->user_id)->first();
        if (!$referral || $referral->status === 'rejected') {
            return;
        }

        if (AffiliateCommission::where('transaction_id', $transaction->id)->exists()) {
            return;
        }

        $previousPayments = Transactions::where('user_id', $transaction->user_id)
            ->where('payment_amount', '>', 0)
            ->where('id', '<', $transaction->id)
            ->count();

        $account = AffiliateAccount::find($referral->affiliate_account_id);
        if (!$account) {
            return;
        }

        $settings = self::settings();
        $type = $previousPayments === 0 ? 'first_payment' : 'recurring';
        $cycleNumber = (int) $referral->cycle_number;

        if ($type === 'first_payment') {
            $nextReferralNumber = (int) $account->referrals_count + 1;
            $tier = AffiliateTier::where('active', true)
                ->where('min_referrals', '<=', $nextReferralNumber)
                ->where(function ($query) use ($nextReferralNumber) {
                    $query->whereNull('max_referrals')->orWhere('max_referrals', '>=', $nextReferralNumber);
                })
                ->orderByDesc('min_referrals')
                ->first();

            if (!$tier || ($settings->max_referrals_per_cycle && $nextReferralNumber > $settings->max_referrals_per_cycle)) {
                $referral->status = 'qualified_without_commission';
                $referral->qualified_at = now();
                $referral->first_transaction_id = $transaction->id;
                $referral->save();
                return;
            }

            $rate = (float) $tier->commission_percent;
            $account->referrals_count = $nextReferralNumber;
            $referral->status = 'qualified';
            $referral->qualified_at = now();
            $referral->first_transaction_id = $transaction->id;
            $referral->save();
        } else {
            $rate = (float) $settings->recurring_commission_percent;
        }

        if ($rate <= 0) {
            return;
        }

        $amount = round(((float) $transaction->payment_amount * $rate) / 100, 2);
        DB::transaction(function () use ($account, $transaction, $referral, $type, $cycleNumber, $rate, $amount) {
            AffiliateCommission::create([
                'affiliate_account_id' => $account->id,
                'referred_user_id' => $transaction->user_id,
                'transaction_id' => $transaction->id,
                'type' => $type,
                'cycle_number' => $cycleNumber,
                'base_amount' => $transaction->payment_amount,
                'rate_percent' => $rate,
                'commission_amount' => $amount,
                'currency' => getcong('currency_code') ?: 'USD',
                'status' => 'pending',
            ]);

            $account->total_earned = (float) $account->total_earned + $amount;
            $account->save();
        });
    }

    public static function currentTier(AffiliateAccount $account): ?AffiliateTier
    {
        $number = (int) $account->referrals_count;
        if ($number < 1) {
            return null;
        }

        return AffiliateTier::where('active', true)
            ->where('min_referrals', '<=', $number)
            ->where(function ($query) use ($number) {
                $query->whereNull('max_referrals')->orWhere('max_referrals', '>=', $number);
            })
            ->orderByDesc('min_referrals')
            ->first();
    }

    private static function newCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (AffiliateAccount::where('referral_code', $code)->exists());

        return $code;
    }
}
