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
                        <div class="col-2 nav-order-last nopadding text-center"
                            style="display: flex; justify-content: center; align-items: center; height: 100px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">

                            <div class="subscribe-btn-item stumble-button">
                                <a href="{{ URL::to('/') }}" title="Stumble"
                                    style="display: flex; align-items: center; text-decoration: none; font-weight: bold; border: 2px solid #444; padding: 10px 20px; border-radius: 5px; transition: background 0.3s, border 0.3s;">

                                    <img src="{{ URL::asset('site_assets/images/ic-subscribe2.png') }}"
                                        alt="ic-subscribe" title="Stumble"
                                        style="width: 24px; height: 24px; margin-right: 8px;">

                                    <span style="color: #fff;">Stumble</span>
                                </a>
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
