<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionalSendingDomain extends Model
{
    protected $table = 'promotional_sending_domains';

    protected $fillable = [
        'smtp_server_id',
        'domain',
        'selector',
        'dkim_type',
        'dkim_value',
        'return_path_subdomain',
        'spf_value',
        'dmarc_policy',
        'dmarc_report_email',
        'dmarc_alignment',
        'dkim_status',
        'spf_status',
        'dmarc_status',
        'status',
        'verified_at',
        'dkim_private_key',
        'dkim_public_key',
        'dns_checked_at',
    ];

    protected $casts = [
        'smtp_server_id' => 'integer',
        'dkim_status' => 'integer',
        'spf_status' => 'integer',
        'dmarc_status' => 'integer',
        'status' => 'integer',
        'verified_at' => 'datetime',
        'dns_checked_at' => 'datetime',
    ];

    public function smtpServer()
    {
        return $this->belongsTo(PromotionalSmtpServer::class, 'smtp_server_id');
    }

    public function isDkimConfigured()
    {
        return !empty($this->dkim_private_key) && !empty($this->dkim_public_key);
    }

    public function getVerificationScoreAttribute()
    {
        return (int) $this->dkim_status + (int) $this->spf_status + (int) $this->dmarc_status;
    }
}
