<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatermelonScore extends Model
{
    protected $table = 'watermelon_leaderboard';

    protected $fillable = ['user_id', 'player_name', 'score', 'guest_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
