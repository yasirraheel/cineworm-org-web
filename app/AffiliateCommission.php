<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    protected $fillable = [
        'affiliate_account_id', 'referred_user_id', 'transaction_id', 'type', 'cycle_number',
        'base_amount', 'rate_percent', 'commission_amount', 'currency', 'status', 'notes',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'rate_percent' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function account() { return $this->belongsTo(AffiliateAccount::class, 'affiliate_account_id'); }
    public function user() { return $this->belongsTo(User::class, 'referred_user_id'); }
    public function transaction() { return $this->belongsTo(Transactions::class, 'transaction_id'); }
}
