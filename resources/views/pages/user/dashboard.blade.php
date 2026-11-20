@extends('site_app')

@section('head_title', trans('words.dashboard_text').' | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

@php
  $activePlans = \App\SubscriptionPlan::active()->orderBy('plan_price')->get();
  $featureLinkConfig = \App\PlanFeature::getActiveFeaturesConfig();
  $planFeatureKeys = $currentPlan ? $currentPlan->getEffectiveFeatureKeys() : [];
  $renderedWorkstationFeatures = [];
@endphp

<style>
  .dashboard-card-grid {
    display: flex;
    flex-wrap: wrap;
  }

  .dashboard-card-col {
    margin-bottom: 30px;
  }

  .dashboard-card-col .member-ship-option {
    height: 100%;
  }

  .dashboard-card-action {
    display: block;
    width: 100%;
    text-align: center;
    padding: 12px;
    box-sizing: border-box;
    text-decoration: none;
  }

  .dashboard-hero {
    padding-bottom: 42px;
  }

  .dashboard-hero-summary {
    display: grid;
    gap: 18px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin-top: 30px;
  }

  .dashboard-hero-card {
    background: linear-gradient(135deg, rgba(42, 22, 83, .98), rgba(20, 10, 49, .96));
    border: 1px solid rgba(254, 2, 120, .42);
    border-radius: 14px;
    box-shadow: 0 14px 38px rgba(0, 0, 0, .24), inset 0 1px 0 rgba(255, 255, 255, .08);
    overflow: hidden;
    padding: 22px 24px;
    position: relative;
    transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
  }

  .dashboard-hero-card::before {
    background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, .12) 48%, transparent 72%);
    content: '';
    height: 180%;
    left: -80%;
    pointer-events: none;
    position: absolute;
    top: -40%;
    transform: rotate(18deg);
    transition: left .7s ease;
    width: 55%;
  }

  .dashboard-hero-card:hover {
    border-color: #fe0278;
    box-shadow: 0 18px 48px rgba(254, 2, 120, .22), 0 0 24px rgba(254, 2, 120, .12), inset 0 1px 0 rgba(255, 255, 255, .12);
    transform: translateY(-5px);
  }

  .dashboard-hero-card:hover::before {
    left: 125%;
  }

  .dashboard-hero-card h5 {
    color: #ffffff;
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 16px;
    position: relative;
  }

  .dashboard-hero-card h5::after {
    background: #fe0278;
    border-radius: 3px;
    content: '';
    display: block;
    height: 3px;
    margin-top: 8px;
    width: 42px;
  }

  .dashboard-hero-card .premuim-memplan-bold-text {
    position: relative;
  }

  .cineworm-workstation {
    background: #1c103c;
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 8px;
    margin-top: 2px;
    overflow: hidden;
    padding: 24px;
  }

  .cineworm-workstation-heading {
    align-items: flex-start;
    display: flex;
    gap: 16px;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .cineworm-workstation-heading h4 {
    color: #ffffff;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 6px;
  }

  .cineworm-workstation-heading h4 span {
    color: #fe0278;
  }

  .cineworm-workstation-heading p {
    color: rgba(255, 255, 255, .65);
    font-size: 13px;
    margin: 0;
  }

  .cineworm-workstation-badge {
    background: rgba(254, 2, 120, .14);
    border: 1px solid rgba(254, 2, 120, .4);
    border-radius: 16px;
    color: #ff5ca5;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 12px;
    white-space: nowrap;
  }

  .cineworm-workstation-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .cineworm-workstation-card {
    align-items: center;
    background: rgba(0, 0, 0, .2);
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 6px;
    color: #ffffff;
    display: flex;
    gap: 12px;
    min-height: 76px;
    padding: 13px;
    position: relative;
    text-decoration: none;
    transition: all .25s ease;
    overflow: hidden;
  }

  .cineworm-workstation-card::before {
    background: linear-gradient(110deg, transparent 22%, rgba(255, 255, 255, .1) 50%, transparent 78%);
    content: '';
    height: 180%;
    left: -100%;
    pointer-events: none;
    position: absolute;
    top: -40%;
    transform: rotate(18deg);
    transition: left .6s ease;
    width: 55%;
  }

  .cineworm-workstation-card:hover {
    background: rgba(254, 2, 120, .16);
    border-color: #fe0278;
    color: #ffffff;
    text-decoration: none;
    box-shadow: 0 10px 24px rgba(254, 2, 120, .16), inset 0 1px 0 rgba(255, 255, 255, .08);
    transform: translateY(-4px) scale(1.01);
  }

  .cineworm-workstation-card:hover::before {
    left: 125%;
  }

  .cineworm-workstation-icon {
    align-items: center;
    animation: cineworm-workstation-pulse 3s ease-in-out infinite;
    background: #fe0278;
    border-radius: 50%;
    color: #ffffff;
    display: inline-flex;
    flex: 0 0 36px;
    font-size: 18px;
    font-weight: 700;
    height: 36px;
    justify-content: center;
    width: 36px;
  }

  .cineworm-workstation-card:nth-child(2) .cineworm-workstation-icon { animation-delay: .25s; }
  .cineworm-workstation-card:nth-child(3) .cineworm-workstation-icon { animation-delay: .5s; }
  .cineworm-workstation-card:nth-child(4) .cineworm-workstation-icon { animation-delay: .75s; }
  .cineworm-workstation-card:nth-child(5) .cineworm-workstation-icon { animation-delay: 1s; }

  .cineworm-workstation-card-copy {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
  }

  .cineworm-workstation-card-copy strong {
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
  }

  .cineworm-workstation-card-copy small {
    color: rgba(255, 255, 255, .58);
    font-size: 11px;
    line-height: 16px;
  }

  .cineworm-workstation-card.is-locked {
    background: rgba(0, 0, 0, .32);
    opacity: .62;
  }

  .cineworm-workstation-card.is-locked:hover {
    background: rgba(254, 2, 120, .1);
    opacity: .85;
    transform: none;
  }

  .cineworm-workstation-card.is-danger:hover {
    background: rgba(220, 53, 69, .16);
    border-color: #dc3545;
  }

  .cineworm-workstation-card.is-danger .cineworm-workstation-icon {
    background: #dc3545;
  }

  .cineworm-workstation-card.is-danger .cineworm-workstation-arrow {
    color: #ff7b87;
  }

  .cineworm-workstation-lock {
    color: #ff5ca5;
    font-size: 14px;
    margin-left: 4px;
  }

  .cineworm-workstation-arrow {
    color: #fe0278;
    font-size: 20px;
    font-weight: 700;
    transition: transform .25s ease;
  }

  .cineworm-workstation-card:hover .cineworm-workstation-arrow {
    transform: translateX(4px);
  }

  @keyframes cineworm-workstation-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(254, 2, 120, .16); }
    50% { box-shadow: 0 0 0 6px rgba(254, 2, 120, 0); }
  }

  .dashboard-feature-sidebar {
    margin-top: 22px;
  }

  .dashboard-feature-sidebar h6 {
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 12px;
    text-transform: uppercase;
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
    background: rgba(255, 255, 255, .05);
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 5px;
    color: #ffffff;
    display: flex;
    font-size: 14px;
    font-weight: 600;
    gap: 10px;
    line-height: 20px;
    padding: 10px 12px;
    text-decoration: none;
    transition: all .2s ease;
  }

  .dashboard-feature-links a:hover {
    background: #fe0278;
    border-color: #fe0278;
    color: #ffffff;
  }

  .dashboard-feature-links i {
    flex: 0 0 18px;
    text-align: center;
  }

  @media only screen and (max-width: 991px) {
    .dashboard-hero-summary {
      grid-template-columns: 1fr;
    }

    .dashboard-card-col {
      margin-bottom: 25px;
    }

    .dashboard-feature-sidebar {
      margin-bottom: 25px;
    }

    .dashboard-feature-links {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .cineworm-workstation-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media only screen and (max-width: 575px) {
    .dashboard-feature-links {
      grid-template-columns: 1fr;
    }

    .cineworm-workstation {
      padding: 18px;
    }

    .cineworm-workstation-heading {
      display: block;
    }

    .cineworm-workstation-badge {
      display: inline-block;
      margin-top: 10px;
    }

    .cineworm-workstation-grid {
      grid-template-columns: 1fr;
    }
  }
</style>


<!-- Start Breadcrumb -->
<div class="breadcrumb-section bg-xs dashboard-hero" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xl-12">
        <h2>{{trans('words.dashboard_text')}}</h2>
        <nav id="breadcrumbs">
          <ul>
            <li><a href="{{ URL::to('/') }}" title="{{trans('words.home')}}">{{trans('words.home')}}</a></li>
            <li>{{trans('words.dashboard_text')}}</li>
          </ul>
        </nav>
      </div>
    </div>

    <div class="dashboard-hero-summary">
      <div class="dashboard-hero-card">
        <h5>Stats</h5>
        <span class="premuim-memplan-bold-text"><strong>Joined Since:</strong>
          <span>{{ date('F, d, Y',strtotime(Auth::User()->created_at)) }}</span>
        </span>
        <span class="premuim-memplan-bold-text"><strong>Total Films:</strong>
          <span>{{ \App\Movies::where('added_by', Auth::user()->id)->count() }}</span>
        </span>
        <span class="premuim-memplan-bold-text"><strong>Active Device:</strong>
          <span>{{ \App\UsersDeviceHistory::where('user_id', Auth::user()->id)->first()->user_device_name ?? 'No Active Device' }}</span>
        </span>
      </div>

      <div class="dashboard-hero-card">
        <h5>{{trans('words.my_subscription')}}</h5>
        @if($currentPlan)
        <span class="premuim-memplan-bold-text"><strong>{{trans('words.current_plan')}}:</strong><span>{{ $currentPlan->plan_name }}</span></span>
        @if($user->start_date)
        <span class="premuim-memplan-bold-text"><strong>Plan Started:</strong><span>{{ date('F, d, Y',$user->start_date) }}</span></span>
        @endif
        @if($user->exp_date)
        <span class="premuim-memplan-bold-text"><strong>{{trans('words.subscription_expires_on')}}:</strong><span>{{ date('F, d, Y',$user->exp_date) }}</span></span>
        @endif
        @if(!empty($currentPlan->plan_price) || $currentPlan->plan_price === '0' || $currentPlan->plan_price === 0)
        <span class="premuim-memplan-bold-text"><strong>Plan Price:</strong><span>{{ html_entity_decode(getcong('currency_sign')) }} {{ number_format((float) $currentPlan->plan_price, 2) }}</span></span>
        @endif
        <div class="mt-3"><a href="{{ URL::to('membership_plan') }}" class="vfx-item-btn-danger text-uppercase">{{trans('words.upgrade_plan')}}</a></div>
        @else
        <span class="premuim-memplan-bold-text"><strong>{{trans('words.current_plan')}}:</strong><span>No Plan Selected</span></span>
        <div class="mt-3"><a href="{{ URL::to('membership_plan') }}" class="vfx-item-btn-danger text-uppercase">{{trans('words.select_plan')}}</a></div>
        @endif
      </div>
    </div>
  </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Dashboard Page -->

<div class="vfx-item-ptb vfx-item-info">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


        @if(false)
        <div class="profile-section">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="row dashboard-card-grid">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 dashboard-card-col">
                  <div class="member-ship-option">
                    <h5 class="color-up">Stats</h5>
                    <span class="premuim-memplan-bold-text"><strong>Joined Since:</strong>
                        <span>{{ date('F, d, Y',strtotime(Auth::User()->created_at)) }}</span>
                    </span>
                    <span class="premuim-memplan-bold-text"><strong>Total Films:</strong>
                        <span>{{ \App\Movies::where('added_by', Auth::user()->id)->count() }}</span>
                    </span>
                    <span class="premuim-memplan-bold-text"><strong>Active Device:</strong>
                        <span>{{ \App\UsersDeviceHistory::where('user_id', Auth::user()->id)->first()->user_device_name ?? 'No Active Device' }}</span>
                    </span>
                  </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 dashboard-card-col">
                  <div class="member-ship-option">
                    <h5 class="color-up">{{trans('words.my_subscription')}}</h5>
                    @if($currentPlan)
                    <span class="premuim-memplan-bold-text"><strong>{{trans('words.current_plan')}}:</strong><span>{{ $currentPlan->plan_name }}</span></span>
                    @if($user->start_date)
                    <span class="premuim-memplan-bold-text"><strong>Plan Started:</strong><span>{{ date('F, d, Y',$user->start_date) }}</span></span>
                    @endif
                    @if($user->exp_date)
                    <span class="premuim-memplan-bold-text"><strong>{{trans('words.subscription_expires_on')}}:</strong><span>{{ date('F, d, Y',$user->exp_date) }}</span></span>
                    @endif
                    @if(!empty($currentPlan->plan_price) || $currentPlan->plan_price === '0' || $currentPlan->plan_price === 0)
                    <span class="premuim-memplan-bold-text"><strong>Plan Price:</strong><span>{{ html_entity_decode(getcong('currency_sign')) }} {{ number_format((float) $currentPlan->plan_price, 2) }}</span></span>
                    @endif
                    <div class="mt-3"><a href="{{ URL::to('membership_plan') }}" class="vfx-item-btn-danger text-uppercase">{{trans('words.upgrade_plan')}}</a></div>
                    @else
                    <span class="premuim-memplan-bold-text"><strong>{{trans('words.current_plan')}}:</strong><span>No Plan Selected</span></span>
                    <div class="mt-3"><a href="{{ URL::to('membership_plan') }}" class="vfx-item-btn-danger text-uppercase">{{trans('words.select_plan')}}</a></div>
                    @endif
                  </div>
                </div>
              </div>

              <section class="cineworm-workstation" aria-labelledby="cineworm-workstation-title">
                <div class="cineworm-workstation-heading">
                  <div>
                    <h4 id="cineworm-workstation-title">Cineworm <span>Workstation</span></h4>
                    <p>Your creative tools and Cineworm services in one place.</p>
                  </div>
                  <span class="cineworm-workstation-badge">Tools</span>
                </div>

                <div class="cineworm-workstation-grid">
                  <a class="cineworm-workstation-card" href="{{ URL::to('reel2reel/') }}" aria-label="Open Reel2Reel video editor">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-scissors"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>Reel2Reel</strong>
                      <small>Video editor, effects and stock</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ URL::to('user/live_broadcasts') }}" aria-label="Open CineMeet">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-video-camera"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>CineMeet</strong>
                      <small>Meetings and collaboration rooms</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ route('user.live-tv.create') }}" aria-label="Add Live TV">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-television"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>Add Live TV</strong>
                      <small>Submit a live channel for admin review</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ URL::to('cinegrab/') }}" aria-label="Open CineGrab Downloader">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-download"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>CineGrab</strong>
                      <small>Download videos from supported sites</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="https://stock.cineworm.org/" target="_blank" rel="noopener" aria-label="Open CineStock">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-th-large"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>CineStock</strong>
                      <small>Stock audio, video, effects and LUTs</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ URL::to('livetv/') }}" aria-label="Open Live TV">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-play"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>Live TV</strong>
                      <small>Browse live channels and streams</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ URL::to('game/remote-control') }}" aria-label="Open Cineworm Games">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-gamepad"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>Games</strong>
                      <small>Play Cineworm interactive games</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ URL::to('profile') }}" aria-label="Edit personal profile">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-user"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>My Profile</strong>
                      <small>Update your Cineworm profile</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ URL::to('user/films') }}" aria-label="Open film uploads">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-upload"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>Film Uploads</strong>
                      <small>Manage your uploaded films</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card" href="{{ URL::to('membership_plan') }}" aria-label="View subscription plans">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-credit-card"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>Subscription Plans</strong>
                      <small>View plans and upgrade access</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  <a class="cineworm-workstation-card is-danger data_remove" href="#" aria-label="Delete account">
                    <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-trash"></i></span>
                    <span class="cineworm-workstation-card-copy">
                      <strong>Delete Account</strong>
                      <small>Permanently remove your account</small>
                    </span>
                    <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                  </a>

                  @foreach($activePlans as $plan)
                    @php
                      $uniqueFeatures = array_diff($plan->getDirectFeatureKeys(), $renderedWorkstationFeatures);
                    @endphp
                    @foreach($uniqueFeatures as $featureKey)
                      @if(isset($featureLinkConfig[$featureKey]))
                        @php
                          $renderedWorkstationFeatures[] = $featureKey;
                          $featureLink = $featureLinkConfig[$featureKey];
                          $hasAccess = in_array($featureKey, $planFeatureKeys, true);
                        @endphp
                        @if($hasAccess)
                          <a class="cineworm-workstation-card" href="{{ $featureLink['url'] }}" aria-label="Open {{ $featureLink['title'] }}">
                        @else
                          <a class="cineworm-workstation-card is-locked" href="javascript:void(0);" onclick="showUpgradeWarning('{{ addslashes($plan->plan_name) }}', '{{ addslashes($featureLink['title']) }}')" aria-label="Unlock {{ $featureLink['title'] }}">
                        @endif
                            <span class="cineworm-workstation-icon" aria-hidden="true"><i class="{{ $featureLink['icon'] }}"></i></span>
                            <span class="cineworm-workstation-card-copy">
                              <strong>{{ $featureLink['title'] }} @unless($hasAccess)<i class="fa fa-lock cineworm-workstation-lock" aria-hidden="true"></i>@endunless</strong>
                              <small>{{ $hasAccess ? 'Available with your plan' : 'Available on the ' . $plan->plan_name . ' plan' }}</small>
                            </span>
                            <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
                          </a>
                      @endif
                    @endforeach
                  @endforeach
                </div>
              </section>
            </div>
          </div>
        </div>
        @endif
        @include('pages.user._workstation')
      </div>

      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-wrapper">
          <div class="vfx-item-section">
            <h3>Watch History</h3>
          </div>

          @if ($recently_watched->count() > 0)
            <div class="recently-watched-video-carousel owl-carousel">
              @foreach ($recently_watched as $i => $watched_videos)
                  <div class="single-video">
                      @if ($watched_videos->video_type == 'Movies')
                          @php
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <a href="{{ URL::to('movies/details/' . $info->video_slug . '/' . $info->id) }}"
                                  title="{{ $info->video_title }}">
                                  <div class="video-img">
                                      <span class="video-item-content">{{ $info->video_title }}</span>
                                      <img src="{{ URL::to('/' . $info->video_image) }}"
                                          alt="{{ $info->video_title }}"
                                          title="Movies-{{ $info->video_title }}">
                                  </div>
                              </a>
                          @endif
                      @endif

                      @if ($watched_videos->video_type == 'Episodes')
                          @php
                              $episode_series_id = \App\Episodes::getEpisodesInfo(
                                  $watched_videos->video_id,
                                  'episode_series_id',
                              );
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <div class="single-video">
                                  <a href="{{ URL::to('shows/' . \App\Series::getSeriesInfo($episode_series_id, 'series_slug') . '/' . $info->video_slug . '/' . $info->id) }}"
                                      title="{{ $info->video_title }}">
                                      <div class="video-img">
                                          <span class="video-item-content">{{ $info->video_title }}</span>
                                          <img src="{{ URL::to('/' . $info->video_image) }}"
                                              alt="{{ $info->video_title }}"
                                              title="Episodes-{{ $info->video_title }}">
                                      </div>
                                  </a>
                              </div>
                          @endif
                      @endif

                      @if ($watched_videos->video_type == 'Sports')
                          @php
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <div class="single-video">
                                  <a href="{{ URL::to('sports/details/' . $info->video_slug . '/' . $info->id) }}"
                                      title="{{ $info->video_title }}">
                                      <div class="video-img">
                                          <span class="video-item-content">{{ $info->video_title }}</span>
                                          <img src="{{ URL::to('/' . $info->video_image) }}"
                                              alt="{{ $info->video_title }}"
                                              title="Sports-{{ $info->video_title }}">
                                      </div>
                                  </a>
                              </div>
                          @endif
                      @endif

                      @if ($watched_videos->video_type == 'LiveTV')
                          @php
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <div class="single-video">
                                  <a href="{{ URL::to('livetv/details/' . $info->channel_slug . '/' . $info->id) }}"
                                      title="{{ $info->channel_name }}">
                                      <div class="video-img">
                                          <span class="video-item-content">{{ $info->channel_name }}</span>
                                          <img src="{{ live_tv_thumb_url($info->channel_thumb) }}"
                                              alt="{{ $info->channel_name }}"
                                              title="LiveTV-{{ $info->channel_name }}">
                                      </div>
                                  </a>
                              </div>
                          @endif
                      @endif
                  </div>
              @endforeach
            </div>

            <div class="col-xs-12">
              @include('_particles.pagination', ['paginator' => $transactions_list])
            </div>
          @else
            <p class="mb-0">No watch history found yet.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Dashboard Page -->

@include("pages.user.logged_in_device_list")

<script src="{{ URL::asset('site_assets/js/jquery-3.3.1.min.js') }}"></script>

<script type="text/javascript">
  @if(Session::has('flash_message'))

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'success',
    title: '{{ Session::get('flash_message') }}'
  })

  @endif

  @if(Session::has('success'))

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'success',
    title: '{{ Session::get('success') }}'
  })

  @endif

  @if(Session::has('error_flash_message'))

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'error',
    title: '{{ Session::get('error_flash_message') }}'
  })

  @endif


  $(".data_remove").click(function(event) {
  event.preventDefault(); // Prevent the default anchor behavior

  Swal.fire({
    title: '{{trans('words.dlt_warning')}}',
    text: "{{trans('words.user_dlt_confirm')}}",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: '{{trans('words.dlt_confirm')}}',
    cancelButtonText: "{{trans('words.btn_cancel')}}",
    background: "#1a2234",
    color: "#fff"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: 'get',
        url: "{{ URL::to('account_delete') }}",
        dataType: 'json',
        data: {
          id: '' // Add the necessary ID if needed
        },
        success: function(res) {
          // Handle the response
          if (res.status == '1') {
            Swal.fire({
              position: 'center',
              icon: 'success',
              title: '{{trans('words.deleted')}}!',
              text: '{{trans('words.user_dlt_success')}}',
              showConfirmButton: true,
              confirmButtonColor: '#10c469',
              background: "#1a2234",
              color: "#fff"
            }).then(function() {
              window.location = "{{ URL::to('/') }}"; // Redirect or refresh
            });
          } else {
            Swal.fire({
              position: 'center',
              icon: 'error',
              title: 'Something went wrong!',
              showConfirmButton: true,
              confirmButtonColor: '#10c469',
              background: "#1a2234",
              color: "#fff"
            });
          }
        }
      });
    }
  });
});

  function showUpgradeWarning(planName, featureName) {
    Swal.fire({
      icon: 'warning',
      title: 'Upgrade Required',
      html: 'You need to subscribe to the <strong>' + planName + '</strong> plan to access <strong>' + featureName + '</strong>.',
      confirmButtonText: 'View Plans',
      confirmButtonColor: '#fe0278',
      showCancelButton: true,
      cancelButtonText: 'Close',
      background: '#1a2234',
      color: '#fff'
    }).then(function(result) {
      if (result.isConfirmed) {
        window.location.href = "{{ URL::to('membership_plan') }}";
      }
    });
  }

</script>

@endsection
