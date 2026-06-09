<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    protected $table = 'job_listings';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'company',
        'location',
        'salary',
        'contact_details',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
