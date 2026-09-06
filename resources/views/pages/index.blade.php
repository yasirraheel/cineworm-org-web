@extends('site_app')

@section('head_title', getcong('site_name'))
@if(request()->getHost() != 'home.cineworm.org')
@section('head_url', Request::url())
@endif
@section('content')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
    <script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>

    <style>
        /* Toggle Button Active State */
        .active-toggle-btn {
            background: rgba(254, 136, 5, 0.2) !important;
            border-left: 3px solid #fe8805 !important;
        }

        .toggle-content {
            transition: opacity 0.3s ease;
        }

        /* Player Buttons Styling */
        .player-buttons-container {
            background: linear-gradient(135deg, #0d0620 0%, #1a0d33 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Desktop: Horizontal layout */
        @media (min-width: 768px) {
            .player-buttons-container {
                flex-direction: row;
                align-items: center;
            }

            .player-buttons-container .btn {
                flex: 0 1 auto;
                min-width: 120px;
            }
        }

        /* Mobile: Vertical stack */
        @media (max-width: 767px) {
            .player-buttons-container {
                flex-direction: column;
                gap: 8px;
                padding: 10px 15px !important;
                margin-bottom: 10px !important;
            }

            .player-buttons-container .btn {
                width: 100% !important;
                padding: 12px 16px !important;
                font-size: 13px !important;
                min-width: unset !important;
                white-space: normal !important;
                text-align: center !important;
                line-height: 1.4 !important;
            }
        }

        .news-ticker-container {
                max-height: 550px;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 10px;
                background: #111;
                color: #fff;
                width: 100%;
                max-width: 100%;
            }

            .news-ticker-container::-webkit-scrollbar {
                width: 6px;
            }

            .news-ticker-container::-webkit-scrollbar-thumb {
                background: #333;
                border-radius: 3px;
            }

            .news-ticker-container::-webkit-scrollbar-track {
                background: #111;
            }

        /* Responsive News Content Body */
        #news-content-body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: #ccc;
            font-size: 15px;
            line-height: 1.6;
        }

        #news-content-body img,
        #news-content-body video,
        #news-content-body iframe,
        #news-content-body object,
        #news-content-body embed {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 5px;
            margin: 10px 0;
        }

        #news-content-body table {
            display: block;
            width: 100% !important;
            overflow-x: auto;
            border-collapse: collapse;
        }

        #news-content-body pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #222;
            padding: 10px;
            border-radius: 5px;
        }

        #news-content-body a {
            color: #fe8805;
            text-decoration: none;
            word-break: break-all;
        }

        .news-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
        }
        .news-item:last-child {
            border-bottom: none;
        }
        .news-headline {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #e50914;
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
        .news-details {
            font-size: 15px;
            color: #ccc;
            line-height: 1.4;
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
        .news-time {
            font-size: 13px;
            color: #888;
            margin-top: 5px;
            display: block;
        }
        .breaking-badge {
            background-color: #e50914;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
            margin-right: 5px;
            vertical-align: middle;
        }
        /* Scrollbar styling */
        .news-ticker-container::-webkit-scrollbar {
            width: 6px;
        }
        .news-ticker-container::-webkit-scrollbar-track {
            background: #111;
        }
        .news-ticker-container::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 3px;
        }
        .news-ticker-container::-webkit-scrollbar-thumb:hover {
            background: #666;
        }


        /* Prevent horizontal overflow in news ticker column */
        .col-md-3.d-flex {
            overflow-x: hidden;
            max-width: 100%;
        }

        .card.bg-dark {
            overflow-x: hidden;
            max-width: 100%;
        }

        .card-body {
            overflow-x: hidden;
            max-width: 100%;
        }

        /* Ensure all news content stays within bounds */
        .news-item * {
            max-width: 100%;
        }

        .news-item a,
        .news-item img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 767px) {
            /* News Ticker Mobile Styling */
            .news-ticker-container {
                max-height: 400px;
                height: auto;
                min-height: 300px;
                margin-top: 0;
                margin-bottom: 15px;
            }

            /* Game Container Mobile Styling */
            .pacman-game-wrapper {
                height: 400px;
                min-height: 350px;
            }

            /* Mobile Column Full Width */
            .col-md-3.d-flex {
                width: 100%;
                max-width: 100%;
                padding: 0 15px;
                margin-top: 15px;
            }

            /* Toggle Buttons Mobile */
            .toggle-section-btn {
                font-size: 14px;
                padding: 12px 20px;
            }

            /* News Item Mobile Adjustments */
            .news-item {
                padding: 12px;
                margin-bottom: 15px;
                max-width: 100%;
                overflow-wrap: break-word;
            }

            .news-headline {
                font-size: 16px;
                max-width: 100%;
                overflow-wrap: break-word;
                word-break: break-word;
            }

            .news-details {
                font-size: 14px;
                max-width: 100%;
                overflow-wrap: break-word;
                word-break: break-word;
            }

            .news-time {
                font-size: 12px;
                overflow-wrap: break-word;
            }
        }

        /* Player Footer Section */
        .player-footer-section {
            background: linear-gradient(135deg, #0d0620 0%, #1a0d33 100%);
            border-radius: 0;
            margin-top: 0;
            padding: 20px 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.4);
            width: 100%;
        }

        /* Unified Player Footer - Single Row Layout */
        .player-footer-unified {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* Video Title Section */
        .video-title-section {
            flex: 0 1 auto;
            min-width: 200px;
            max-width: 300px;
        }

        .video-title-link {
            text-decoration: none;
        }

        .video-title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            transition: color 0.3s ease;
        }

        .video-title-link:hover .video-title {
            color: #fe8805;
        }

        /* Action Buttons Section */
        .action-buttons-section {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .like-form {
            display: inline-block;
            margin: 0;
        }

        /* Base Action Button Style */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            gap: 8px;
            white-space: nowrap;
        }

        .action-btn i {
            font-size: 14px;
        }

        /* Donate Button */
        .donate-btn {
            background: linear-gradient(90deg, #fe8805, #ff6b00);
            box-shadow: 0 2px 8px rgba(254, 136, 5, 0.25);
        }

        .donate-btn:hover {
            background: linear-gradient(90deg, #ff6b00, #fe8805);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(254, 136, 5, 0.4);
            color: #ffffff;
        }

        /* Webpage Button */
        .webpage-btn {
            background: linear-gradient(90deg, #167ac6, #0a789c);
            box-shadow: 0 2px 8px rgba(22, 122, 198, 0.25);
        }

        .webpage-btn:hover {
            background: linear-gradient(90deg, #0a789c, #167ac6);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 122, 198, 0.4);
            color: #ffffff;
        }

        /* Like Button */
        .like-btn {
            background: linear-gradient(90deg, #fe0278, #d10257);
            box-shadow: 0 2px 8px rgba(254, 2, 120, 0.25);
        }

        .like-btn:hover {
            background: linear-gradient(90deg, #d10257, #fe0278);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(254, 2, 120, 0.4);
        }

        .like-btn.liked {
            background: linear-gradient(90deg, #118d04, #0d6b03);
            box-shadow: 0 2px 8px rgba(17, 141, 4, 0.25);
        }

        .like-btn.liked:hover {
            background: linear-gradient(90deg, #0d6b03, #118d04);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 141, 4, 0.4);
        }

        /* Share Button */
        .share-btn {
            background: linear-gradient(90deg, #8e44ad, #6c2d91);
            box-shadow: 0 2px 8px rgba(142, 68, 173, 0.25);
        }

        .share-btn:hover {
            background: linear-gradient(90deg, #6c2d91, #8e44ad);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(142, 68, 173, 0.4);
            color: #ffffff;
        }

        /* Next Button */
        .next-btn {
            background: linear-gradient(90deg, #2c3e50, #4ca1af);
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.25);
        }

        .next-btn:hover {
            background: linear-gradient(90deg, #4ca1af, #2c3e50);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.4);
            color: #ffffff;
        }

        /* Player Footer Meta */
        .player-footer-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            flex: 1 1 auto;
            justify-content: center;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #b5b5b5;
            font-size: 13px;
            font-weight: 500;
        }

        .meta-item i {
            font-size: 14px;
            color: #fe8805;
        }

        .meta-item.imdb-rating {
            background: rgba(245, 197, 24, 0.1);
            padding: 4px 10px;
            border-radius: 4px;
        }

        .imdb-logo {
            width: 30px;
            height: auto;
            vertical-align: middle;
        }

        .meta-item.imdb-rating span {
            color: #f5c518;
            font-weight: 700;
            font-size: 14px;
        }

        /* Responsive adjustments for player footer */
        @media (max-width: 992px) {
            .player-footer-unified {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .video-title-section {
                width: 100%;
                max-width: 100%;
            }

            .player-footer-meta {
                width: 100%;
                justify-content: flex-start;
                order: 2;
            }

            .action-buttons-section {
                width: 100%;
                justify-content: flex-start;
                order: 3;
            }

            .video-title {
                font-size: 18px;
            }
        }

        @media (max-width: 768px) {
            .player-footer-section {
                padding: 15px 18px;
            }

            .player-footer-unified {
                gap: 12px;
            }

            .action-btn {
                padding: 8px 14px;
                font-size: 12px;
            }

            .action-btn span {
                display: none;
            }

            .action-btn i {
                margin: 0;
            }

            .video-title {
                font-size: 16px;
                white-space: normal;
            }

            .player-footer-meta {
                gap: 12px;
            }

            .meta-item {
                font-size: 12px;
            }

            .meta-item i {
                font-size: 13px;
            }
        }

        /* Game Button */
        .game-btn {
            background: linear-gradient(90deg, #e67e22, #d35400);
            box-shadow: 0 2px 8px rgba(230, 126, 34, 0.25);
        }
        .game-btn:hover {
            background: linear-gradient(90deg, #d35400, #e67e22);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.4);
            color: #ffffff;
        }

    </style>

    <!-- Start Page Content Area -->
    <div class="page-content-area vfx-item-ptb pt-0">

        <div class="container-fluid px-0 bg-dark video-player-base">
            <div class="row no-gutters align-items-stretch">
                <!-- Video Player -->
                <div class="col-md-9 p-0" id="main-player-column">
                    @php
                        $playerButtons = get_web_button_banner('buttons', \App\Models\ButtonsBanners::PLACEMENT_DEFAULT);
                        $belowNewsGamesButtons = get_web_button_banner('buttons', \App\Models\ButtonsBanners::PLACEMENT_BELOW_NEWS_GAMES);
                        $banners = get_web_button_banner('banners'); // Fetch all banner components
                    @endphp

                    @if ($playerButtons->isNotEmpty())
                        <div class="player-buttons-container mb-2 px-3 py-2">
                            @foreach ($playerButtons as $button)
                                <a href="{{ $button->link ?? '#' }}" class="btn btn-primary mb-2 flex-shrink-0"
                                    style="padding: 8px 16px; font-size: 14px; font-weight: bold; border-radius: 8px;
                                           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                                           background-color: #{{ $button->color ? $button->color : '007bff' }};
                                           font-family: 'Perpetua', serif; text-align: center;
                                           text-decoration: none; color: #fff;
                                           position: relative;
                                           display: inline-block;
                                           overflow: hidden;
                                           border: 3px solid #00008B;
                                           transition: all 0.3s ease-in-out;"
                                    onmouseover="this.style.borderImage='linear-gradient(45deg, #ff0000, #00ff00, #0000ff, #ff00ff) 1';
                                                 this.style.borderStyle='solid';
                                                 this.style.borderWidth='3px';
                                                 this.style.borderRadius='8px';
                                                 this.style.borderColor='transparent';"
                                    onmouseout="this.style.borderImage='none';
                                                this.style.borderColor='#00008B';
                                                this.style.borderStyle='solid';
                                                this.style.borderWidth='3px';
                                                this.style.borderRadius='8px';"
                                    target="_blank">
                                    {{ $button->title }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($movies_info && $movies_info->video_url != '')
                        @if ($movies_info->video_type == 'GoogleDrive')
                            @include('pages.movies.player.google_drive_player')
                            @else
                             @include('pages.movies.player.other')
                        @endif

                    @else
                        <div
                            style="text-align: center; padding: 70px 30px; font-size: 24px; font-weight: 700; background: #101011;
                                   border-radius: 10px; margin-top: 15px; min-height: 280px; line-height: 6;">
                            NO Source URL Set
                        </div>
                    @endif

                    <!-- Player Footer Section -->
                    <div class="player-footer-section">
                        <!-- Combined Title, Meta Info, and Action Buttons Row -->
                        <div class="player-footer-unified">
                            <div class="video-title-section">
                                <a href="{{ url('movies/details', ['slug' => $movies_info->video_slug, 'id' => $movies_info->id]) }}" class="video-title-link">
                                    <h3 class="video-title">{{ $movies_info->video_title }}</h3>
                                </a>
                            </div>

                            <!-- Video Info Meta -->
                            <div class="player-footer-meta">
                                <div class="meta-item">
                                    <i class="fa fa-eye"></i>
                                    <span>{{ number_format_short($movies_info->views) }} {{ trans('words.video_views') }}</span>
                                </div>

                                @if ($movies_info->release_date)
                                    <div class="meta-item">
                                        <i class="fa fa-calendar-alt"></i>
                                        <span>{{ date('M d, Y', $movies_info->release_date) }}</span>
                                    </div>
                                @endif

                                @if ($movies_info->duration)
                                    <div class="meta-item">
                                        <i class="fa fa-clock"></i>
                                        <span>{{ $movies_info->duration }}</span>
                                    </div>
                                @endif

                                @if ($movies_info->imdb_rating)
                                    <div class="meta-item imdb-rating">
                                        <img src="{{ URL::to('site_assets/images/imdb-logo.png') }}" alt="IMDb" class="imdb-logo" />
                                        <span>{{ $movies_info->imdb_rating }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="action-buttons-section">
                                @if ($movies_info->funding_url)
                                    <a href="{{ $movies_info->funding_url }}" target="_blank" class="action-btn donate-btn">
                                        <i class="fas fa-donate"></i>
                                        <span>Fund/Donate</span>
                                    </a>
                                @endif

                                @if ($movies_info->webpage_url)
                                    <a href="{{ $movies_info->webpage_url }}" target="_blank" class="action-btn webpage-btn">
                                        <i class="fas fa-globe"></i>
                                        <span>Webpage</span>
                                    </a>
                                @endif

                                @auth
                                    <form
                                        action="{{ $user_has_liked ? route('movie-videos.unlike', $movies_info->id) : route('movie-videos.like', $movies_info->id) }}"
                                        method="POST" class="like-form">
                                        @csrf
                                        <button type="submit" class="action-btn like-btn {{ $user_has_liked ? 'liked' : '' }}">
                                            <i class="fas fa-heart"></i>
                                            <span class="like-text">{{ $user_has_liked ? 'Unlike' : 'Like' }} ({{ $movies_info->likes }})</span>
                                        </button>
                                    </form>
                                @endauth

                                <button class="action-btn share-btn" data-toggle="modal" data-target="#social-media">
                                    <i class="fas fa-share-alt"></i>
                                    <span>{{ trans('words.share_text') }}</span>
                                </button>

                                <a href="{{ $random_movie ? url('movies/'.$random_movie->video_slug.'/'.$random_movie->id) : URL::to('/') }}" class="action-btn next-btn footer-stumble-btn" id="footer-next-btn">
                                    <i class="fas fa-random"></i>
                                    <span>Stumble</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side News Ticker and Banners -->
                <div class="col-md-3 d-flex flex-column justify-content-start" style="max-height: 100%;">
                    <!-- News Ticker Section -->
                <div id="watchAccordion">
                    <!-- News Content -->
                    <div id="collapseNews" class="toggle-content" style="display: block; overflow: hidden;">
                        <div class="card bg-dark text-white border-0 mb-2" style="height: 100%;">
                            <div class="card-body p-0" style="height: 100%; display: flex; flex-direction: column;">
                            <div class="news-ticker-container" style="flex-grow: 1; overflow-y: auto; position: relative;">
                                <div id="news-list-view">
                                    @php
                                        $has_news = false;
                                    @endphp

                                    @if(isset($mixed_feed) && count($mixed_feed) > 0)
                                        @php $has_news = true; @endphp
                                        @foreach($mixed_feed as $item)
                                            @if($item['type'] == 'news_ticker')
                                                @php
                                                    $is_admin = $item['is_admin'] ?? false;
                                                    $theme_color = $is_admin ? '#e50914' : '#fe8805'; // Red for admin, Orange for user
                                                    $badge_text = $is_admin ? 'NEWS' : 'USER NEWS';
                                                    if ($item['is_breaking']) $badge_text = 'BREAKING';
                                                @endphp
                                                <div class="news-item" style="background: {{ $is_admin ? 'rgba(229, 9, 20, 0.05)' : 'rgba(254, 136, 5, 0.05)' }}; padding: 10px; border-radius: 5px; border-left: 3px solid {{ $theme_color }}; margin-bottom: 20px;">
                                                    <div class="news-headline" style="color: #fff;">
                                                        <span class="breaking-badge" style="background: {{ $theme_color }}; padding: 3px 8px; font-weight: bold;">{{ $badge_text }}</span>
                                                        {{ $item['headline'] }}
                                                    </div>
                                                    <div class="news-details" style="color: #bbb;">
                                                        {!! \Illuminate\Support\Str::limit(strip_tags($item['details']), 100) !!}
                                                    </div>
                                                    <span class="news-time">
                                                        <i class="fa fa-clock-o"></i> {{ $item['created_at']->diffForHumans() }}
                                                        @if(!$is_admin && $item['user_name'])
                                                            &bull; by <strong>{{ $item['user_name'] }}</strong>
                                                        @endif
                                                    </span>
                                                    <a href="javascript:void(0)" onclick="$(this).next('.news-inline-details').slideToggle();" style="display: block; font-size: 12px; color: #fe8805; margin-top: 5px; font-weight: bold;">
                                                        <i class="fa fa-chevron-down"></i> Read Full Story
                                                    </a>
                                                    <div class="news-inline-details" style="display: none; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 13px; color: #ddd; line-height: 1.5;">
                                                        {!! $item['details'] !!}
                                                    </div>
                                                </div>
                                            @elseif($item['type'] == 'rss')
                                                <div class="news-item" style="background: rgba(0, 123, 255, 0.05); padding: 10px; border-radius: 5px; border-left: 3px solid #007bff; margin-bottom: 20px;">
                                                    <div class="news-headline" style="color: #fff;">
                                                        <span class="breaking-badge" style="background: #007bff; padding: 3px 8px; font-weight: bold;">{{ $item['feed_name'] }}</span>
                                                        {{ $item['headline'] }}
                                                    </div>
                                                    <div class="news-details" style="color: #bbb;">
                                                        {!! \Illuminate\Support\Str::limit(strip_tags($item['details']), 100) !!}
                                                    </div>
                                                    <span class="news-time">
                                                        <i class="fa fa-clock-o"></i> {{ $item['created_at']->diffForHumans() }}
                                                    </span>
                                                    <a href="javascript:void(0)" onclick="$(this).next('.rss-inline-details').slideToggle();" style="display: block; font-size: 12px; color: #fe8805; margin-top: 5px; font-weight: bold;">
                                                        <i class="fa fa-chevron-down"></i> Read Full Story
                                                    </a>
                                                    <div class="rss-inline-details" style="display: none; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 13px; color: #ddd; line-height: 1.5;">
                                                        {!! $item['details'] !!}
                                                    </div>
                                                </div>
                                            @elseif($item['type'] == 'job')
                                                @php $job = $item['job']; @endphp
                                                <div class="news-item" style="background: rgba(40, 167, 69, 0.05); padding: 10px; border-radius: 5px; border-left: 3px solid #28a745; margin-bottom: 20px;">
                                                    <div class="news-headline" style="color: #fff;">
                                                        <span class="breaking-badge" style="background: #28a745; padding: 3px 8px; font-weight: bold;">JOB</span>
                                                        {{ $job->title }}
                                                    </div>
                                                    <div class="news-details" style="color: #bbb;">
                                                        <strong>{{ $job->company }}</strong> - {{ $job->location }}
                                                    </div>
                                                    <span class="news-time">
                                                        <i class="fa fa-clock-o"></i> {{ $item['created_at']->diffForHumans() }}
                                                    </span>
                                                    <a href="javascript:void(0)" onclick="$(this).next('.job-inline-details').slideToggle();" style="display: block; font-size: 12px; color: #fe8805; margin-top: 5px; font-weight: bold;">
                                                        <i class="fa fa-chevron-down"></i> View Details
                                                    </a>
                                                    <div class="job-inline-details" style="display: none; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 13px; color: #ddd; line-height: 1.5;">
                                                        <strong>Salary:</strong> {{ $job->salary ? html_entity_decode(getCurrencySymbols(getcong('currency_code'))).' '.$job->salary : 'N/A' }}<br>
                                                        <strong>Contact:</strong> {{ $job->contact_details ?? 'N/A' }}<br><br>
                                                        {!! nl2br(e($job->description)) !!}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if(!$has_news)
                                        <p style="color: #888; padding: 15px;">No news updates at the moment.</p>
                                    @endif
                                </div>
                                <div id="news-detail-view" style="display: none; height: 100%; flex-direction: column;">
                                    <button class="btn btn-sm btn-secondary mb-2" id="back-to-news-list" style="align-self: flex-start; margin-bottom: 10px;"><i class="fa fa-arrow-left"></i> Back to News</button>
                                    <div id="news-content-body" style="flex-grow: 1; overflow-y: auto;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- Game Content -->
                    <div id="collapseGame" class="toggle-content" style="display: none; overflow: hidden;">
                        <div class="card bg-dark text-white border-0 mb-2">
                            <div class="card-body p-0">
                                <div class="pacman-game-container">
                                    <div class="pacman-game-wrapper" id="pacman-game-wrapper">
                                        <!-- Game will be loaded here dynamically -->
                                        <div style="text-align: center; padding: 50px; color: #fff;">
                                            <i class="fa fa-spinner fa-spin" style="font-size: 48px; margin-bottom: 20px;"></i>
                                            <p>Loading Game...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toggle Buttons at Bottom -->
                    <div class="card bg-dark text-white border-0 mt-2" id="accordionHeaders">
                        <div class="card-header p-2" id="headingNews" style="background: #111; border-bottom: 1px solid #333;">
                            <h5 class="mb-0">
                                <button class="btn btn-link text-white text-decoration-none w-100 text-left font-weight-bold active-toggle-btn" type="button" onclick="toggleSection('news')" id="newsToggleBtn">
                                    <i class="fa fa-newspaper-o mr-2"></i> Latest News
                                </button>
                            </h5>
                        </div>
                    </div>

                    <!-- Watermelon Game Button Moved from Footer -->
                    <div class="card bg-dark text-white border-0 mt-2">
                        <button class="action-btn game-btn w-100" id="open-game-modal" style="justify-content: center;">
                            <i class="fas fa-gamepad"></i>
                            <span>Play Watermelon</span>
                        </button>
                    </div>

                    @if ($belowNewsGamesButtons->isNotEmpty())
                        <div class="sidebar-buttons-container mt-2 px-2 pt-2">
                            @foreach ($belowNewsGamesButtons as $button)
                                <a href="{{ $button->link ?? '#' }}" class="btn btn-primary w-100 mb-2"
                                    style="padding: 8px 12px; font-size: 14px; font-weight: bold; border-radius: 8px;
                                           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                                           background-color: #{{ $button->color ? $button->color : '007bff' }};
                                           font-family: 'Perpetua', serif; text-align: center;
                                           text-decoration: none; color: #fff;
                                           position: relative;
                                           display: inline-block;
                                           overflow: hidden;
                                           border: 3px solid #00008B;
                                           transition: all 0.3s ease-in-out;"
                                    onmouseover="this.style.borderImage='linear-gradient(45deg, #ff0000, #00ff00, #0000ff, #ff00ff) 1';
                                                 this.style.borderStyle='solid';
                                                 this.style.borderWidth='3px';
                                                 this.style.borderRadius='8px';
                                                 this.style.borderColor='transparent';"
                                    onmouseout="this.style.borderImage='none';
                                                this.style.borderColor='#00008B';
                                                this.style.borderStyle='solid';
                                                this.style.borderWidth='3px';
                                                this.style.borderRadius='8px';"
                                    target="_blank">
                                    {{ $button->title }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

                <div class="sidebar-banners-container mt-3">
                    @if ($banners->isNotEmpty())
                        @foreach ($banners as $banner)
                            <div class="banner-item">
                                <a href="{{ $banner->link ?? '#' }}" target="_blank" class="banner-link">
                                    <div class="banner-wrapper">
                                        <img src="{{ url($banner->image) }}" alt="Advertisement" class="banner-image" style="object-fit: cover; border-radius: 5px; width: 100%; max-height: 350px;">
                                        <div class="banner-overlay">
                                            <i class="fas fa-external-link-alt"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

@if(request()->getHost() != 'home.cineworm.org')

@if (get_web_banner('home_top') != '')
    <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="d-flex justify-content-center align-items-center mx-auto"
                        style="max-width: 728px; height: auto;">
                        <div style="width: 100%; height: auto; overflow: hidden;">
                            <a href="{{ get_web_banner('ad_url') }}" target="_blank">
                                <img src="{{ url(get_web_banner('home_top')) }}" alt="Banner"
                                    style="max-width: 100%; height: auto; display: block;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif




@if(request()->getHost() != 'home.cineworm.org')
    @if (Auth::check() && $recently_watched->count() > 0)
        <!-- Start Recently Watched Video Section -->
        <div class="video-shows-section vfx-item-ptb">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="vfx-item-section">
                            <h3>{{ trans('words.recently_watched') }}</h3>
                        </div>
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
                                                        <img src="{{ URL::to('/' . $info->channel_thumb) }}"
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
                    </div>
                </div>
            </div>
        </div>
        <!-- End Recently Watched Video Section -->

    @endif
    <div class="video-shows-section vfx-item-ptb">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="vfx-item-section">
                        <h3>Movies List</h3>
                    </div>
                    <div class="recently-watched-video-carousel owl-carousel">
                        @foreach ($movies_list as $movies_data)
                            <div class="single-video">
                                @if (Auth::check())
                                    <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                        title="{{ $movies_data->video_title }}">
                                    @else
                                        @if ($movies_data->video_access == 'Paid')
                                            <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                title="{{ $movies_data->video_title }}" data-toggle="modal"
                                                data-target="#loginAlertModal">
                                            @else
                                                <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                    title="{{ $movies_data->video_title }}">
                                        @endif
                                @endif
                                <div class="video-img">
                                    @if ($movies_data->video_access == 'Paid')
                                        <div class="vid-lab-premium">
                                            <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}" alt="premium"
                                                title="premium">
                                        </div>
                                    @endif
                                    <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                        alt="{{ stripslashes($movies_data->video_title) }}"
                                        title="{{ stripslashes($movies_data->video_title) }}" class="img-fluid fixed-img">
                                    <div style="background:rgba(255,0,0,0.6);color:white;padding:3px;">
                                        <span>{{ Str::limit(stripslashes($movies_data->video_title), 20) }}</span>
                                        <br>
                                        {{-- <strong>Duration:</strong> {{ $movies_data->duration ?? 'Unknown' }}
                                            <br> --}}
                                        <strong>Genre:</strong>
                                        {{ App\Genres::where('id', $movies_data->movie_genre_id)->first()->genre_name ?? 'Not specified' }}
                                    </div>

                                </div>
                                </a>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($genres as $genre)
        @php
            $filteredMovies = $movies_list->where('movie_genre_id', $genre->id);
        @endphp

        @if ($filteredMovies->count() > 0)
            <div class="video-shows-section vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3>{{ $genre->genre_name }}</h3>
                            </div>
                            <div class="recently-watched-video-carousel owl-carousel">
                                @foreach ($filteredMovies as $movies_data)
                                    <div class="single-video">
                                        @if (Auth::check())
                                            <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                title="{{ $movies_data->video_title }}">
                                            @else
                                                @if ($movies_data->video_access == 'Paid')
                                                    <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                        title="{{ $movies_data->video_title }}" data-toggle="modal"
                                                        data-target="#loginAlertModal">
                                                    @else
                                                        <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                                            title="{{ $movies_data->video_title }}">
                                                @endif
                                        @endif
                                        <div class="video-img">
                                            @if ($movies_data->video_access == 'Paid')
                                                <div class="vid-lab-premium">
                                                    <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                        alt="premium" title="premium">
                                                </div>
                                            @endif
                                            <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                                alt="{{ stripslashes($movies_data->video_title) }}"
                                                title="{{ stripslashes($movies_data->video_title) }}"
                                                class="img-fluid fixed-img">
                                            <div style="background:rgba(255,0,0,0.6);color:white;padding:3px;">
                                                <span>{{ Str::limit(stripslashes($movies_data->video_title), 20) }}</span>
                                                <br>
                                                {{-- <strong>Duration:</strong> {{ $movies_data->duration ?? 'Unknown' }}
                                                    <br> --}}
                                                <strong>Genre:</strong>
                                                {{ App\Genres::where('id', $movies_data->movie_genre_id)->first()->genre_name ?? 'Not specified' }}
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="video-shows-section vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3>{{ $genre->genre_name }}</h3>
                            </div>
                            <p>No videos available for this genre.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach



    </div>

    @if (getcong('menu_movies'))
        <!-- Start Upcoming Section -->
        @if ($upcoming_movies->count() > 0)

            <!-- Start Movies Video Carousel -->
            <div class="video-carousel-area vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3>{{ trans('words.upcoming_movies') }}</h3>
                            </div>
                            <div class="video-carousel owl-carousel">

                                @foreach ($upcoming_movies as $movies_data)
                                    <div class="single-video">
                                        <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                            title="{{ $movies_data->video_title }}">
                                            <div class="video-img">
                                                @if ($movies_data->video_access == 'Paid')
                                                    <div class="vid-lab-premium">
                                                        <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                            alt="ic-premium" title="Movies">
                                                    </div>
                                                @endif
                                                <span
                                                    class="video-item-content">{{ stripslashes($movies_data->video_title) }}</span>
                                                <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                                    alt="{{ $movies_data->video_title }}"
                                                    title="Movies-{{ $movies_data->video_title }}">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Latest Movies Video Carousel -->
        @endif
        <!-- End Upcoming Section -->
    @endif

    @if (getcong('menu_shows'))
        <!-- Start Upcoming Section -->
        @if ($upcoming_series->count() > 0)

            <!-- Start Latest Shows Video Section -->
            <div class="video-shows-section vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3>{{ trans('words.upcoming_shows') }}</h3>

                            </div>
                            <div class="video-shows-carousel owl-carousel">
                                @foreach ($upcoming_series as $series_data)
                                    <div class="single-video">
                                        <a href="{{ URL::to('shows/details/' . $series_data->series_slug . '/' . $series_data->id) }}"
                                            title="{{ $series_data->series_name }}">
                                            <div class="video-img">
                                                @if ($series_data->series_access == 'Paid')
                                                    <div class="vid-lab-premium"><img
                                                            src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                            alt="premium" title="premium"></div>
                                                @endif
                                                <span
                                                    class="video-item-content">{{ stripslashes($series_data->series_name) }}</span>
                                                <img src="{{ URL::to('/' . $series_data->series_poster) }}"
                                                    alt="{{ $series_data->series_name }}"
                                                    title="Shows-{{ $series_data->series_name }}">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Latest Shows Video Section -->
        @endif
        <!-- End Upcoming Section -->
    @endif

    @foreach ($home_sections as $sections_data)

        @if (getcong('menu_movies'))
            @if ($sections_data->post_type == 'Movie')
                <!-- Start Movies Video Carousel -->
                <div class="video-carousel-area vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>
                                <div class="video-carousel owl-carousel">

                                    @foreach (explode(',', $sections_data->movie_ids) as $movie_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('movies/details/' . App\Movies::getMoviesInfo($movie_data, 'video_slug') . '/' . App\Movies::getMoviesInfo($movie_data, 'id')) }}"
                                                title="{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}">
                                                <div class="video-img">
                                                    @if (App\Movies::getMoviesInfo($movie_data, 'video_access') == 'Paid')
                                                        <div class="vid-lab-premium">
                                                            <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Movies-ic-premium">
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ stripslashes(App\Movies::getMoviesInfo($movie_data, 'video_title')) }}</span>
                                                    <img src="{{ URL::to('/' . App\Movies::getMoviesInfo($movie_data, 'video_image_thumb')) }}"
                                                        alt="{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}"
                                                        title="Movies-{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Movies Video Carousel -->
            @endif
        @endif

        @if (getcong('menu_shows'))
            @if ($sections_data->post_type == 'Shows')
                <!-- Start Latest Shows Video Section -->
                <div class="video-shows-section vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>
                                <div class="video-shows-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->show_ids) as $show_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('shows/details/' . App\Series::getSeriesInfo($show_data, 'series_slug') . '/' . $show_data) }}"
                                                title="{{ App\Series::getSeriesInfo($show_data, 'series_name') }}">
                                                <div class="video-img">
                                                    @if (App\Series::getSeriesInfo($show_data, 'series_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Shows-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ stripslashes(App\Series::getSeriesInfo($show_data, 'series_name')) }}</span>
                                                    <img src="{{ URL::to('/' . App\Series::getSeriesInfo($show_data, 'series_poster')) }}"
                                                        alt="{{ App\Series::getSeriesInfo($show_data, 'series_name') }}"
                                                        title="Shows-{{ App\Series::getSeriesInfo($show_data, 'series_name') }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Shows Video Section -->
            @endif
        @endif


        @if (getcong('menu_sports'))
            @if ($sections_data->post_type == 'Sports')
                <!-- Start Sports Video Section -->
                <div class="video-shows-section sport-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->sport_ids) as $sport_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('sports/details/' . App\Sports::getSportsInfo($sport_data, 'video_slug') . '/' . $sport_data) }}"
                                                title="{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}">
                                                <div class="video-img">
                                                    @if (App\Sports::getSportsInfo($sport_data, 'video_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Sports-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}</span>
                                                    <img src="{{ URL::to('/' . App\Sports::getSportsInfo($sport_data, 'video_image')) }}"
                                                        alt="{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}"
                                                        title="Sports-{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}" />
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sports Section -->
            @endif
        @endif


        @if (getcong('menu_livetv'))
            @if ($sections_data->post_type == 'LiveTV')
                <!-- Start Live TV Video Section -->
                <div class="video-shows-section live-tv-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->tv_ids) as $tv_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('livetv/details/' . App\LiveTV::getLiveTvInfo($tv_data, 'channel_slug') . '/' . $tv_data) }}"
                                                title="{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}">
                                                <div class="video-img">
                                                    @if (App\LiveTV::getLiveTvInfo($tv_data, 'channel_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="LiveTV-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}</span>
                                                    <img src="{{ URL::to('/' . App\LiveTV::getLiveTvInfo($tv_data, 'channel_thumb')) }}"
                                                        alt="{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}"
                                                        title="LiveTV-{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}" />
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Live TV Section -->
            @endif
        @endif

    @endforeach
 @endif
@if (get_web_banner('home_top') != '')
    <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="d-flex justify-content-center align-items-center mx-auto"
                        style="max-width: 728px; height: auto;">
                        <div style="width: 100%; height: auto; overflow: hidden;">
                            <a href="{{ get_web_banner('ad_url') }}" target="_blank">
                                <img src="{{ url(get_web_banner('home_top')) }}" alt="Banner"
                                    style="max-width: 100%; height: auto; display: block;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
 @endif



    <script>
        // Track if game has been loaded
        let gameLoaded = false;

        // Toggle function for News and Games
        function toggleSection(section) {
            var newsSection = document.getElementById('collapseNews');
            var gameSection = document.getElementById('collapseGame');
            var newsBtn = document.getElementById('newsToggleBtn');
            var gameBtn = document.getElementById('gameToggleBtn');

            if (section === 'news') {
                // Show news, hide game
                newsSection.style.display = 'block';
                gameSection.style.display = 'none';
                newsBtn.classList.add('active-toggle-btn');
                gameBtn.classList.remove('active-toggle-btn');
            } else if (section === 'game') {
                // Show game, hide news
                newsSection.style.display = 'none';
                gameSection.style.display = 'block';
                newsBtn.classList.remove('active-toggle-btn');
                gameBtn.classList.add('active-toggle-btn');

                // Load game dynamically on first click
                if (!gameLoaded) {
                    loadPacmanGame();
                    gameLoaded = true;
                }
            }

            // Recalculate heights after toggle
            setTimeout(function() {
                if (typeof matchHeightToPlayer === 'function') {
                    matchHeightToPlayer();
                }
            }, 100);
        }

        // Function to load Pacman game dynamically
        function loadPacmanGame() {
            var gameWrapper = document.getElementById('pacman-game-wrapper');
            if (gameWrapper) {
                // Create iframe element
                var iframe = document.createElement('iframe');
                iframe.src = "{{ URL::asset('games/pacman/index.html') }}?v={{ time() }}";
                iframe.style.width = '100%';
                iframe.style.height = '100%';
                iframe.style.border = 'none';
                iframe.setAttribute('allowfullscreen', '');
                iframe.setAttribute('title', 'Pacman Game');

                // Clear loading message and append iframe
                gameWrapper.innerHTML = '';
                gameWrapper.appendChild(iframe);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Match news ticker/game height with player height
            window.matchHeightToPlayer = function() {
                // Only apply on desktop (md and above)
                if (window.innerWidth < 768) {
                     // Reset heights on mobile - let CSS handle it
                    var ticker = document.querySelector('.news-ticker-container');
                    if (ticker) ticker.style.height = '';

                    var gameWrapper = document.querySelector('.pacman-game-wrapper');
                    if (gameWrapper) gameWrapper.style.height = ''; // Let CSS media query handle mobile height
                    return;
                }

                var player = document.getElementById('main-player-column');
                if (!player) return;

                var playerHeight = player.offsetHeight;

                // Calculate available height for content
                // Buttons moved to player column, no longer in sidebar
                var buttonsHeight = 0;

                // Toggle buttons are now at the bottom
                var toggleButtons = document.getElementById('accordionHeaders');
                var toggleButtonsHeight = toggleButtons ? toggleButtons.offsetHeight : 0;
                // Add margin top of toggle buttons
                if (toggleButtons) {
                     var style = window.getComputedStyle(toggleButtons);
                     toggleButtonsHeight += parseInt(style.marginTop) || 0;
                }

                // Available height for the collapsible content
                var availableHeight = playerHeight - buttonsHeight - toggleButtonsHeight;

                // Ensure non-negative
                if (availableHeight < 200) availableHeight = 200; // Minimum safety height

                // Adjust News Ticker if visible
                var newsCollapse = document.getElementById('collapseNews');
                if (newsCollapse && newsCollapse.style.display !== 'none') {
                    var ticker = document.querySelector('.news-ticker-container');
                    if (ticker) {
                        ticker.style.height = availableHeight + 'px';
                    }
                }

                // Adjust Game if visible - REMOVED
                // var gameCollapse = document.getElementById('collapseGame');
                // if (gameCollapse && gameCollapse.style.display !== 'none') {
                //    var gameWrapper = document.querySelector('.pacman-game-wrapper');
                //    if (gameWrapper) {
                //        gameWrapper.style.height = availableHeight + 'px';
                //    }
                // }
            }

            // Call on load, resize, and periodically
            window.addEventListener('resize', matchHeightToPlayer);
            window.addEventListener('load', matchHeightToPlayer);

            // Check periodically for initialization
            var attempts = 0;
            var checkInterval = setInterval(function() {
                matchHeightToPlayer();
                attempts++;
                if (attempts > 20) clearInterval(checkInterval);
            }, 500);

            // Auto-scroll news ticker
            function autoScrollTicker() {
                var ticker = document.querySelector('.news-ticker-container');
                if (!ticker) return;

                var scrollSpeed = 1;
                var scrollInterval = 50;
                var pauseAtEnd = 2000;
                var pauseAtTop = 3000;
                var isScrolling = false;
                var hasStarted = false;

                setTimeout(function() {
                    hasStarted = true;
                    startScrolling();
                }, pauseAtTop);

                function startScrolling() {
                    if (isScrolling) return;
                    isScrolling = true;

                    var scrollTimer = setInterval(function() {
                        if (!ticker) {
                            clearInterval(scrollTimer);
                            return;
                        }

                        // Check if news section is visible
                        var newsCollapse = document.getElementById('collapseNews');
                        if (newsCollapse && newsCollapse.style.display === 'none') {
                            return;
                        }

                        if (ticker.scrollTop + ticker.clientHeight >= ticker.scrollHeight - 5) {
                            clearInterval(scrollTimer);
                            isScrolling = false;
                            setTimeout(function() {
                                ticker.scrollTo({ top: 0, behavior: 'smooth' });
                                setTimeout(function() { startScrolling(); }, pauseAtTop);
                            }, pauseAtEnd);
                        } else {
                            ticker.scrollTop += scrollSpeed;
                        }
                    }, scrollInterval);
                }

                ticker.addEventListener('mouseenter', function() { scrollSpeed = 0; });
                ticker.addEventListener('mouseleave', function() { if (hasStarted) scrollSpeed = 1; });
            }

            autoScrollTicker();

            // AJAX Like Button Handler
            const likeForm = document.querySelector('.like-form');
            if (likeForm) {
                likeForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const form = this;
                    const button = form.querySelector('.like-btn');
                    const likeText = form.querySelector('.like-text');
                    const formData = new FormData(form);

                    // Disable button during request
                    button.disabled = true;

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Toggle liked state
                            button.classList.toggle('liked');

                            // Update like count and text
                            if (data.liked) {
                                likeText.textContent = 'Unlike (' + data.likes + ')';
                                form.action = form.action.replace('/like/', '/unlike/');
                            } else {
                                likeText.textContent = 'Like (' + data.likes + ')';
                                form.action = form.action.replace('/unlike/', '/like/');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        // Re-enable button
                        button.disabled = false;
                    });
                });
            }
        });
    </script>

    <style>
        /* Game Modal Styling */
        .game-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 600px;
            background: #000;
            border: 2px solid #fe8805;
            border-radius: 8px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 20px rgba(0,0,0,0.8);
            resize: both;
            overflow: hidden;
        }

        .game-modal-header {
            padding: 10px 15px;
            background: #1a1a1a;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
            border-bottom: 1px solid #333;
        }

        .game-modal-title {
            font-weight: bold;
            color: #fe8805;
        }

        .game-modal-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            line-height: 1;
        }

        .game-modal-close:hover {
            color: #fe8805;
        }

        .game-modal-body {
            flex: 1;
            background: #000;
            position: relative;
            overflow: hidden;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .game-modal {
                width: 85% !important;
                height: 60vh !important;
                /* Use initial centered position but allow overriding via JS (no !important on position) */
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                border-radius: 4px;
                resize: none;
            }

            .game-modal-header {
                padding: 15px; /* Larger touch target */
                cursor: move; /* Enable move cursor on mobile */
            }

            .game-modal-close {
                font-size: 28px;
                padding: 5px;
            }
        }

        /* ── Modern Watermelon Leaderboard Popover ───────────────────────────── */
        .wm-header-lb-btn {
            background: linear-gradient(135deg, rgba(254, 136, 5, 0.25) 0%, rgba(254, 136, 5, 0.08) 100%);
            border: 1px solid rgba(254, 136, 5, 0.6);
            color: #fe8805;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
        .wm-header-lb-btn:hover {
            background: #fe8805;
            color: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(254, 136, 5, 0.45);
        }
        .wm-header-lb-btn i {
            font-size: 13px;
        }

        .wm-lb-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.72);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99;
            padding: 12px;
            animation: wmOverlayFadeIn 0.22s ease-out;
        }

        @keyframes wmOverlayFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .wm-lb-card {
            background: #141518;
            background: linear-gradient(180deg, #1c1d22 0%, #111215 100%);
            border: 1px solid rgba(254, 136, 5, 0.45);
            border-radius: 14px;
            width: 100%;
            max-width: 430px;
            max-height: 92%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.9), 0 0 25px rgba(254, 136, 5, 0.15);
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            animation: wmCardPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes wmCardPop {
            from { transform: scale(0.92) translateY(8px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }

        .wm-lb-card-header {
            padding: 12px 16px;
            background: rgba(254, 136, 5, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .wm-lb-title-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .wm-lb-title-wrap h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.3px;
        }

        .wm-lb-card-close {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #bbb;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            line-height: 1;
            transition: all 0.15s ease;
        }
        .wm-lb-card-close:hover {
            background: rgba(254, 136, 5, 0.3);
            color: #fff;
            border-color: #fe8805;
        }

        /* Player Profile Card in Leaderboard */
        .wm-lb-player-box {
            margin: 10px 14px 6px 14px;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(254, 136, 5, 0.25);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .wm-lb-player-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 1;
        }

        .wm-lb-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fe8805 0%, #22c55e 100%);
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        }

        .wm-lb-player-meta {
            min-width: 0;
            flex: 1;
        }

        .wm-lb-player-name {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .wm-guest-edit-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
        }

        .wm-guest-edit-input {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(254, 136, 5, 0.4);
            color: #fff;
            border-radius: 6px;
            font-size: 12px;
            padding: 2px 8px;
            width: 130px;
            outline: none;
            transition: border-color 0.2s;
        }
        .wm-guest-edit-input:focus {
            border-color: #fe8805;
        }

        .wm-guest-save-btn {
            background: #fe8805;
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .wm-guest-save-btn:hover {
            background: #ffa133;
        }

        .wm-lb-player-score-tag {
            font-size: 11px;
            color: #999;
        }
        .wm-lb-player-score-tag strong {
            color: #fe8805;
            font-weight: 700;
        }

        .wm-lb-rank-badge {
            text-align: right;
            flex-shrink: 0;
        }
        .wm-lb-rank-badge .tag {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            color: #777;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .wm-lb-rank-badge .val {
            font-size: 16px;
            font-weight: 800;
            color: #fe8805;
            line-height: 1.1;
        }

        /* Leaderboard table list */
        .wm-lb-table-head {
            padding: 6px 16px;
            display: flex;
            align-items: center;
            font-size: 10px;
            font-weight: 700;
            color: #777;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .wm-lb-list {
            flex: 1;
            overflow-y: auto;
            max-height: 280px;
            padding: 4px 8px;
        }
        .wm-lb-list::-webkit-scrollbar { width: 5px; }
        .wm-lb-list::-webkit-scrollbar-thumb { background: rgba(254, 136, 5, 0.4); border-radius: 4px; }

        .wm-lb-row {
            display: flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 8px;
            margin-bottom: 2px;
            font-size: 12px;
            color: #ddd;
            transition: background 0.15s;
        }
        .wm-lb-row:hover {
            background: rgba(255, 255, 255, 0.04);
        }
        .wm-lb-row.me {
            background: rgba(254, 136, 5, 0.14);
            border: 1px solid rgba(254, 136, 5, 0.35);
        }

        .wm-lb-col-rank {
            width: 34px;
            font-weight: 800;
            font-size: 12px;
            display: flex;
            align-items: center;
        }
        .wm-lb-col-rank.gold { color: #ffd700; font-size: 14px; }
        .wm-lb-col-rank.silver { color: #d1d5db; font-size: 14px; }
        .wm-lb-col-rank.bronze { color: #d97706; font-size: 14px; }

        .wm-lb-col-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
            padding-right: 8px;
        }
        .wm-lb-col-name .you-tag {
            font-size: 10px;
            background: #fe8805;
            color: #000;
            padding: 1px 5px;
            border-radius: 4px;
            font-weight: 700;
            margin-left: 6px;
        }

        .wm-lb-col-score {
            width: 70px;
            text-align: right;
            font-weight: 800;
            color: #22c55e;
            font-variant-numeric: tabular-nums;
        }

        .wm-lb-empty, .wm-lb-loading {
            padding: 24px 16px;
            text-align: center;
            color: #777;
            font-size: 12px;
        }

        .wm-lb-card-footer {
            padding: 8px 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(0, 0, 0, 0.3);
        }

        .wm-lb-tip-text {
            font-size: 10px;
            color: #666;
        }

        .wm-lb-refresh-btn {
            background: transparent;
            border: 1px solid rgba(254, 136, 5, 0.35);
            color: #fe8805;
            border-radius: 6px;
            font-size: 11px;
            padding: 3px 10px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .wm-lb-refresh-btn:hover {
            background: rgba(254, 136, 5, 0.2);
        }
    </style>



    <!-- Game Modal -->
    <div id="watermelon-game-modal" class="game-modal" style="display: none;">
        <div class="game-modal-header" id="game-modal-header">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 16px;">🍉</span>
                <span class="game-modal-title" id="game-modal-title">Watermelon Game</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <button id="wm-lb-toggle" class="wm-header-lb-btn" type="button" title="View Global Leaderboard">
                    <i class="fas fa-trophy" style="color: #ffd700;"></i>
                    <span>Leaderboard</span>
                </button>
                <button class="game-modal-close" id="close-game-modal" title="Close Game">&times;</button>
            </div>
        </div>
        <div class="game-modal-body" style="position: relative; overflow: hidden;">
            <!-- Game iframe (full size) -->
            <iframe id="game-iframe" src="" style="width: 100%; height: 100%; border: none; display: block;"></iframe>

            <!-- Leaderboard Centered Overlay Modal (hidden by default) -->
            <div id="wm-leaderboard-overlay" class="wm-lb-overlay" style="display: none;">
                <div class="wm-lb-card">
                    <!-- Header -->
                    <div class="wm-lb-card-header">
                        <div class="wm-lb-title-wrap">
                            <span style="font-size: 18px;">🏆</span>
                            <div>
                                <h4>Global Leaderboard</h4>
                            </div>
                        </div>
                        <button id="wm-lb-close-btn" class="wm-lb-card-close" type="button" title="Close">&times;</button>
                    </div>

                    <!-- User/Guest Card -->
                    <div class="wm-lb-player-box">
                        <div class="wm-lb-player-left">
                            <div class="wm-lb-avatar">
                                @auth
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                @else
                                    <i class="fas fa-user-astronaut"></i>
                                @endauth
                            </div>
                            <div class="wm-lb-player-meta">
                                @auth
                                    <div class="wm-lb-player-name">{{ Auth::user()->name }}</div>
                                @else
                                    <div class="wm-guest-edit-wrap">
                                        <input id="wm-guest-name-input" class="wm-guest-edit-input" type="text" maxlength="20" placeholder="Guest Name">
                                        <button id="wm-guest-save-btn" class="wm-guest-save-btn" type="button">Save</button>
                                    </div>
                                @endauth
                                <div class="wm-lb-player-score-tag">
                                    Your High Score: <strong id="wm-my-score-text">0</strong>
                                </div>
                            </div>
                        </div>
                        <div class="wm-lb-rank-badge">
                            <span class="tag">Rank</span>
                            <span class="val" id="wm-my-rank-text">--</span>
                        </div>
                    </div>

                    <!-- Table Header -->
                    <div class="wm-lb-table-head">
                        <span class="wm-lb-col-rank">#</span>
                        <span class="wm-lb-col-name">PLAYER</span>
                        <span class="wm-lb-col-score">SCORE</span>
                    </div>

                    <!-- Rankings List -->
                    <div class="wm-lb-list" id="wm-lb-list">
                        <div class="wm-lb-loading"><i class="fas fa-spinner fa-spin"></i> Loading rankings...</div>
                    </div>

                    <!-- Footer -->
                    <div class="wm-lb-card-footer">
                        <span class="wm-lb-tip-text"><i class="fas fa-medal text-warning"></i> Merge watermelons to reach top!</span>
                        <button class="wm-lb-refresh-btn" type="button" onclick="wmLoadLeaderboard()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal     = document.getElementById('watermelon-game-modal');
            var btn       = document.getElementById('open-game-modal');
            var closeBtn  = document.getElementById('close-game-modal');
            var iframe    = document.getElementById('game-iframe');
            var modalTitle = document.getElementById('game-modal-title');
            var lbOverlay = document.getElementById('wm-leaderboard-overlay');
            var lbToggle  = document.getElementById('wm-lb-toggle');
            var lbCloseBtn = document.getElementById('wm-lb-close-btn');

            var watermelonUrl = "{{ URL::asset('games/Watermelon/index.html') }}";

            // ─── State ──────────────────────────────────────────
            var WM_SCORE_URL      = "{{ route('game.watermelon.score') }}";
            var WM_LB_URL         = "{{ route('game.watermelon.leaderboard') }}";
            var WM_CSRF           = "{{ csrf_token() }}";
            @auth
            var WM_IS_AUTH        = true;
            var WM_PLAYER_NAME    = "{{ Auth::user()->name }}";
            @else
            var WM_IS_AUTH        = false;
            var WM_PLAYER_NAME    = localStorage.getItem('wm_guest_name') || 'Guest';
            @endauth

            var WM_GUEST_TOKEN = localStorage.getItem('wm_guest_token');
            if (!WM_GUEST_TOKEN) {
                WM_GUEST_TOKEN = 'guest_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                localStorage.setItem('wm_guest_token', WM_GUEST_TOKEN);
            }

            // Guest name input setup
            var guestInput = document.getElementById('wm-guest-name-input');
            var guestSave  = document.getElementById('wm-guest-save-btn');
            if (guestInput) {
                guestInput.value = WM_PLAYER_NAME;
            }
            if (guestSave) {
                guestSave.onclick = function(e) {
                    e.stopPropagation();
                    var val = (guestInput.value || '').trim();
                    if (!val) return;
                    WM_PLAYER_NAME = val.substring(0, 20);
                    localStorage.setItem('wm_guest_name', WM_PLAYER_NAME);
                    guestSave.textContent = '✓ Saved';
                    setTimeout(function(){ guestSave.textContent = 'Save'; }, 1200);
                    wmLoadLeaderboard();
                };
            }

            // ─── Leaderboard Popover Toggle ──────────────────────
            function openLeaderboard() {
                if (!lbOverlay) return;
                lbOverlay.style.display = 'flex';
                wmLoadLeaderboard();
            }

            function closeLeaderboard() {
                if (!lbOverlay) return;
                lbOverlay.style.display = 'none';
            }

            if (lbToggle) {
                lbToggle.onclick = function(e) {
                    e.stopPropagation();
                    if (lbOverlay.style.display === 'none' || !lbOverlay.style.display) {
                        openLeaderboard();
                    } else {
                        closeLeaderboard();
                    }
                };
            }

            if (lbCloseBtn) {
                lbCloseBtn.onclick = function(e) {
                    e.stopPropagation();
                    closeLeaderboard();
                };
            }

            // Click outside card to dismiss
            if (lbOverlay) {
                lbOverlay.onclick = function(e) {
                    if (e.target === lbOverlay) {
                        closeLeaderboard();
                    }
                };
            }

            // ─── Load Rankings ───────────────────────────────────
            window.wmLoadLeaderboard = function() {
                var list = document.getElementById('wm-lb-list');
                var myScoreText = document.getElementById('wm-my-score-text');
                var myRankText  = document.getElementById('wm-my-rank-text');

                var url = WM_LB_URL + '?guest_token=' + encodeURIComponent(WM_GUEST_TOKEN);
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r){ return r.json(); })
                    .then(function(data) {
                        if (!data.success) return;

                        if (data.my_entry) {
                            if (myScoreText) myScoreText.textContent = Number(data.my_entry.score).toLocaleString();
                            if (myRankText)  myRankText.textContent  = '#' + data.my_entry.rank;
                        }

                        renderLeaderboardRows(data.top, data.my_entry);
                    })
                    .catch(function(e){
                        if (list) list.innerHTML = '<div class="wm-lb-empty">Unable to load leaderboard.</div>';
                    });
            };

            function renderLeaderboardRows(rows, myEntry) {
                var list = document.getElementById('wm-lb-list');
                if (!list) return;

                if (!rows || rows.length === 0) {
                    list.innerHTML = '<div class="wm-lb-empty">No scores yet. Play to be the first!</div>';
                    return;
                }

                var html = '';
                rows.forEach(function(row) {
                    var rankClass = '';
                    var medalIcon = '#' + row.rank;
                    if (row.rank === 1) { rankClass = 'gold';   medalIcon = '🥇'; }
                    else if (row.rank === 2) { rankClass = 'silver'; medalIcon = '🥈'; }
                    else if (row.rank === 3) { rankClass = 'bronze'; medalIcon = '🥉'; }

                    var isMe = false;
                    if (WM_IS_AUTH && row.user_id) {
                        isMe = myEntry && myEntry.rank === row.rank && myEntry.score === row.score;
                    } else if (!WM_IS_AUTH && row.guest_token === WM_GUEST_TOKEN) {
                        isMe = true;
                    }

                    html += '<div class="wm-lb-row' + (isMe ? ' me' : '') + '">' +
                        '<span class="wm-lb-col-rank ' + rankClass + '">' + medalIcon + '</span>' +
                        '<span class="wm-lb-col-name">' + escHtml(row.player_name) + (isMe ? '<span class="you-tag">YOU</span>' : '') + '</span>' +
                        '<span class="wm-lb-col-score">' + Number(row.score).toLocaleString() + '</span>' +
                    '</div>';
                });
                list.innerHTML = html;
            }

            // ─── Score Tracking & Submission ────────────────────
            var wmHighestScore   = 0;
            var wmSubmittedScore = 0;
            var wmPollInterval   = null;

            function wmGetLiveScoreFromIframe() {
                try {
                    if (iframe && iframe.contentWindow) {
                        var win = iframe.contentWindow;
                        if (typeof win.__wm_live_score === 'number' && win.__wm_live_score > 0) {
                            return win.__wm_live_score;
                        }
                    }
                } catch(e) {}
                return 0;
            }

            function wmSubmitScore(score, isClosing) {
                score = parseInt(score, 10);
                if (isNaN(score) || score <= 0) return;
                if (score <= wmSubmittedScore && !isClosing) return;

                wmSubmittedScore = Math.max(wmSubmittedScore, score);
                wmHighestScore   = Math.max(wmHighestScore, score);

                var myScoreText = document.getElementById('wm-my-score-text');
                if (myScoreText) myScoreText.textContent = Number(wmHighestScore).toLocaleString();

                var formData = new FormData();
                formData.append('_token',      WM_CSRF);
                formData.append('score',       score);
                formData.append('guest_token', WM_GUEST_TOKEN);
                if (!WM_IS_AUTH) {
                    formData.append('player_name', WM_PLAYER_NAME);
                }

                try {
                    fetch(WM_SCORE_URL, {
                        method: 'POST',
                        body: formData,
                        keepalive: true,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function(r){ return r.json(); })
                    .then(function(data) {
                        if (data && data.success) {
                            var myRankText = document.getElementById('wm-my-rank-text');
                            if (myRankText && data.rank) myRankText.textContent = '#' + data.rank;
                        }
                    })
                    .catch(function(e){ console.warn('Score submit error', e); });
                } catch(e) {}
            }

            function wmSyncLiveScore(isClosing) {
                var iframeScore = wmGetLiveScoreFromIframe();
                var best = Math.max(wmHighestScore, iframeScore);
                if (best > wmSubmittedScore) {
                    wmSubmitScore(best, isClosing);
                }
            }

            // ─── Leaderboard Popover Toggle ──────────────────────
            function openLeaderboard() {
                if (!lbOverlay) return;
                wmSyncLiveScore(false);
                lbOverlay.style.display = 'flex';
                wmLoadLeaderboard();
            }

            function closeLeaderboard() {
                if (!lbOverlay) return;
                lbOverlay.style.display = 'none';
            }

            if (lbToggle) {
                lbToggle.onclick = function(e) {
                    e.stopPropagation();
                    if (lbOverlay.style.display === 'none' || !lbOverlay.style.display) {
                        openLeaderboard();
                    } else {
                        closeLeaderboard();
                    }
                };
            }

            if (lbCloseBtn) {
                lbCloseBtn.onclick = function(e) {
                    e.stopPropagation();
                    closeLeaderboard();
                };
            }

            // Click outside card to dismiss
            if (lbOverlay) {
                lbOverlay.onclick = function(e) {
                    if (e.target === lbOverlay) {
                        closeLeaderboard();
                    }
                };
            }

            // ─── Load Rankings ───────────────────────────────────
            window.wmLoadLeaderboard = function() {
                var list = document.getElementById('wm-lb-list');
                var myScoreText = document.getElementById('wm-my-score-text');
                var myRankText  = document.getElementById('wm-my-rank-text');

                // If we already have a live score higher than display, show it
                if (wmHighestScore > 0 && myScoreText) {
                    myScoreText.textContent = Number(wmHighestScore).toLocaleString();
                }

                var url = WM_LB_URL + '?guest_token=' + encodeURIComponent(WM_GUEST_TOKEN);
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r){ return r.json(); })
                    .then(function(data) {
                        if (!data.success) return;

                        if (data.my_entry) {
                            var displayScore = Math.max(Number(data.my_entry.score), wmHighestScore);
                            if (myScoreText) myScoreText.textContent = Number(displayScore).toLocaleString();
                            if (myRankText)  myRankText.textContent  = '#' + data.my_entry.rank;
                        }

                        renderLeaderboardRows(data.top, data.my_entry);
                    })
                    .catch(function(e){
                        if (list) list.innerHTML = '<div class="wm-lb-empty">Unable to load leaderboard.</div>';
                    });
            };

            function renderLeaderboardRows(rows, myEntry) {
                var list = document.getElementById('wm-lb-list');
                if (!list) return;

                if (!rows || rows.length === 0) {
                    list.innerHTML = '<div class="wm-lb-empty">No scores yet. Play to be the first!</div>';
                    return;
                }

                var html = '';
                rows.forEach(function(row) {
                    var rankClass = '';
                    var medalIcon = '#' + row.rank;
                    if (row.rank === 1) { rankClass = 'gold';   medalIcon = '🥇'; }
                    else if (row.rank === 2) { rankClass = 'silver'; medalIcon = '🥈'; }
                    else if (row.rank === 3) { rankClass = 'bronze'; medalIcon = '🥉'; }

                    var isMe = false;
                    if (WM_IS_AUTH && row.user_id) {
                        isMe = myEntry && myEntry.rank === row.rank && myEntry.score === row.score;
                    } else if (!WM_IS_AUTH && row.guest_token === WM_GUEST_TOKEN) {
                        isMe = true;
                    }

                    html += '<div class="wm-lb-row' + (isMe ? ' me' : '') + '">' +
                        '<span class="wm-lb-col-rank ' + rankClass + '">' + medalIcon + '</span>' +
                        '<span class="wm-lb-col-name">' + escHtml(row.player_name) + (isMe ? '<span class="you-tag">YOU</span>' : '') + '</span>' +
                        '<span class="wm-lb-col-score">' + Number(row.score).toLocaleString() + '</span>' +
                    '</div>';
                });
                list.innerHTML = html;
            }

            function escHtml(s) {
                return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            // ─── Realtime Game Score Listener ────────────────────
            function wmPollIframeScore() {
                try {
                    var live = wmGetLiveScoreFromIframe();
                    if (live > wmHighestScore) {
                        wmHighestScore = live;
                        wmSubmitScore(live, false);
                    }
                    var iframeWin = iframe.contentWindow;
                    if (!iframeWin) return;
                    var lf = iframeWin.localforage || (iframeWin.localforage = null);
                    if (lf) {
                        lf.getItem('BestScore', function(err, val) {
                            if (!err && val !== null && val !== undefined) {
                                var s = parseInt(val, 10);
                                if (!isNaN(s) && s > wmHighestScore) {
                                    wmHighestScore = s;
                                    wmSubmitScore(s, false);
                                }
                            }
                        });
                    }
                } catch(e) {}
            }

            window.addEventListener('message', function(event) {
                if (!event.data) return;
                var d = event.data;
                if (typeof d === 'object' && (d.type === 'wm_score' || d.score !== undefined)) {
                    var s = parseInt(d.score || d.BestScore || 0, 10);
                    if (!isNaN(s) && s > 0) {
                        if (s > wmHighestScore) {
                            wmHighestScore = s;
                            wmSubmitScore(s, false);
                        }
                    }
                }
            });

            // ─── Open Watermelon Modal ───────────────────────────
            if (btn) {
                btn.onclick = function() {
                    if (modalTitle) modalTitle.innerText = "Watermelon Game";
                    modal.style.display = "flex";
                    document.body.style.overflow = 'hidden';
                    iframe.src = watermelonUrl;
                    wmHighestScore = 0;
                    wmSubmittedScore = 0;

                    // Ensure leaderboard overlay is CLOSED on startup so game has 100% full view
                    closeLeaderboard();

                    clearInterval(wmPollInterval);
                    wmPollInterval = setInterval(wmPollIframeScore, 1000);
                };
            }

            // ─── Close Modal (Go Back) ───────────────────────────
            if (closeBtn) {
                closeBtn.onclick = function() {
                    // Sync current in-game score BEFORE killing the iframe
                    wmSyncLiveScore(true);

                    clearInterval(wmPollInterval);
                    wmPollInterval = null;

                    // Tiny delay so keepalive fetch fires before DOM unload
                    setTimeout(function() {
                        modal.style.display = "none";
                        document.body.style.overflow = '';
                        iframe.src = "";
                        closeLeaderboard();
                    }, 150);
                };
            }

            // ─── Draggable Modal Logic ───────────────────────────
            dragElement(modal);

            function dragElement(elmnt) {
                var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
                var header = document.getElementById("game-modal-header");
                if (header) {
                    header.onmousedown = dragMouseDown;
                    header.addEventListener('touchstart', dragMouseDown, {passive: false});
                }

                function dragMouseDown(e) {
                    // Do not drag when clicking buttons inside header
                    if (e.target.closest('button')) return;

                    e = e || window.event;
                    var clientX, clientY;
                    if (e.type === 'touchstart') {
                         clientX = e.touches[0].clientX;
                         clientY = e.touches[0].clientY;
                    } else {
                        e.preventDefault();
                        clientX = e.clientX;
                        clientY = e.clientY;
                    }
                    pos3 = clientX;
                    pos4 = clientY;
                    if (e.type === 'touchstart') {
                        document.addEventListener('touchend', closeDragElement);
                        document.addEventListener('touchmove', elementDrag, {passive: false});
                    } else {
                        document.onmouseup = closeDragElement;
                        document.onmousemove = elementDrag;
                    }
                }

                function elementDrag(e) {
                    e = e || window.event;
                    if (e.preventDefault) e.preventDefault();
                    var clientX, clientY;
                    if (e.type === 'touchmove') {
                         clientX = e.touches[0].clientX;
                         clientY = e.touches[0].clientY;
                    } else {
                        clientX = e.clientX;
                        clientY = e.clientY;
                    }
                    pos1 = pos3 - clientX;
                    pos2 = pos4 - clientY;
                    pos3 = clientX;
                    pos4 = clientY;
                    elmnt.style.top  = (elmnt.offsetTop  - pos2) + "px";
                    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
                    elmnt.style.transform = "none";
                }

                function closeDragElement() {
                    document.onmouseup = null;
                    document.onmousemove = null;
                    document.removeEventListener('touchend', closeDragElement);
                    document.removeEventListener('touchmove', elementDrag);
                }
            }
        });
    </script>


@endsection
