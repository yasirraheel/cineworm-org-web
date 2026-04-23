<!-- Start Header -->
<header style="position: relative; height: 100px;">
    <!-- Start Navigation Area -->
    <div class="main-menu">
        <nav class="header-section pin-style">
            <div class="container-fluid">
                <div class="mod-menu">
                    <div class="row">
                        <div class="col-2">
                            @if (getcong('site_logo'))
                                <a href="{{ URL::to('/') }}" title="logo" class="logo">
                                    <img src="{{ URL::asset('/' . getcong('site_logo')) }}" alt="logo"
                                        title="logo">
                                </a>
                            @else
                                <a href="{{ URL::to('/') }}" title="logo" class="logo">
                                    <img src="{{ URL::asset('site_assets/images/logo.png') }}" alt="logo"
                                        title="logo">
                                </a>
                            @endif
                        </div>

                        <div class="col-4 nav-order-last nopadding">

                        </div>
                        <div class="col-6">
                            <div class="right-sub-item-area">
                                <div class="signup-btn-item" style="float:right; margin-left:10px;">
                                    <a href="{{ URL::to('membership_plan') }}"
                                        title="{{ trans('words.subscription_plan') }}">
                                        <i class="fa fa-credit-card" aria-hidden="true"></i>
                                        <span>{{ trans('words.subscription_plan') }}</span>
                                    </a>
                                </div>
                                @if (getcong('donation_link'))
                                    <div class="signup-btn-item" style="float:right;">
                                        <a href="{{ stripslashes(getcong('donation_link')) }}" title="Donate"
                                            target="_blank" rel="noopener noreferrer">
                                            <i class="fa fa-heart" aria-hidden="true"></i>
                                            <span>Donate</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- End Navigation Area -->
</header>
<!-- End Header -->

<!-- Custom CSS for Responsive Behavior -->
<style>
    /* Hide the Stumble button on large screens */


    /* Show Stumble button only on mobile screens */
</style>
