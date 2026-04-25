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
        'notes',
    ];

    protected $casts = [
        'smtp_server_id' => 'integer',
        'dkim_status' => 'integer',
        'spf_status' => 'integer',
        'dmarc_status' => 'integer',
        'status' => 'integer',
    ];

    public function smtpServer()
    {
        return $this->belongsTo(PromotionalSmtpServer::class, 'smtp_server_id');
    }
}
