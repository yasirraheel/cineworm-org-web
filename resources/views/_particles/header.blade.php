<!-- Start Header -->
<header id="auto-hide-header" style="position: relative; height: 100px;">
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

                        <div class="col-4 nav-order-last nopadding">
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
                                    </ul>
                                </div>
                            </div>
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


                        <!-- Centered Stumble Button (Mobile only) -->
                        {{-- <div class="col-2">
                            <div class="centered-stumble-button">
                                <a href="{{ route('stumble_random') }}" class="btn btn-primary px-4 py-2 fw-bold text-white">
                                    Stumble
                                </a>
                            </div>
                        </div> --}}


                        <div class="col-3">
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
                                            <ul class="content-user">
                                                <li><a href="{{ URL::to('dashboard') }}"
                                                        title="{{ trans('words.dashboard_text') }}"><i
                                                            class="fa fa-database"></i>{{ trans('words.dashboard_text') }}</a>
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
    /* Auto-hide header styles */
    #auto-hide-header {
        transition: transform 0.4s ease-in-out, opacity 0.4s ease-in-out;
    }

    #auto-hide-header.header-hidden {
        transform: translateY(-100%);
        opacity: 0;
        pointer-events: none;
    }

    #auto-hide-header.header-visible {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    #header-hover-trigger {
        pointer-events: all;
    }

    /* Ensure header-section is positioned correctly */
    .header-section {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.getElementById('auto-hide-header');
        const hoverTrigger = document.getElementById('header-hover-trigger');
        let autoHideTimeout;
        let delayedHideTimeout;
        let isHidden = false;
        let isHovering = false;

        // Function to hide header
        function hideHeader() {
            header.classList.add('header-hidden');
            header.classList.remove('header-visible');
            isHidden = true;
        }

        // Function to show header
        function showHeader() {
            header.classList.remove('header-hidden');
            header.classList.add('header-visible');
            isHidden = false;
        }

        // Auto-hide after 5 seconds on page load
        autoHideTimeout = setTimeout(() => {
            if (!isHovering) {
                hideHeader();
            }
        }, 5000);

        // Show header when hovering over trigger area
        hoverTrigger.addEventListener('mouseenter', function() {
            isHovering = true;
            clearTimeout(autoHideTimeout);
            clearTimeout(delayedHideTimeout);
            showHeader();
        });

        hoverTrigger.addEventListener('mouseleave', function() {
            isHovering = false;
        });

        // Keep header visible when mouse enters header
        header.addEventListener('mouseenter', function() {
            isHovering = true;
            clearTimeout(autoHideTimeout);
            clearTimeout(delayedHideTimeout);
            showHeader();
        });

        // Set timeout to hide when mouse leaves header
        header.addEventListener('mouseleave', function(e) {
            isHovering = false;

            // Only hide if mouse is not moving to trigger area
            delayedHideTimeout = setTimeout(() => {
                if (!isHovering) {
                    hideHeader();
                }
            }, 500);
        });

        // Also show header on any click within the header
        header.addEventListener('click', function() {
            clearTimeout(autoHideTimeout);
            clearTimeout(delayedHideTimeout);
            showHeader();
        });
    });
</script>
