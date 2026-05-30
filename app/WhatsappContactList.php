<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappContactList extends Model
{
    protected $table = 'whatsapp_contact_lists';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'status' => 'integer',
    ];

    public function contacts()
    {
        return $this->hasMany(WhatsappContact::class, 'contact_list_id');
    }

    public function campaigns()
    {
        return $this->hasMany(WhatsappCampaign::class, 'contact_list_id');
    }
}
