<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sports extends Model
{
    protected $table = 'sports_videos';

    protected $fillable = ['sports_cat_id','video_title','video_slug','video_image'];


	public $timestamps = false;
 
	 
	
	public static function getSportsInfo($id,$field_name) 
    { 
		$sports_info = Sports::where('status','1')->where('id',$id)->first();
		
		if($sports_info)
		{
			$val = $sports_info->$field_name;
			if(in_array($field_name, ['video_image']) && empty($val)) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return $val;
		}
		else
		{
			if(in_array($field_name, ['video_image'])) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return '';
		}
	}

	
}
