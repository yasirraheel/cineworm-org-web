<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'name',
        'status',
        'unsubscribe_token',
        'ip_address',
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->unsubscribe_token)) {
                $model->unsubscribe_token = Str::random(32);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getUnsubscribeUrlAttribute()
    {
        return url('newsletter/unsubscribe/' . $this->unsubscribe_token);
    }
}
