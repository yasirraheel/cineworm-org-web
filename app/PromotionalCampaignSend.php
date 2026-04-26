<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionalCampaignSend extends Model
{
    protected $table = 'promotional_campaign_sends';

    protected $fillable = [
        'campaign_id',
        'contact_id',
        'email',
        'subject',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'campaign_id' => 'integer',
        'contact_id' => 'integer',
        'sent_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED = 'skipped';

    public function campaign()
    {
        return $this->belongsTo(PromotionalCampaign::class, 'campaign_id');
    }

    public function contact()
    {
        return $this->belongsTo(PromotionalContact::class, 'contact_id');
    }
}
