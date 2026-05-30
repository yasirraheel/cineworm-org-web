<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappCampaignSend extends Model
{
    protected $table = 'whatsapp_campaign_sends';

    protected $fillable = [
        'campaign_id',
        'contact_id',
        'phone',
        'message',
        'status',
        'attempts',
        'sent_at',
        'error_message',
        'provider_response',
    ];

    protected $casts = [
        'campaign_id' => 'integer',
        'contact_id' => 'integer',
        'attempts' => 'integer',
        'sent_at' => 'datetime',
        'provider_response' => 'array',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED = 'skipped';

    public function campaign()
    {
        return $this->belongsTo(WhatsappCampaign::class, 'campaign_id');
    }

    public function contact()
    {
        return $this->belongsTo(WhatsappContact::class, 'contact_id');
    }
}
