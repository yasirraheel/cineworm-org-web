<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButtonsBanners extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'buttons_banners';

    // Fillable fields for mass assignment
    protected $fillable = [
        'title',
        'image',
        'type',
        'link',
        'color',
    ];

    // Disable timestamps if not needed
    public $timestamps = true;
}
