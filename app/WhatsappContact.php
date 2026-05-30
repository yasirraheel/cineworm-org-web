<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappContact extends Model
{
    protected $table = 'whatsapp_contacts';

    protected $fillable = [
        'contact_list_id',
        'name',
        'phone',
        'company',
        'tags',
        'status',
        'last_sent_at',
        'opt_out_at',
    ];

    protected $casts = [
        'contact_list_id' => 'integer',
        'status' => 'integer',
        'last_sent_at' => 'datetime',
        'opt_out_at' => 'datetime',
    ];

    public function list()
    {
        return $this->belongsTo(WhatsappContactList::class, 'contact_list_id');
    }

    public function sends()
    {
        return $this->hasMany(WhatsappCampaignSend::class, 'contact_id');
    }

    public function getIsOptedOutAttribute()
    {
        return !empty($this->opt_out_at);
    }
}
