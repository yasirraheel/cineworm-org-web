<?php

namespace App\Http\Controllers\Admin;

use App\AffiliateAccount;
use App\AffiliateCommission;
use App\AffiliatePayout;
use App\AffiliateReferral;
use App\AffiliateSetting;
use App\AffiliateTier;
use App\Services\AffiliateService;
use Auth;
use DB;
use Illuminate\Http\Request;

class AffiliateController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
    }

    public function index()
    {
        $this->ensureAdmin();
        $settings = AffiliateService::settings();
        $tiers = AffiliateTier::orderBy('sort_order')->get();
        $referrals = AffiliateReferral::with(['account.user', 'user'])->latest()->paginate(20, ['*'], 'referrals_page');
        $commissions = AffiliateCommission::with(['account.user', 'user'])->latest()->paginate(20, ['*'], 'commissions_page');
        $payouts = AffiliatePayout::with('account.user')->latest()->paginate(20, ['*'], 'payouts_page');
        $stats = [
            'accounts' => AffiliateAccount::count(),
            'referrals' => AffiliateReferral::count(),
            'pending_commissions' => AffiliateCommission::where('status', 'pending')->sum('commission_amount'),
            'requested_payouts' => AffiliatePayout::where('status', 'requested')->sum('amount'),
        ];

        return view('admin.pages.affiliate.index', compact('settings', 'tiers', 'referrals', 'commissions', 'payouts', 'stats'));
    }

    public function updateSettings(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'enabled' => 'nullable|boolean',
            'landing_title' => 'required|string|max:255',
            'landing_description' => 'nullable|string|max:2000',
            'recurring_commission_percent' => 'required|numeric|min:0|max:100',
            'max_referrals_per_cycle' => 'required|integer|min:1',
            'minimum_payout_amount' => 'required|numeric|min:0',
            'reset_on_payout' => 'nullable|boolean',
            'payout_instructions' => 'nullable|string|max:2000',
        ]);
        $data['enabled'] = $request->boolean('enabled');
        $data['reset_on_payout'] = $request->boolean('reset_on_payout');
        $settings = AffiliateSetting::first() ?: new AffiliateSetting();
        $settings->fill($data)->save();

        return redirect()->back()->with('affiliate_message', 'Affiliate settings updated.');
    }

    public function storeTier(Request $request)
    {
        $this->ensureAdmin();
        AffiliateTier::create($this->tierData($request));
        return redirect()->back()->with('affiliate_message', 'Affiliate tier added.');
    }

    public function updateTier(Request $request, $id)
    {
        $this->ensureAdmin();
        $tier = AffiliateTier::findOrFail($id);
        $tier->update($this->tierData($request));
        return redirect()->back()->with('affiliate_message', 'Affiliate tier updated.');
    }

    public function deleteTier($id)
    {
        $this->ensureAdmin();
        AffiliateTier::findOrFail($id)->delete();
        return redirect()->back()->with('affiliate_message', 'Affiliate tier deleted.');
    }

    public function approveCommission($id)
    {
        $this->ensureAdmin();
        AffiliateCommission::whereKey($id)->where('status', 'pending')->update(['status' => 'approved']);
        return redirect()->back()->with('affiliate_message', 'Commission approved.');
    }

    public function rejectCommission($id)
    {
        $this->ensureAdmin();
        AffiliateCommission::whereKey($id)->whereIn('status', ['pending', 'approved'])->update(['status' => 'rejected']);
        return redirect()->back()->with('affiliate_message', 'Commission rejected.');
    }

    public function approvePayout($id)
    {
        $this->ensureAdmin();
        AffiliatePayout::whereKey($id)->where('status', 'requested')->update(['status' => 'approved', 'processed_by' => Auth::id()]);
        return redirect()->back()->with('affiliate_message', 'Payout approved.');
    }

    public function rejectPayout($id)
    {
        $this->ensureAdmin();
        AffiliatePayout::whereKey($id)->whereIn('status', ['requested', 'approved'])->update(['status' => 'rejected', 'processed_by' => Auth::id()]);
        return redirect()->back()->with('affiliate_message', 'Payout rejected.');
    }

    public function markPayoutPaid($id)
    {
        $this->ensureAdmin();
        DB::transaction(function () use ($id) {
            $payout = AffiliatePayout::lockForUpdate()->findOrFail($id);
            if (!in_array($payout->status, ['approved', 'requested'])) {
                return;
            }
            $payout->status = 'paid';
            $payout->processed_by = Auth::id();
            $payout->processed_at = now();
            $payout->save();

            $account = AffiliateAccount::lockForUpdate()->find($payout->affiliate_account_id);
            if (!$account) {
                return;
            }
            $account->total_paid = (float) $account->total_paid + (float) $payout->amount;
            if (AffiliateService::settings()->reset_on_payout && (float) $account->commissions()->where('status', 'approved')->sum('commission_amount') <= 0) {
                $account->cycle_number = (int) $account->cycle_number + 1;
                $account->referrals_count = 0;
            }
            $account->save();
        });
        return redirect()->back()->with('affiliate_message', 'Payout marked as paid.');
    }

    private function tierData(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'min_referrals' => 'required|integer|min:1',
            'max_referrals' => 'nullable|integer|min:1|gte:min_referrals',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'active' => 'nullable|boolean',
            'sort_order' => 'required|integer|min:0',
        ]) + ['active' => $request->boolean('active')];
    }

    private function ensureAdmin()
    {
        if (!Auth::check() || !in_array(Auth::user()->usertype, ['Admin', 'Sub_Admin'])) {
            abort(403);
        }
    }
}
