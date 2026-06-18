@php
    $currentPlan = null;
    $planFeatureKeys = [];

    if (!empty(Auth::User()->plan_id)) {
        $currentPlan = \App\SubscriptionPlan::find(Auth::User()->plan_id);
        if ($currentPlan) {
            $planFeatureKeys = $currentPlan->getEffectiveFeatureKeys();
        }
    }

    $activePlans = \App\SubscriptionPlan::active()->orderBy('plan_price')->get();

    $featureLinkConfig = [
        'watch_content' => [
            'title' => 'Watch Content',
            'url' => URL('/'),
            'icon' => 'fa fa-play-circle',
        ],
        'donate_to_projects' => [
            'title' => 'Donate to Projects',
            'url' => getcong('donation_link') ? stripslashes(getcong('donation_link')) : 'javascript:void(0);',
            'icon' => 'fa fa-heart',
        ],
        'play_games' => [
            'title' => 'Play Games',
            'url' => URL('game/remote-control'),
            'icon' => 'fa fa-gamepad',
        ],
        'basic_user_account' => [
            'title' => 'Basic User Account',
            'url' => URL('dashboard'),
            'icon' => 'fa fa-user-circle',
        ],
        'personal_profile_page' => [
            'title' => 'Personal Profile Page',
            'url' => URL('profile'),
            'icon' => 'fa fa-id-card',
        ],
        'film_uploads' => [
            'title' => 'Film Uploads',
            'url' => URL('user/films'),
            'icon' => 'fa fa-upload',
        ],
        'promotion_services' => [
            'title' => 'Promotion Services',
            'url' => URL('promotions'),
            'icon' => 'fa fa-bullhorn',
        ],
        'deal_plus_access' => [
            'title' => 'Deal Plus Access',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-handshake',
        ],
        'crowdfunding_link_sharing' => [
            'title' => 'Crowdfunding Link Sharing',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-link',
        ],
        'website_link_sharing' => [
            'title' => 'Website Link Sharing',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-globe',
        ],
        'photo_gallery' => [
            'title' => 'Photo Gallery',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-image',
        ],
        'project_showcase_page' => [
            'title' => 'Project Showcase Page',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-film',
        ],
        'film_project_space' => [
            'title' => 'Film Project Space',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-folder-open',
        ],
        'film_editing_access' => [
            'title' => 'Film Editing Access',
            'url' => URL('user/editor'),
            'icon' => 'fa fa-cut',
        ],
        'colour_grading_access' => [
            'title' => 'Colour Grading Access',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-adjust',
        ],
        'advanced_film_showcase' => [
            'title' => 'Advanced Film Showcase',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-star',
        ],
        'pro_creator_tools' => [
            'title' => 'Pro Creator Tools',
            'url' => 'javascript:void(0);',
            'icon' => 'fa fa-wrench',
        ],
        'extended_media_uploads' => [
            'title' => 'Extended Media Uploads',
            'url' => URL('user/films/upload'),
            'icon' => 'fa fa-cloud-upload-alt',
        ],
        'priority_promotion' => [
            'title' => 'Priority Promotion',
            'url' => URL('promotions'),
            'icon' => 'fa fa-rocket',
        ],
        'job_listing' => [
            'title' => 'Job Listing',
            'url' => URL('user/jobs'),
            'icon' => 'fa fa-briefcase',
        ],
        'news_ticker' => [
            'title' => 'News Ticker',
            'url' => URL('user/news_tickers'),
            'icon' => 'fa fa-newspaper',
        ],
        'live_broadcast' => [
            'title' => 'Live Broadcast',
            'url' => URL('user/live_broadcasts'),
            'icon' => 'fa fa-podcast',
        ],
    ];
@endphp
<style>
  .dashboard-feature-sidebar {
    margin-top: 25px;
    text-align: left;
  }

  .plan-group {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }

  .dashboard-feature-sidebar h6 {
    color: #fe0278;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 15px;
    margin-top: 0;
    text-transform: uppercase;
    border-bottom: 2px solid rgba(254, 2, 120, 0.3);
    padding-bottom: 10px;
    letter-spacing: 0.5px;
  }

  .dashboard-feature-links {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin: 0;
    padding: 0;
  }

  .dashboard-feature-links a {
    align-items: center;
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 5px;
    color: #ffffff;
    display: flex;
    font-size: 14px;
    font-weight: 500;
    gap: 12px;
    line-height: 20px;
    padding: 12px 15px;
    text-decoration: none;
    transition: all .2s ease;
  }

  .dashboard-feature-links a:hover {
    background: #fe0278;
    border-color: #fe0278;
    color: #ffffff;
    transform: translateX(4px);
  }
  
  .dashboard-feature-links a.locked-feature {
    background: rgba(0, 0, 0, 0.4);
    color: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.02);
  }
  
  .dashboard-feature-links a.locked-feature:hover {
    background: rgba(254, 2, 120, 0.1);
    border-color: rgba(254, 2, 120, 0.3);
    color: rgba(255, 255, 255, 0.6);
    transform: none;
    cursor: pointer;
  }

  .dashboard-feature-links i {
    flex: 0 0 18px;
    text-align: center;
    font-size: 16px;
  }
  
  .dashboard-feature-links i.fa-lock {
    margin-left: auto;
    color: #fe0278;
    opacity: 0.5;
    flex: 0 0 auto;
  }

  @media only screen and (max-width: 991px) {
    .dashboard-feature-sidebar {
      margin-bottom: 25px;
    }

    .dashboard-feature-links {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media only screen and (max-width: 575px) {
    .dashboard-feature-links {
      grid-template-columns: 1fr;
    }
  }
</style>
<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
  <div class="img-profile">
    @if(Auth::User()->user_image)
    <img src="{{ URL::asset('upload/'.Auth::User()->user_image) }}" class="img-rounded" alt="profile pic" title="profile pic">
    @else
    <img src="{{ URL::asset('site_assets/images/user-avatar.png') }}" class="img-rounded" alt="profile_img" title="profile pic">
    @endif
  </div>
  <div class="profile_title_item">
    <h5>{{Auth::User()->name}}</h5>
    <p>{{Auth::User()->email}}</p>
    <a href="{{ URL::to('profile') }}" class="vfx-item-btn-danger text-uppercase"><i class="fa fa-edit"></i>{{trans('words.edit')}}</a><br /><br />
    <a href="#" class="vfx-item-btn-danger text-uppercase data_remove"><i class="fa fa-trash"></i>Account Delete</a>

    @if(count($activePlans) > 0)
    <div class="dashboard-feature-sidebar">
      @foreach($activePlans as $plan)
        @php
            $directFeatures = $plan->getDirectFeatureKeys();
            if (empty($directFeatures)) continue;
        @endphp
        <div class="plan-group">
          <h6>{{ $plan->plan_name }}</h6>
          <div class="dashboard-feature-links">
            @foreach($directFeatures as $featureKey)
              @if(isset($featureLinkConfig[$featureKey]))
                  @php 
                      $featureLink = $featureLinkConfig[$featureKey]; 
                      $hasAccess = in_array($featureKey, $planFeatureKeys, true);
                  @endphp
                  @if($hasAccess)
                      <a href="{{ $featureLink['url'] }}">
                          <i class="{{ $featureLink['icon'] }}"></i>
                          <span>{{ $featureLink['title'] }}</span>
                      </a>
                  @else
                      <a href="javascript:void(0);" class="locked-feature" onclick="showUpgradeWarning('{{ addslashes($plan->plan_name) }}', '{{ addslashes($featureLink['title']) }}')">
                          <i class="{{ $featureLink['icon'] }}"></i>
                          <span>{{ $featureLink['title'] }}</span>
                          <i class="fa fa-lock"></i>
                      </a>
                  @endif
              @endif
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
    @endif
  </div>
</div>

<script>
function showUpgradeWarning(planName, featureName) {
    Swal.fire({
        icon: 'warning',
        title: 'Upgrade Required',
        html: 'You need to subscribe to the <strong>' + planName + '</strong> plan to access <strong>' + featureName + '</strong>.',
        confirmButtonText: 'View Plans',
        confirmButtonColor: '#fe0278',
        showCancelButton: true,
        cancelButtonText: 'Close'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ URL::to('membership_plan') }}";
        }
    });
}
</script>
