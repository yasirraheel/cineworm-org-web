<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionalContact extends Model
{
    protected $table = 'promotional_contacts';

    protected $fillable = [
        'user_id',
        'contact_list_id',
        'name',
        'email',
        'company',
        'tags',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'contact_list_id' => 'integer',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function list()
    {
        return $this->belongsTo(PromotionalContactList::class, 'contact_list_id');
    }

    public function sends()
    {
        return $this->hasMany(PromotionalCampaignSend::class, 'contact_id');
    }
}
