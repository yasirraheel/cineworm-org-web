<!-- Start Header -->
<header id="auto-hide-header">
    <!-- Hover trigger area -->
    <div id="header-hover-trigger" style="position: fixed; top: 0; left: 0; right: 0; height: 80px; z-index: 999;"></div>

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

                        <div class="col-5 nav-order-last nopadding">
                            <div class="main-nav leftnav">
                                <ul class="top-nav">
                                    <li class="visible-this d-md-none menu-icon">
                                        <a href="#" class="navbar-toggle collapsed" data-bs-toggle="collapse"
                                            data-bs-target="#menu" aria-expanded="false" title="menu-toggle">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    </li>
                                </ul>
                                <div id="menu" class="collapse header-menu">
                                    <ul class="nav vfx-item-nav">
                                        <li><a href="{{ URL::to('/') }}" class="{{ classActivePathSite('') }}"
                                                title="home">Home</a></li>

                                        @if (getcong('menu_movies'))
                                            <li><a href="{{ URL::to('movies/') }}"
                                                    class="{{ classActivePathSite('movies') }}"
                                                    title="{{ trans('words.movies_text') }}">{{ trans('words.movies_text') }}</a>
                                            </li>
                                        @endif

                                        @if (getcong('menu_shows'))
                                            <li><a href="{{ URL::to('shows/') }}"
                                                    class="{{ classActivePathSite('shows') }}"
                                                    title="{{ trans('words.tv_shows_text') }}">{{ trans('words.tv_shows_text') }}</a>
                                            </li>
                                        @endif

                                        @if (getcong('menu_sports'))
                                            <li><a href="{{ URL::to('sports') }}"
                                                    class="{{ classActivePathSite('sports') }}"
                                                    title="{{ trans('words.sports_text') }}">{{ trans('words.sports_text') }}</a>
                                                <span class="arrow"></span>
                                                <ul class="dm-align-2 mega-list">
                                                    @foreach (\App\SportsCategory::where('status', '1')->orderBy('category_name')->get() as $sports_cat)
                                                        <li><a href="{{ URL::to('sports/?cat_id=' . $sports_cat->id) }}"
                                                                title="{{ $sports_cat->category_name }}">{{ $sports_cat->category_name }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif

                                        @if (getcong('menu_livetv'))
                                            <li><a href="{{ route('stumble_random') }}"
                                                    class="{{ classActivePathSite('livetv') }}"
                                                    title="{{ trans('words.live_tv') }}">{{ trans('words.live_tv') }}</a>
                                                <span class="arrow"></span>
                                                {{-- <ul class="dm-align-2 mega-list">
                                                    @foreach (\App\TvCategory::where('status', '1')->orderBy('category_name')->get() as $tv_cat)
                                                        <li><a href="{{ URL::to('livetv/?cat_id=' . $tv_cat->id) }}"
                                                                title="{{ $tv_cat->category_name }}">{{ $tv_cat->category_name }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul> --}}
                                            </li>
                                        @endif

                                        <li>
                                            <a href="{{ URL::to('membership_plan') }}"
                                                class="{{ request()->is('membership_plan') || request()->is('payment_method/*') ? 'active' : '' }}"
                                                title="{{ trans('words.subscription_plan') }}">
                                                {{ trans('words.subscription_plan') }}
                                            </a>
                                        </li>
                                        @if (Auth::check())
                                            <li>
                                                <a href="{{ URL::to('dashboard') }}"
                                                    class="{{ request()->is('dashboard') || request()->is('user/*') || request()->is('profile') || request()->is('watchlist') || request()->is('promotions*') ? 'active' : '' }}"
                                                    title="Dashboard">
                                                    Dashboard
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="right-sub-item-area">
                                <div class="search-item-block">
                                    <form class="navbar-form navbar-left">
                                        <a type="submit" href="#popup1" class="btn btn-default open" title="search">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    </form>
                                </div>
                                <!-- Stumble Button -->



                                @if (Auth::check())
                                    <div class="user-menu">
                                        <div class="user-name">
                                            <span>
                                                @if (Auth::User()->user_image and file_exists(public_path('upload/' . Auth::User()->user_image)))
                                                    <img src="{{ URL::asset('upload/' . Auth::User()->user_image) }}"
                                                        alt="profile_img" title="{{ Auth::User()->name, 6 }}"
                                                        id="userPic">
                                                @else
                                                    <img src="{{ URL::asset('site_assets/images/user-avatar.png') }}"
                                                        alt="profile_img" title="{{ Auth::User()->name, 6 }}"
                                                        id="userPic">
                                                @endif
                                            </span>
                                            {{ Str::limit(Auth::User()->name, 6) }}<i class="fa fa-angle-down"
                                                id="userArrow"></i>
                                        </div>

                                        @if (Auth::User()->usertype == 'Admin')
                                            <ul class="content-user">
                                                <li><a href="{{ URL::to('admin/dashboard') }}"
                                                        title="{{ trans('words.dashboard_text') }}"><i
                                                            class="fa fa-database"></i>{{ trans('words.dashboard_text') }}</a>
                                                </li>
                                                <li><a href="{{ URL::to('profile') }}"
                                                        title="{{ trans('words.profile') }}"><i
                                                            class="fa fa-user"></i>{{ trans('words.profile') }}</a>
                                                </li>
                                                <li><a href="{{ URL::to('messages') }}" title="Contact"><i
                                                            class="fa fa-envelope"></i>Messages</a></li>
                                                <li><a href="{{ URL::to('admin/logout') }}"
                                                        title="{{ trans('words.logout') }}"><i
                                                            class="fa fa-sign-out-alt"></i>{{ trans('words.logout') }}</a>
                                                </li>
                                            </ul>
                                        @else
                                            @php
                                                $promotionUserPlan = Auth::User()->plan_id ? \App\SubscriptionPlan::find(Auth::User()->plan_id) : null;
                                                $promotionUserFeatures = $promotionUserPlan ? $promotionUserPlan->getEffectiveFeatureKeys() : [];
                                            @endphp
                                            <ul class="content-user">
                                                <li><a href="{{ URL::to('dashboard') }}"
                                                        title="{{ trans('words.dashboard_text') }}"><i
                                                            class="fa fa-database"></i>{{ trans('words.dashboard_text') }}</a>
                                                </li>
                                                @if(in_array('promotion_services', $promotionUserFeatures, true))
                                                    <li><a href="{{ URL::to('promotions') }}"
                                                            title="Promotion"><i
                                                                class="fa fa-bullhorn"></i>Promotion</a>
                                                    </li>
                                                @endif
                                                <li><a href="{{ URL::to('membership_plan') }}"
                                                        title="{{ trans('words.subscription_plan') }}"><i
                                                            class="fa fa-credit-card"></i>{{ trans('words.subscription_plan') }}</a>
                                                </li>
                                                <li><a href="{{ URL::to('profile') }}"
                                                        title="{{ trans('words.profile') }}"><i
                                                            class="fa fa-user"></i>{{ trans('words.profile') }}</a>
                                                </li>
                                                <li><a href="{{ URL::to('messages') }}" title="Contact"><i
                                                            class="fa fa-envelope"></i>Contact</a></li>
                                                <li><a href="{{ URL::to('logout') }}"
                                                        title="{{ trans('words.logout') }}"><i
                                                            class="fa fa-sign-out-alt"></i>{{ trans('words.logout') }}</a>
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                @else
                                    <div class="signup-btn-item">
                                        <a href="{{ URL::to('login') }}" title="login">
                                            <img src="{{ URL::asset('site_assets/images/ic-signup-user.png') }}"
                                                alt="ic-signup-user" title="signup-user">
                                            <span>{{ trans('words.login_text') }}</span>
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
    /* Auto-hide header styles - DISABLED */
    #auto-hide-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        height: 100px;
        /* transition: transform 0.4s ease-in-out, opacity 0.4s ease-in-out; */
    }

    /* Force visibility */
    #auto-hide-header.header-hidden {
        transform: translateY(0) !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    #auto-hide-header.header-visible {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    #header-hover-trigger {
        display: none; /* Disable hover trigger */
    }

    /* Ensure header-section is positioned correctly */
    .header-section {
        position: relative;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    /* Body padding to accommodate header */
    body {
        padding-top: 100px;
        /* transition: padding-top 0.4s ease-in-out; */
    }

    body.header-is-hidden {
        padding-top: 100px !important; /* Force padding */
    }

    @media only screen and (min-width: 1001px) {
        .header-section #menu > ul.vfx-item-nav {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
        }

        .header-section #menu > ul.vfx-item-nav > li {
            flex: 0 0 auto;
        }

        .main-menu .vfx-item-nav > li > a {
            white-space: nowrap;
        }
    }

    .user-menu .content-user {
        min-width: 180px !important;
    }

    .user-menu .content-user li a {
        white-space: nowrap !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .user-menu .content-user li a i {
        margin-right: 0 !important;
        min-width: 16px !important;
        text-align: center !important;
    }
</style>

<script>
    // Header hiding logic disabled by request
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.getElementById('auto-hide-header');
        
        // Ensure header is always visible
        if(header) {
            header.classList.remove('header-hidden');
            header.classList.add('header-visible');
        }
    });
</script>
