<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilmPurchase extends Model
{
    protected $table = 'film_purchases';

    protected $fillable = [
        'movie_id', 'buyer_id', 'seller_id', 'gateway',
        'provider_order_id', 'provider_payment_id', 'currency',
        'film_amount', 'platform_fee', 'total_amount', 'status',
        'paid_at', 'metadata',
    ];

    protected $casts = [
        'film_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function movie()
    {
        return $this->belongsTo(Movies::class, 'movie_id');
    }

    public function buyer()
    {
        return $this->belongsTo(\App\Models\User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(\App\Models\User::class, 'seller_id');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
