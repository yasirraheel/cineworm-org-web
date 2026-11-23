<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateAccount extends Model
{
    protected $fillable = ['user_id', 'referral_code', 'cycle_number', 'referrals_count', 'total_earned', 'total_paid'];

    protected $casts = [
        'total_earned' => 'decimal:2',
        'total_paid' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function referrals() { return $this->hasMany(AffiliateReferral::class); }
    public function commissions() { return $this->hasMany(AffiliateCommission::class); }
    public function payouts() { return $this->hasMany(AffiliatePayout::class); }
}
