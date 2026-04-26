<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionalCampaign extends Model
{
    protected $table = 'promotional_campaigns';

    protected $fillable = [
        'user_id',
        'smtp_server_id',
        'sending_domain_id',
        'tracking_domain_id',
        'contact_list_id',
        'name',
        'subject',
        'preview_text',
        'from_name',
        'from_email',
        'reply_to_email',
        'html_content',
        'plain_text',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_contacts',
        'processed_contacts',
        'success_count',
        'failed_count',
        'last_error',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'smtp_server_id' => 'integer',
        'sending_domain_id' => 'integer',
        'tracking_domain_id' => 'integer',
        'contact_list_id' => 'integer',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_contacts' => 'integer',
        'processed_contacts' => 'integer',
        'success_count' => 'integer',
        'failed_count' => 'integer',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_RUNNING = 'running';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contactList()
    {
        return $this->belongsTo(PromotionalContactList::class, 'contact_list_id');
    }

    public function smtpServer()
    {
        return $this->belongsTo(PromotionalSmtpServer::class, 'smtp_server_id');
    }

    public function sendingDomain()
    {
        return $this->belongsTo(PromotionalSendingDomain::class, 'sending_domain_id');
    }

    public function trackingDomain()
    {
        return $this->belongsTo(PromotionalTrackingDomain::class, 'tracking_domain_id');
    }

    public function sends()
    {
        return $this->hasMany(PromotionalCampaignSend::class, 'campaign_id');
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return 'success';
            case self::STATUS_RUNNING:
                return 'info';
            case self::STATUS_SCHEDULED:
                return 'warning';
            case self::STATUS_PAUSED:
                return 'default';
            case self::STATUS_FAILED:
                return 'danger';
            default:
                return 'primary';
        }
    }

    public function getProgressPercentage()
    {
        if (empty($this->total_contacts)) {
            return 0;
        }

        return min(100, (int) round(($this->processed_contacts / $this->total_contacts) * 100));
    }
}
