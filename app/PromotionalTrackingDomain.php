<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionalTrackingDomain extends Model
{
    protected $table = 'promotional_tracking_domains';

    protected $fillable = [
        'smtp_server_id',
        'domain',
        'cname_target',
        'status',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'smtp_server_id' => 'integer',
        'status' => 'integer',
    ];

    public function smtpServer()
    {
        return $this->belongsTo(PromotionalSmtpServer::class, 'smtp_server_id');
    }
}
