<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionalContactList extends Model
{
    protected $table = 'promotional_contact_lists';

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contacts()
    {
        return $this->hasMany(PromotionalContact::class, 'contact_list_id');
    }

    public function campaigns()
    {
        return $this->hasMany(PromotionalCampaign::class, 'contact_list_id');
    }
}
