<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPromotionalQueue extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;
    const STATUS_FAILED = 2;

    protected $table = 'user_promotional_queue';

    protected $fillable = [
        'campaign_id',
        'user_id',
        'email',
        'name',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(UserPromotionalCampaign::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
