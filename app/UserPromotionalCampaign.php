<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPromotionalCampaign extends Model
{
    protected $table = 'user_promotional_campaigns';

    protected $fillable = [
        'admin_id',
        'title',
        'subject',
        'content',
        'audience',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
    ];

    public function queueItems()
    {
        return $this->hasMany(UserPromotionalQueue::class, 'campaign_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
