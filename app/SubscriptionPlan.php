<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plan';

    protected $fillable = ['plan_name','plan_days','plan_duration','plan_price','included_plan_id','features'];

    protected $casts = [
        'features' => 'array',
    ];

    public const AVAILABLE_FEATURES = [
        'watch_content' => 'Watch Content',
        'donate_to_projects' => 'Donate to Projects',
        'play_games' => 'Play Games',
        'basic_user_account' => 'Basic User Account',
        'personal_profile_page' => 'Personal Profile Page',
        'promotion_services' => 'Promotion Services',
        'deal_plus_access' => 'Deal Plus Access',
        'film_uploads' => 'Film Uploads',
        'crowdfunding_link_sharing' => 'Crowdfunding Link Sharing',
        'website_link_sharing' => 'Website Link Sharing',
        'photo_gallery' => 'Photo Gallery',
        'project_showcase_page' => 'Project Showcase Page',
        'film_project_space' => 'Film Project Space',
        'film_editing_access' => 'Film Editing Access',
        'colour_grading_access' => 'Colour Grading Access',
        'advanced_film_showcase' => 'Advanced Film Showcase',
        'pro_creator_tools' => 'Pro Creator Tools',
        'extended_media_uploads' => 'Extended Media Uploads',
        'priority_promotion' => 'Priority Promotion',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($subQuery) {
            $subQuery->where('status', 1)
                ->orWhere('status', '1')
                ->orWhereRaw('LOWER(CAST(status AS CHAR)) IN (?, ?, ?)', ['true', 'on', 'active']);
        });
    }

	public $timestamps = false; 

    public function includedPlan()
    {
        return $this->belongsTo(self::class, 'included_plan_id');
    }

    public function getDirectFeatureKeys(): array
    {
        return array_values(array_intersect((array) $this->features, array_keys(self::AVAILABLE_FEATURES)));
    }

    public function getInheritedFeatureKeys(array $visitedPlanIds = []): array
    {
        if (empty($this->included_plan_id) || in_array($this->included_plan_id, $visitedPlanIds, true)) {
            return [];
        }

        $includedPlan = self::find($this->included_plan_id);

        if (!$includedPlan) {
            return [];
        }

        $visitedPlanIds[] = $this->included_plan_id;

        return array_values(array_unique(array_merge(
            $includedPlan->getInheritedFeatureKeys($visitedPlanIds),
            $includedPlan->getDirectFeatureKeys()
        )));
    }

    public function getEffectiveFeatureKeys(): array
    {
        return array_values(array_unique(array_merge(
            $this->getInheritedFeatureKeys(),
            $this->getDirectFeatureKeys()
        )));
    }

    public function getEffectiveFeatureLabels(): array
    {
        return array_values(array_intersect_key(self::AVAILABLE_FEATURES, array_flip($this->getEffectiveFeatureKeys())));
    }

    public function wouldCreateInheritanceLoop($includedPlanId): bool
    {
        if (empty($includedPlanId) || empty($this->id)) {
            return false;
        }

        $visitedPlanIds = [];
        $nextPlanId = (int) $includedPlanId;

        while ($nextPlanId) {
            if ($nextPlanId === (int) $this->id || in_array($nextPlanId, $visitedPlanIds, true)) {
                return true;
            }

            $visitedPlanIds[] = $nextPlanId;
            $nextPlanId = (int) self::where('id', $nextPlanId)->value('included_plan_id');
        }

        return false;
    }
	 
	
	public static function getSubscriptionPlanInfo($id,$field_name) 
    { 
 
		$plan_info = SubscriptionPlan::where('id',$id)->first();
		
		if($plan_info)
		{
			return  $plan_info->$field_name;
		}
		else
		{
			return  '';
		}
	}


	public static function getPlanDuration($id) 
    { 
		$plan_obj = SubscriptionPlan::find($id);

		if($plan_obj->plan_duration_type==1)
		{
			$plan_duration_type='Day(s)';
		}
		else if($plan_obj->plan_duration_type==30)
		{
			$plan_duration_type='Month(s)';
		}
		else
		{
			$plan_duration_type='Year(s)';
		}

		$duration_final=$plan_obj->plan_duration.' '.$plan_duration_type; 

		return $duration_final;
	}

	
}
