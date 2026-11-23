<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateReferral extends Model
{
    protected $fillable = ['affiliate_account_id', 'referred_user_id', 'referral_code', 'status', 'cycle_number', 'first_transaction_id', 'qualified_at'];

    protected $casts = ['qualified_at' => 'datetime'];

    public function account() { return $this->belongsTo(AffiliateAccount::class, 'affiliate_account_id'); }
    public function user() { return $this->belongsTo(User::class, 'referred_user_id'); }
}
