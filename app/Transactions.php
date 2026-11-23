<?php

namespace App;

use App\Services\AffiliateService;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = 'transaction';

    protected $fillable = ['email', 'plan_id','gateway','payment_id'];


	public $timestamps = false;

    protected static function booted()
    {
        static::created(function (Transactions $transaction) {
            try {
                AffiliateService::recordTransaction($transaction);
            } catch (\Throwable $e) {
                \Log::error('Affiliate commission processing failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
 
	  
}
