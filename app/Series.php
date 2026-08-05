<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Season;
use App\Episodes;

class Series extends Model
{
    protected $table = 'series';

    protected $fillable = ['series_name','series_poster'];


	public $timestamps = false; 
	 
	
	public static function getSeriesInfo($id,$field_name) 
    { 
        static $series_cache = [];

        if (!isset($series_cache[$id])) {
		    $series_cache[$id] = Series::where('status','1')->where('id',$id)->first();
        }

        $series_info = $series_cache[$id];
		
		if($series_info)
		{
			$val = $series_info->$field_name;
			if(in_array($field_name, ['series_poster', 'series_cover']) && empty($val)) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return $val;
		}
		else
		{
			if(in_array($field_name, ['series_poster', 'series_cover'])) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return '';
		}
	}

	public static function getSeriesTotalSeason($id) 
    {
    	$total_season = Season::where('series_id',$id)->count(); 

    	return $total_season;
    }

	public static function getSeriesTotalEpisodes($id) 
    {
    	$total_episode = Episodes::where('episode_series_id',$id)->count(); 

    	return $total_episode;
    }	
}
