<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveTV extends Model
{
    protected $table = 'channels_list';

    protected $fillable = ['channel_name','channel_thumb','webpage_url','donation_funding_url'];


	public $timestamps = false;



	public static function getLiveTvInfo($id,$field_name)
    {
		$livetv_info = LiveTV::where('status','1')->where('id',$id)->first();

		if($livetv_info)
		{
			$val = $livetv_info->$field_name;
			if(in_array($field_name, ['channel_thumb']) && empty($val)) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return $val;
		}
		else
		{
			if(in_array($field_name, ['channel_thumb'])) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return '';
		}
	}


}
