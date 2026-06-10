<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plan';

    public const DEFAULT_SIGNUP_FEATURE_FLAG = '__default_signup_plan';

    protected $fillable = ['plan_name','plan_days','plan_duration','plan_price','included_plan_id','included_plan_ids','features'];

    protected $casts = [
        'included_plan_ids' => 'array',
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
        'job_listing' => 'Job Listing',
        'news_ticker' => 'News Ticker',
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

    public function getIncludedPlanIds(): array
    {
        $includedPlanIds = array_filter(array_map('intval', (array) $this->included_plan_ids));

        if (!empty($this->included_plan_id)) {
            $includedPlanIds[] = (int) $this->included_plan_id;
        }

        return array_values(array_unique($includedPlanIds));
    }

    public function getIncludedPlans()
    {
        $includedPlanIds = $this->getIncludedPlanIds();

        if (empty($includedPlanIds)) {
            return collect();
        }

        return self::whereIn('id', $includedPlanIds)
            ->get()
            ->sortBy(function ($plan) use ($includedPlanIds) {
                return array_search((int) $plan->id, $includedPlanIds, true);
            })
            ->values();
    }

    public function getIncludedPlanNames(): array
    {
        return $this->getIncludedPlans()->pluck('plan_name')->all();
    }

    public function getDirectFeatureKeys(): array
    {
        return array_values(array_intersect((array) $this->features, array_keys(self::AVAILABLE_FEATURES)));
    }

    public function getRawFeatureKeys(): array
    {
        return array_values(array_filter((array) $this->features));
    }

    public function isDefaultSignupPlan(): bool
    {
        return in_array(self::DEFAULT_SIGNUP_FEATURE_FLAG, $this->getRawFeatureKeys(), true);
    }

    public static function getDefaultSignupPlan()
    {
        return self::active()
            ->orderBy('id')
            ->get()
            ->first(function (self $plan) {
                return $plan->isDefaultSignupPlan();
            });
    }

    public function getInheritedFeatureKeys(array $visitedPlanIds = []): array
    {
        $includedPlanIds = array_diff($this->getIncludedPlanIds(), $visitedPlanIds);

        if (empty($includedPlanIds)) {
            return [];
        }

        $inheritedFeatures = [];

        foreach (self::whereIn('id', $includedPlanIds)->get() as $includedPlan) {
            $nextVisitedPlanIds = array_merge($visitedPlanIds, [$includedPlan->id]);

            $inheritedFeatures = array_merge(
                $inheritedFeatures,
                $includedPlan->getInheritedFeatureKeys($nextVisitedPlanIds),
                $includedPlan->getDirectFeatureKeys()
            );
        }

        return array_values(array_unique($inheritedFeatures));
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

    public function getDirectFeatureLabels(): array
    {
        return array_values(array_intersect_key(self::AVAILABLE_FEATURES, array_flip($this->getDirectFeatureKeys())));
    }

    public function wouldCreateInheritanceLoop($includedPlanIds): bool
    {
        if (empty($includedPlanIds) || empty($this->id)) {
            return false;
        }

        $includedPlanIds = array_filter(array_map('intval', (array) $includedPlanIds));
        $planIdsToCheck = $includedPlanIds;
        $visitedPlanIds = [];

        while (!empty($planIdsToCheck)) {
            $nextPlanId = array_shift($planIdsToCheck);

            if (!$nextPlanId) {
                continue;
            }

            if ($nextPlanId === (int) $this->id || in_array($nextPlanId, $visitedPlanIds, true)) {
                return true;
            }

            $visitedPlanIds[] = $nextPlanId;
            $nextPlan = self::find($nextPlanId);

            if ($nextPlan) {
                $planIdsToCheck = array_merge($planIdsToCheck, $nextPlan->getIncludedPlanIds());
            }
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
