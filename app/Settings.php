<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $fillable = ['site_name','currency_symbol', 'site_email', 'site_logo', 'site_default_movie_thumb', 'site_default_movie_poster', 'site_meta_image', 'site_favicon','site_description','site_header_code','site_footer_code','site_copyright','comments_approval','donation_link', 'zoom_client_id', 'zoom_client_secret'];



	 public $timestamps = false;

}
