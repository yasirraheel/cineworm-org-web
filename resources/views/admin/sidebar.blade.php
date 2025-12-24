<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        @if (Auth::User()->usertype == 'Admin')
            <div id="sidebar-menu">

                <ul>
                    <li><a href="{{ URL::to('admin/dashboard') }}"
                            class="waves-effect {{ classActivePath('dashboard') }}"><i
                                class="fa fa-dashboard"></i><span>{{ trans('words.dashboard_text') }}</span></a></li>
                    <li><a href="{{ URL::to('admin/language') }}"
                            class="waves-effect {{ classActivePath('language') }}"><i
                                class="fa fa-language"></i><span>{{ trans('words.language_text') }}</span></a></li>
                    <li><a href="{{ URL::to('admin/genres') }}" class="waves-effect {{ classActivePath('genres') }}"><i
                                class="fa fa-list"></i><span>{{ trans('words.genres_text') }}</span></a></li>
                    <li><a href="{{ URL::to('admin/news_ticker') }}" class="waves-effect {{ classActivePath('news_ticker') }}"><i
                                class="fa fa-newspaper-o"></i><span>News Ticker</span></a></li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-image"></i>
                            <span>Movies</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @if (getcong('menu_movies'))
                                <li><a href="{{ URL::to('admin/movies') }}"
                                        class="waves-effect {{ classActivePath('movies') }}"><i
                                            class="fa fa-video-camera"></i><span>{{ trans('words.movies_text') }}</span></a>
                                </li>
                            @endif

                            <li class="{{ classActivePath('generateScreenshot') }}"><a
                                    href="{{ URL::to('admin/generateScreenshot') }}"
                                    class="{{ classActivePath('generateScreenshot') }}"><i
                                        class="fa fa-image"></i><span>
                                        Screenshot</span></a></li>

                            <li class="{{ classActivePath('google_drive_api') }}"><a
                                    href="{{ URL::to('admin/google_drive_api') }}"
                                    class="{{ classActivePath('google_drive_api') }}"><i
                                        class="fa fa-google"></i><span>
                                        Google Drive API</span></a></li>
                        </ul>
                    </li>


   @if(getcong('menu_livetv'))
            <li class="has_sub">
              <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-tv"></i><span>{{trans('words.live_tv')}}</span><span class="menu-arrow"></span></a>
              <ul class="list-unstyled">
                <li class="{{classActivePath('tv_category')}}"><a href="{{ URL::to('admin/tv_category') }}" class="{{classActivePath('tv_category')}}"><i class="fa fa-tags"></i><span>{{trans('words.live_tv_category')}}</span></a></li>
                <li class="{{classActivePath('live_tv')}}"><a href="{{ URL::to('admin/live_tv') }}" class="{{classActivePath('live_tv')}}"><i class="fa fa-list"></i><span>{{trans('words.tv_channel')}}</span></a></li>
               </ul>
            </li>
            @endif



                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-users"></i><span>{{ trans('words.cast_crew') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('actor') }}"><a href="{{ URL::to('admin/actor') }}"
                                    class="{{ classActivePath('actor') }}"><i
                                        class="fa fa-user"></i><span>{{ trans('words.actors') }}</span></a></li>
                            <li class="{{ classActivePath('director') }}"><a href="{{ URL::to('admin/director') }}"
                                    class="{{ classActivePath('director') }}"><i
                                        class="fa fa-user"></i><span>{{ trans('words.directors') }}</span></a></li>
                        </ul>
                    </li>



                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-users"></i>
                            <span>{{ trans('words.users') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('users') }}">
                                <a href="{{ URL::to('admin/users') }}" class="{{ classActivePath('users') }}">
                                    <i class="fa fa-users"></i>
                                    <span>{{ trans('words.users') }}</span>
                                </a>
                            </li>
                            <li class="{{ classActivePath('sub_admin') }}"><a href="{{ URL::to('admin/sub_admin') }}"
                                    class="{{ classActivePath('sub_admin') }}"><i
                                        class="fa fa-users"></i><span>Moderators</span></a></li>
                            <li class="{{ classActivePath('deleted_users') }}"><a
                                    href="{{ URL::to('admin/deleted_users') }}"
                                    class="{{ classActivePath('deleted_users') }}"><i
                                        class="fa fa-users"></i><span>{{ trans('words.deleted_users') }}</span></a>
                            </li>
                        </ul>

                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-edit"></i><span>{{ trans('words.pages') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('pages') }}"><a href="{{ URL::to('admin/pages') }}"
                                    class="{{ classActivePath('pages') }}"><i
                                        class="fa fa-file"></i><span>{{ trans('words.pages') }}</span></a></li>
                            <li class="{{ classActivePath('pages/add') }}"><a href="{{ URL::to('admin/pages/add') }}"
                                    class="{{ classActivePath('pages') }}"><i
                                        class="fa fa-plus"></i><span>{{ trans('words.add_page') }}</span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-play-circle"></i><span>{{ trans('words.player_settings') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('player_settings') }}"><a
                                    href="{{ URL::to('admin/player_settings') }}"
                                    class="{{ classActivePath('player_settings') }}"><i
                                        class="fa fa-cog"></i><span>{{ trans('words.settings') }}</span></a></li>

                            <li class="{{ classActivePath('player_ad_settings') }}"><a
                                    href="{{ URL::to('admin/player_ad_settings') }}"
                                    class="{{ classActivePath('player_ad_settings') }}"><i
                                        class="fa fa-buysellads"></i><span>{{ trans('words.player_ads') }}</span></a>
                            </li>
                            <li class="{{ classActivePath('google_derive_player') }}"><a
                                    href="{{ URL::to('admin/google_derive_player') }}"
                                    class="{{ classActivePath('google_derive_player') }}"><i
                                        class="fa fa-google"></i><span>Google Drive Player</span></a></li>

                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-cog"></i><span>{{ trans('words.settings') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('general_settings') }}"><a
                                    href="{{ URL::to('admin/general_settings') }}"
                                    class="{{ classActivePath('general_settings') }}"><i
                                        class="fa fa-cog"></i><span>{{ trans('words.general') }}</span></a></li>
                            <li class="{{ classActivePath('email_settings') }}"><a
                                    href="{{ URL::to('admin/email_settings') }}"
                                    class="{{ classActivePath('email_settings') }}"><i
                                        class="fa fa-send"></i><span>{{ trans('words.smtp_email') }}</span></a></li>
                            <li class="{{ classActivePath('social_login_settings') }}"><a
                                    href="{{ URL::to('admin/social_login_settings') }}"
                                    class="{{ classActivePath('social_login_settings') }}"><i
                                        class="fa fa-usb"></i><span>{{ trans('words.social_login') }}</span></a></li>

                            <li class="{{ classActivePath('menu_settings') }}"><a
                                    href="{{ URL::to('admin/menu_settings') }}"
                                    class="{{ classActivePath('menu_settings') }}"><i
                                        class="fa fa-list"></i><span>{{ trans('words.menu') }}</span></a></li>
                            <li class="{{ classActivePath('recaptcha_settings') }}"><a
                                    href="{{ URL::to('admin/recaptcha_settings') }}"
                                    class="{{ classActivePath('recaptcha_settings') }}"><i
                                        class="fa fa-refresh"></i><span> {{ trans('words.reCAPTCHA') }}</span></a>
                            </li>
                            <li class="{{ classActivePath('web_ads_settings') }}"><a
                                    href="{{ URL::to('admin/web_ads_settings') }}"
                                    class="{{ classActivePath('web_ads_settings') }}"><i
                                        class="fa fa-buysellads"></i><span>
                                        {{ trans('words.banner_ads') }}</span></a></li>

                                        <li class="{{ classActivePath('buttons_banners') }}"><a
                                    href="{{ URL::to('admin/buttons') }}"
                                    class="{{ classActivePath('buttons_banners') }}"><i
                                        class="fa fa-gbp"></i><span>
                                        {{ trans('Buttons') }}</span></a></li>

                                        <li class="{{ classActivePath('buttons_banners') }}"><a
                                    href="{{ URL::to('admin/banners') }}"
                                    class="{{ classActivePath('buttons_banners') }}"><i
                                        class="fa fa-gbp"></i><span>
                                        {{ trans('Banners') }}</span></a></li>

                            <li class="{{ classActivePath('site_maintenance') }}"><a
                                    href="{{ URL::to('admin/site_maintenance') }}"
                                    class="{{ classActivePath('site_maintenance') }}"><i
                                        class="fa fa-wrench"></i><span>
                                        {{ trans('words.site_maintenance') }}</span></a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="fa fa-android"></i><span>{{ trans('words.android_app') }}</span><span
                                class="menu-arrow"></span></a>
                        <ul class="list-unstyled">

                            <li class="{{ classActivePath('android_settings') }}"><a
                                    href="{{ URL::to('admin/android_settings') }}"
                                    class="{{ classActivePath('android_settings') }}"><i
                                        class="fa fa-cog"></i><span>{{ trans('words.android_app_settings') }}</span></a>
                        </ul>
                    </li>
                    <li class="{{ classActivePath('messages') }}">
                        <a href="{{ URL::to('messages') }}" class="waves-effect">
                            <i class="fa fa-envelope"></i>
                            <span>Messages</span>

                        </a>

                    </li>
                    <li class="{{ classActivePath('chat') }}">
                        <a href="{{ URL::to('/admin/clear-cache') }}" class="waves-effect">
                            <i class="fa fa-cogs"></i>
                            <span>Clear Cache</span>

                        </a>

                    </li>
                    <li class="{{ classActivePath('slider') }}"><a href="{{ URL::to('admin/slider') }}"
                            class="{{ classActivePath('slider') }}"><i
                                class="fa fa-sliders"></i><span>{{ trans('words.slider') }}</span></a></li>

                </ul>
            </div>
        @elseif (Auth::User()->usertype == 'Moderator')
            <div id="sidebar-menu">

                <ul>
                    <li><a href="{{ URL::to('admin/dashboard') }}"
                            class="waves-effect {{ classActivePath('dashboard') }}"><i
                                class="fa fa-dashboard"></i><span>{{ trans('words.dashboard_text') }}</span></a></li>


                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-image"></i>
                            <span>Movies</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @if (getcong('menu_movies'))
                                <li><a href="{{ URL::to('admin/movies') }}"
                                        class="waves-effect {{ classActivePath('movies') }}"><i
                                            class="fa fa-video-camera"></i><span>{{ trans('words.movies_text') }}</span></a>
                                </li>
                            @endif

                            <li class="{{ classActivePath('generateScreenshot') }}"><a
                                    href="{{ URL::to('admin/generateScreenshot') }}"
                                    class="{{ classActivePath('generateScreenshot') }}"><i
                                        class="fa fa-image"></i><span>
                                        Screenshot</span></a></li>

                            <li class="{{ classActivePath('google_drive_api') }}"><a
                                    href="{{ URL::to('admin/google_drive_api') }}"
                                    class="{{ classActivePath('google_drive_api') }}"><i
                                        class="fa fa-google"></i><span>
                                        Google Drive API</span></a></li>
                        </ul>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fa fa-users"></i>
                            <span>{{ trans('words.users') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li class="{{ classActivePath('users') }}">
                                <a href="{{ URL::to('admin/users') }}" class="{{ classActivePath('users') }}">
                                    <i class="fa fa-users"></i>
                                    <span>{{ trans('words.users') }}</span>
                                </a>
                            </li>
                            <li class="{{ classActivePath('sub_admin') }}"><a
                                    href="{{ URL::to('admin/sub_admin') }}"
                                    class="{{ classActivePath('sub_admin') }}"><i
                                        class="fa fa-users"></i><span>Moderators</span></a></li>
                            <li class="{{ classActivePath('deleted_users') }}"><a
                                    href="{{ URL::to('admin/deleted_users') }}"
                                    class="{{ classActivePath('deleted_users') }}"><i
                                        class="fa fa-users"></i><span>{{ trans('words.deleted_users') }}</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ classActivePath('chat') }}">
                        <a href="{{ URL::to('/admin/clear-cache') }}" class="waves-effect">
                            <i class="fa fa-cogs"></i>
                            <span>Clear Cache</span>
                        </a>
                    </li>
                </ul>
            </div>
        @elseif (Auth::User()->usertype == 'Sub_Admin')
        <div id="sidebar-menu">

            <ul>
                @if (getcong('menu_movies'))
                            <li><a href="{{ URL::to('admin/movies') }}"
                                    class="waves-effect {{ classActivePath('movies') }}"><i
                                        class="fa fa-video-camera"></i><span>{{ trans('words.movies_text') }}</span></a>
                            </li>
                        @endif


            </ul>
        </div>
        @endif
    </div>
</div>
