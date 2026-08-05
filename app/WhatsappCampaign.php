<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappCampaign extends Model
{
    protected $table = 'whatsapp_campaigns';

    protected $fillable = [
        'user_id',
        'contact_list_id',
        'name',
        'title',
        'message',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_contacts',
        'processed_contacts',
        'success_count',
        'failed_count',
        'skipped_count',
        'batch_size',
        'min_delay_seconds',
        'max_delay_seconds',
        'pause_after_messages',
        'pause_duration_seconds',
        'daily_limit',
        'quiet_hours_start',
        'quiet_hours_end',
        'last_error',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'contact_list_id' => 'integer',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_contacts' => 'integer',
        'processed_contacts' => 'integer',
        'success_count' => 'integer',
        'failed_count' => 'integer',
        'skipped_count' => 'integer',
        'batch_size' => 'integer',
        'min_delay_seconds' => 'integer',
        'max_delay_seconds' => 'integer',
        'pause_after_messages' => 'integer',
        'pause_duration_seconds' => 'integer',
        'daily_limit' => 'integer',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_RUNNING = 'running';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public function contactList()
    {
        return $this->belongsTo(WhatsappContactList::class, 'contact_list_id');
    }

    public function sends()
    {
        return $this->hasMany(WhatsappCampaignSend::class, 'campaign_id');
    }

    public function getProgressPercentage()
    {
        if (empty($this->total_contacts)) {
            return 0;
        }

        return min(100, (int) round(($this->processed_contacts / $this->total_contacts) * 100));
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
}
