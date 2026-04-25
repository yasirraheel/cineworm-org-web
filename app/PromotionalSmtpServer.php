<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PromotionalSmtpServer extends Model
{
    protected $table = 'promotional_smtp_servers';

    protected $fillable = [
        'server_name',
        'gateway_type',
        'from_name',
        'sender_email',
        'reply_to_email',
        'host',
        'port',
        'encryption',
        'username',
        'smtp_password',
        'ehlo_domain',
        'min_delay_per_message',
        'max_delay_per_message',
        'pause_after_messages',
        'pause_duration',
        'reset_counter_after_messages',
        'max_messages_per_day',
        'status',
        'is_default',
        'notes',
    ];

    protected $casts = [
        'status' => 'integer',
        'is_default' => 'integer',
        'port' => 'integer',
        'min_delay_per_message' => 'integer',
        'max_delay_per_message' => 'integer',
        'pause_after_messages' => 'integer',
        'pause_duration' => 'integer',
        'reset_counter_after_messages' => 'integer',
        'max_messages_per_day' => 'integer',
    ];

    public function sendingDomains()
    {
        return $this->hasMany(PromotionalSendingDomain::class, 'smtp_server_id');
    }

    public function trackingDomains()
    {
        return $this->hasMany(PromotionalTrackingDomain::class, 'smtp_server_id');
    }

    public function getDecryptedPasswordAttribute()
    {
        if (empty($this->smtp_password)) {
            return '';
        }

        try {
            return Crypt::decrypt($this->smtp_password);
        } catch (\Exception $exception) {
            return '';
        }
    }
}
