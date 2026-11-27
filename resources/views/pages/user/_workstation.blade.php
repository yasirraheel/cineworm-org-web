@php
  $renderedWorkstationFeatures = $renderedWorkstationFeatures ?? [];
  $workstationFeatures = $workstationFeatures ?? collect();
  $alwaysAvailableKeys = ['cinegrab', 'cinestock', 'my_profile', 'subscription_plans', 'affiliate_program', 'add_live_tv', 'live_tv', 'games'];
@endphp

<section class="cineworm-workstation" aria-labelledby="cineworm-workstation-title">
  <div class="cineworm-workstation-heading">
    <div>
      <h4 id="cineworm-workstation-title">Cineworm <span>Workstation</span></h4>
      <p>Your creative tools and Cineworm services in one place.</p>
    </div>
    <span class="cineworm-workstation-badge">Tools</span>
  </div>

  <div class="cineworm-workstation-grid">
    {{-- Every canonical card comes from Admin > Plan Features. This prevents
         the same feature from having a stale hard-coded duplicate. --}}
    @foreach($workstationFeatures as $feature)
      @php
        $featureKey = $feature->feature_key;
        $featureLink = $featureLinkConfig[$featureKey] ?? null;
        $hasAccess = in_array($featureKey, $planFeatureKeys, true) || in_array($featureKey, $alwaysAvailableKeys, true);
      @endphp
      @if($featureLink && !in_array($featureKey, $renderedWorkstationFeatures, true))
        @php $renderedWorkstationFeatures[] = $featureKey; @endphp
        @if($hasAccess)
          <a class="cineworm-workstation-card" href="{{ $featureLink['url'] }}" aria-label="Open {{ $featureLink['title'] }}">
        @else
          <a class="cineworm-workstation-card is-locked" href="javascript:void(0);" onclick="showUpgradeWarning('your plan', '{{ addslashes($featureLink['title']) }}')" aria-label="Unlock {{ $featureLink['title'] }}">
        @endif
            <span class="cineworm-workstation-icon" aria-hidden="true"><i class="{{ $featureLink['icon'] }}"></i></span>
            <span class="cineworm-workstation-card-copy">
              <strong>{{ $featureLink['title'] }} @unless($hasAccess)<i class="fa fa-lock cineworm-workstation-lock" aria-hidden="true"></i>@endunless</strong>
              <small>{{ $hasAccess ? 'Available with your plan' : 'Available with a subscription plan' }}</small>
            </span>
            <span class="cineworm-workstation-arrow" aria-hidden="true">&rarr;</span>
          </a>
      @endif
    @endforeach

    {{-- Existing feature assignments remain visible even on installations
         where the new workstation flag has not been enabled yet. --}}
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
