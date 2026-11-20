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
      <span class="cineworm-workstation-card-copy"><strong>Reel2Reel</strong><small>Video editor, effects and stock</small></span>
      <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
    </a>

    <a class="cineworm-workstation-card" href="{{ URL::to('user/live_broadcasts') }}" aria-label="Open CineMeet">
      <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-video-camera"></i></span>
      <span class="cineworm-workstation-card-copy"><strong>CineMeet</strong><small>Meetings and collaboration rooms</small></span>
      <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
    </a>

    <a class="cineworm-workstation-card" href="{{ URL::to('cinegrab/') }}" aria-label="Open CineGrab Downloader">
      <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-download"></i></span>
      <span class="cineworm-workstation-card-copy"><strong>CineGrab</strong><small>Download videos from supported sites</small></span>
      <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
    </a>

    <a class="cineworm-workstation-card" href="https://stock.cineworm.org/" target="_blank" rel="noopener" aria-label="Open CineStock">
      <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-th-large"></i></span>
      <span class="cineworm-workstation-card-copy"><strong>CineStock</strong><small>Stock audio, video, effects and LUTs</small></span>
      <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
    </a>

    <a class="cineworm-workstation-card" href="{{ URL::to('profile') }}" aria-label="Edit personal profile">
      <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-user"></i></span>
      <span class="cineworm-workstation-card-copy"><strong>My Profile</strong><small>Update your Cineworm profile</small></span>
      <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
    </a>

    <a class="cineworm-workstation-card" href="{{ URL::to('user/films') }}" aria-label="Open film uploads">
      <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-upload"></i></span>
      <span class="cineworm-workstation-card-copy"><strong>Film Uploads</strong><small>Manage your uploaded films</small></span>
      <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
    </a>

    <a class="cineworm-workstation-card" href="{{ URL::to('membership_plan') }}" aria-label="View subscription plans">
      <span class="cineworm-workstation-icon" aria-hidden="true"><i class="fa fa-credit-card"></i></span>
      <span class="cineworm-workstation-card-copy"><strong>Subscription Plans</strong><small>View plans and upgrade access</small></span>
      <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
    </a>

    @foreach($activePlans as $plan)
      @php $uniqueFeatures = array_diff($plan->getDirectFeatureKeys(), $renderedWorkstationFeatures); @endphp
      @foreach($uniqueFeatures as $featureKey)
        @if(isset($featureLinkConfig[$featureKey]) && !in_array($featureKey, ['watch_content', 'play_games'], true))
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
