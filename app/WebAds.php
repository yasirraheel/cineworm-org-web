<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebAds extends Model
{
    protected $table = 'web_banner_ads';

    protected $fillable = ['home_top','ad_url'];


	public $timestamps = false;

}
