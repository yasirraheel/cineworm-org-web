<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateTier extends Model
{
    protected $fillable = ['name', 'min_referrals', 'max_referrals', 'commission_percent', 'active', 'sort_order'];

    protected $casts = [
        'active' => 'boolean',
        'commission_percent' => 'decimal:2',
    ];
}
