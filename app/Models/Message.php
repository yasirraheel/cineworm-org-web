<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'guest_token',
        'guest_name',
        'message',
        'sender',
        'sender_type',
        'is_read',
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSenderNameAttribute()
    {
        if ($this->sender === 'admin' || $this->sender_type === 'admin') {
            return 'Support Agent';
        }
        if ($this->user_id && $this->user) {
            return $this->user->name;
        }
        return !empty($this->guest_name) ? $this->guest_name : 'Guest';
    }
}

