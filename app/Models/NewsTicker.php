<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsTicker extends Model
{
    use HasFactory;

    protected $table = 'news_tickers';

    protected $fillable = [
        'headline',
        'details',
        'is_breaking',
        'status',
    ];
}
