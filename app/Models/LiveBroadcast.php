<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveBroadcast extends Model
{
    use HasFactory;

    protected $table = 'live_broadcasts';

    protected $fillable = [
        'user_id',
        'title',
        'zoom_meeting_id',
        'zoom_join_url',
        'zoom_start_url',
        'zoom_meeting_password',
        'scheduled_at',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
