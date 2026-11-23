<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateSetting extends Model
{
    protected $fillable = [
        'enabled', 'landing_title', 'landing_description',
        'recurring_commission_percent', 'max_referrals_per_cycle',
        'minimum_payout_amount', 'reset_on_payout', 'payout_instructions',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'reset_on_payout' => 'boolean',
        'recurring_commission_percent' => 'decimal:2',
        'minimum_payout_amount' => 'decimal:2',
    ];
}
