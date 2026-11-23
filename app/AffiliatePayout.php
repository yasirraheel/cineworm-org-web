<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliatePayout extends Model
{
    protected $fillable = [
        'affiliate_account_id', 'amount', 'currency', 'method', 'account_details',
        'status', 'processed_by', 'notes', 'requested_at', 'processed_at',
    ];

    protected $casts = ['amount' => 'decimal:2', 'requested_at' => 'datetime', 'processed_at' => 'datetime'];

    public function account() { return $this->belongsTo(AffiliateAccount::class, 'affiliate_account_id'); }
}
