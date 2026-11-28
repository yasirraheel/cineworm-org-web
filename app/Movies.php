<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movies extends Model
{
    protected $table = 'movie_videos';

    protected $fillable = ['video_title','video_image','added_by','file_id','is_owner'];


	public $timestamps = false;

    public function isCreatorPaidFilm(): bool
    {
        return (bool) $this->creator_sale_enabled
            && $this->creator_sale_status === 'approved'
            && (float) $this->creator_price > 0;
    }

    public function isCreatorFilm(): bool
    {
        return (bool) $this->is_owner && !empty($this->added_by);
    }

    public function scopeRandomStreamable($query)
    {
        return $this->scopeFreeStreamable($query);
    }

    /**
     * Films that are allowed in Cineworm's normal/free catalogue.
     * Approved paid creator films belong to Cinex and must never be mixed
     * into the free/random catalogue.
     */
    public function scopeFreeStreamable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('creator_sale_enabled')
                ->orWhere('creator_sale_enabled', 0)
                ->orWhere('creator_sale_status', '!=', 'approved');
        });
    }



	public static function getMoviesInfo($id,$field_name)
    {
        static $movies_cache = [];

        if (!isset($movies_cache[$id])) {
    	    $movies_cache[$id] = Movies::where('status','1')->where('id',$id)->first();
        }

        $movie_info = $movies_cache[$id];

		if($movie_info)
		{
			$val = $movie_info->$field_name;
			if(in_array($field_name, ['video_image', 'video_image_thumb']) && empty($val)) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return $val;
		}
		else
		{
			if(in_array($field_name, ['video_image', 'video_image_thumb'])) {
				return 'site_assets/images/poster_placeholder.png';
			}
			return '';
		}

	}
    public function genre()
    {
        return $this->belongsTo(Genres::class, 'genre_id');
    }



}
