@php
    if(!isset($news_tickers)){
        $news_tickers = \App\Models\NewsTicker::where('status', 1)->orderBy('id', 'DESC')->get();
    }
@endphp

@extends('site_app')

@if ($movies_info->seo_title)
    @section('head_title', stripslashes($movies_info->seo_title) . ' | ' . getcong('site_name'))
@else
    @section('head_title', stripslashes($movies_info->video_title) . ' | ' . getcong('site_name'))
@endif

@if ($movies_info->seo_description)
    @section('head_description', stripslashes($movies_info->seo_description))
@else
    @section('head_description', Str::limit(stripslashes($movies_info->video_description), 160))
@endif

@if ($movies_info->seo_keyword)
    @section('head_keywords', stripslashes($movies_info->seo_keyword))
@endif


@section('head_image', URL::to('/' . $movies_info->video_image))

@section('head_url', Request::url())

@section('content')




    <link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
    <script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>

    <style>
        .news-ticker-container {
                max-height: 250px;
                overflow-y: auto;
                padding: 10px;
                background: #111;
                color: #fff;
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
        .news-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
        }
        .news-item:last-child {
            border-bottom: none;
        }
        .news-headline {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #e50914;
        }
        .news-details {
            font-size: 13px;
            color: #ccc;
            line-height: 1.4;
        }
        .news-time {
            font-size: 11px;
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

        /* Pacman Game Container Styling */
        .pacman-game-container {
            background: #1a1a1a;
            flex: 1;
            overflow: hidden;
            padding: 0;
            color: #fff;
        }

        .pacman-game-wrapper {
            background: #000;
            height: 100%;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .pacman-game-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        @media (max-width: 767px) {
            .news-ticker-container {
                height: 300px;
                margin-top: 15px;
            }
            .pacman-game-wrapper {
                height: 300px;
            }
        }
    </style>

    <!-- Start Page Content Area -->
    <div class="page-content-area vfx-item-ptb pt-0">

        <div class="container-fluid px-0 bg-dark video-player-base">
            <div class="row no-gutters align-items-stretch">
                <!-- Video Player -->
                <div class="col-md-9 p-0">
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
                </div>

                <!-- Right Side News Ticker and Banners -->
                <div class="col-md-3 d-none d-md-flex flex-column justify-content-start" style="max-height: 100%;">
                    @php
                        $buttons = get_web_button_banner('buttons'); // Fetch all button components
                        $banners = get_web_button_banner('banners'); // Fetch all banner components
                    @endphp

                    @if ($buttons->isNotEmpty())
                        <div class="sidebar-buttons-container mb-3 px-2 pt-2">
                            @foreach ($buttons as $button)
                                <a href="{{ $button->link ?? '#' }}" class="btn btn-primary w-100 mb-2"
                                    style="padding: 6px; font-size: 14px; font-weight: bold; border-radius: 8px;
                                           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                                           background-color: #{{ $button->color ? $button->color : '007bff' }};
                                           font-family: 'Perpetua', serif; text-align: center;
                                           text-decoration: none; color: #fff;
                                           position: relative;
                                           display: inline-block;
                                           overflow: hidden;
                                           border: 3px solid #00008B; /* Dark blue border before hover */
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

                    <!-- News Ticker Section -->
                <div class="card bg-dark text-white border-0 mb-2" style="max-height: 350px; display: flex; flex-direction: column;">
                    <div class="card-header p-2" id="headingNews" style="background: #111; border-bottom: 1px solid #333; flex-shrink: 0;">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white text-decoration-none w-100 text-left font-weight-bold" type="button" onclick="toggleSection('news')">
                                <i class="fa fa-newspaper-o mr-2"></i> Latest News
                            </button>
                        </h5>
                    </div>

                    <div id="collapseNews" class="collapse show" style="overflow: hidden; flex-grow: 1; transition: height 0.3s ease;">
                        <div class="card-body p-0" style="height: 100%; display: flex; flex-direction: column;">
                            <div class="news-ticker-container" style="flex-grow: 1; overflow-y: auto; position: relative;">
                                <div id="news-list-view">
                                    @php
                                        $has_news = false;
                                    @endphp

                                    @if(isset($news_tickers) && count($news_tickers) > 0)
                                        @php $has_news = true; @endphp
                                        @foreach($news_tickers as $news)
                                            <div class="news-item">
                                                <div class="news-headline">
                                                    @if($news->is_breaking)
                                                        <span class="breaking-badge">BREAKING</span>
                                                    @endif
                                                    {{ $news->headline }}
                                                </div>
                                                <div class="news-details">
                                                    {!! \Illuminate\Support\Str::limit(strip_tags($news->details), 150) !!}
                                                </div>
                                                <span class="news-time">
                                                    <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news->created_at)->diffForHumans() }}
                                                </span>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if(isset($rss_news) && count($rss_news) > 0)
                                        @php $has_news = true; @endphp
                                        @foreach($rss_news as $news)
                                            <div class="news-item">
                                                <div class="news-headline">
                                                    <span class="breaking-badge" style="background: #007bff;">WORLD NEWS</span>
                                                    {{ $news['headline'] }}
                                                </div>
                                                <div class="news-details">
                                                    {!! \Illuminate\Support\Str::limit(strip_tags($news['details']), 150) !!}
                                                </div>
                                                <span class="news-time">
                                                    <i class="fa fa-clock-o"></i> {{ $news['created_at'] }}
                                                </span>
                                                <a href="#" class="read-dw-news" data-link="{{ $news['link'] }}" style="display: block; font-size: 11px; color: #fe8805; margin-top: 5px;">Read Full Story</a>
                                            </div>
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

                <!-- Game Section -->
                <div class="card bg-dark text-white border-0 mb-2" style="flex-shrink: 0;">
                    <div class="card-header p-2" id="headingGame" style="background: #111; border-bottom: 1px solid #333; border-top: 1px solid #333;">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-white text-decoration-none w-100 text-left font-weight-bold" type="button" onclick="toggleSection('game')">
                                <i class="fa fa-gamepad mr-2"></i> Games
                            </button>
                        </h5>
                    </div>
                    <div id="collapseGame" class="collapse" style="overflow: hidden; transition: height 0.3s ease;">
                        <div class="card-body p-0">
                            <div class="pacman-game-container">
                                <div class="pacman-game-wrapper" style="height: 200px;">
                                    <iframe
                                        src="https://pacman.platzh1rsch.ch/"
                                        style="width: 100%; height: 100%; border: none;"
                                        allowfullscreen
                                        title="Pacman Game">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function toggleSection(section, event) {
                        if (event) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        const newsCollapse = document.getElementById('collapseNews');
                        const gameCollapse = document.getElementById('collapseGame');

                        if (section === 'news') {
                            if (!newsCollapse.classList.contains('show')) {
                                // Open News, Close Game
                                $(gameCollapse).collapse('hide');
                                $(newsCollapse).collapse('show');
                            }
                        } else if (section === 'game') {
                            if (!gameCollapse.classList.contains('show')) {
                                // Open Game, Close News
                                $(newsCollapse).collapse('hide');
                                $(gameCollapse).collapse('show');
                            }
                        }
                    }
                </script>

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

            <!-- Player Footer Section -->
            <div class="player-footer-section">
                <!-- Title and Action Buttons Row -->
                <div class="player-footer-top">
                    <div class="video-title-section">
                        <a href="{{ url('movies/details', ['slug' => $movies_info->video_slug, 'id' => $movies_info->id]) }}" class="video-title-link">
                            <h3 class="video-title">{{ $movies_info->video_title }}</h3>
                        </a>
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

                        <button class="action-btn share-btn" data-bs-toggle="modal" data-bs-target="#social-media">
                            <i class="fas fa-share-alt"></i>
                            <span>{{ trans('words.share_text') }}</span>
                        </button>

                        <a href="{{ $random_movie ? url('movies/'.$random_movie->video_slug.'/'.$random_movie->id) : URL::to('/') }}" class="action-btn next-btn" id="footer-next-btn">
                            <i class="fas fa-step-forward"></i>
                            <span>Next</span>
                        </a>
                    </div>
                </div>

                <!-- Video Info Meta Row -->
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
            </div>

            <style>
            /* Player Footer Section */
            .player-footer-section {
                background: linear-gradient(135deg, #0d0620 0%, #1a0d33 100%);
                border-radius: 0;
                margin-top: 0;
                padding: 20px 25px;
                box-shadow: 0 2px 15px rgba(0, 0, 0, 0.4);
                width: 100%;
            }

            /* Player Footer Top Section */
            .player-footer-top {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            /* Video Title Section */
            .video-title-section {
                flex: 1;
                min-width: 0;
            }

            .video-title-link {
                text-decoration: none;
            }

            .video-title {
                font-size: 22px;
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
                gap: 25px;
                flex-wrap: wrap;
                align-items: center;
            }

            .meta-item {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #b5b5b5;
                font-size: 14px;
                font-weight: 500;
            }

            .meta-item i {
                font-size: 16px;
                color: #fe8805;
            }

            .meta-item.imdb-rating {
                background: rgba(245, 197, 24, 0.1);
                padding: 5px 12px;
                border-radius: 4px;
            }

            .imdb-logo {
                width: 35px;
                height: auto;
                vertical-align: middle;
            }

            .meta-item.imdb-rating span {
                color: #f5c518;
                font-weight: 700;
                font-size: 15px;
            }

            /* Responsive adjustments */
            @media (max-width: 992px) {
                .player-footer-top {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .action-buttons-section {
                    width: 100%;
                    justify-content: flex-start;
                }

                .video-title {
                    font-size: 20px;
                }
            }

            @media (max-width: 768px) {
                .player-footer-section {
                    padding: 15px 18px;
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
                    font-size: 18px;
                    white-space: normal;
                }

                .player-footer-meta {
                    gap: 15px;
                }

                .meta-item {
                    font-size: 13px;
                }
            }
            </style>
        </div>
            <!-- Banner -->
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

    </div>

        <div class="vfx-tabs-item mt-30">
                        <input checked="checked" id="tab1" type="radio" name="pct" />
                        <input id="tab2" type="radio" name="pct" />
                        <input id="tab3" type="radio" name="pct" />
                        <nav>
                            <ul>
                                <li class="tab1">
                                    <label for="tab1">{{ trans('words.description') }}</label>
                                </li>
                                <li class="tab2">
                                    <label for="tab2">{{ trans('words.actors') }}</label>
                                </li>
                                <li class="tab3">
                                    <label for="tab3">{{ trans('words.directors') }}</label>
                                </li>
                            </ul>
                        </nav>
                        <section class="tabs_item_block">
                            <div class="tab1">
                                <div class="description-detail-item">

                                    <p>{!! stripslashes($movies_info->video_description) !!}</p>

                                </div>
                            </div>
                            <div class="tab2">
                                <div class="row">
                                    @foreach (explode(',', $movies_info->actor_id) as $i => $actor_ids)
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 col-6">
                                            <div class="actors-member-item">
                                                <a href="{{ URL::to('actors/' . App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_slug')) }}/{{ $actor_ids }}"
                                                    title="actors details">
                                                    @if (App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_image'))
                                                        <img src="{{ URL::to('/' . App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_image')) }}"
                                                            alt="{{ App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_name') }}"
                                                            title="{{ App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_name') }}">
                                                    @else
                                                        <img src="{{ URL::to('images/user_icon.png') }}"
                                                            alt="{{ App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_name') }}"
                                                            title="{{ App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_name') }}">
                                                    @endif


                                                    <span>{{ App\ActorDirector::getActorDirectorInfo($actor_ids, 'ad_name') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                            <div class="tab3">

                                <div class="row">
                                    @foreach (explode(',', $movies_info->director_id) as $i => $director_ids)
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 col-6">
                                            <div class="actors-member-item">
                                                <a href="{{ URL::to('directors/' . App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_slug')) }}/{{ $director_ids }}"
                                                    title="directors details">
                                                    @if (App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_image'))
                                                        <img src="{{ URL::to('/' . App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_image')) }}"
                                                            alt="{{ App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_name') }}"
                                                            title="{{ App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_name') }}">
                                                    @else
                                                        <img src="{{ URL::to('images/user_icon.png') }}"
                                                            alt="{{ App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_name') }}"
                                                            title="{{ App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_name') }}">
                                                    @endif

                                                    <span>{{ App\ActorDirector::getActorDirectorInfo($director_ids, 'ad_name') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                        </section>
                    </div>
                </div>
            </div>
            <!-- Start Popular Videos -->

            <!-- Start You May Also Like Video Carousel -->
            <div class="video-carousel-area vfx-item-ptb related-video-item">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 p-0">
                            <div class="vfx-item-section">
                                <h3>{{ trans('words.you_may_like') }}</h3>
                            </div>
                            <div class="video-carousel owl-carousel">

                                @foreach ($related_movies_list as $movies_data)
                                    <div class="single-video">
                                        <a href="{{ URL::to('movies/details/' . $movies_data->video_slug . '/' . $movies_data->id) }}"
                                            title="{{ stripslashes($movies_data->video_title) }}">
                                            <div class="video-img">

                                                @if ($movies_data->video_access == 'Paid')
                                                    <div class="vid-lab-premium">
                                                        <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                            alt="ic-premium" title="ic-premium">
                                                    </div>
                                                @endif

                                                <span
                                                    class="video-item-content">{{ stripslashes($movies_data->video_title) }}</span>
                                                <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                                    alt="{{ stripslashes($movies_data->video_title) }}"
                                                    title="{{ stripslashes($movies_data->video_title) }}">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End You May Also Like Video Carousel -->
        </div>
    </div>
    </div>
    <!-- End Page Content Area -->

    <!-- Banner -->
    <!--@if (get_web_banner('details_bottom') != '')
        -->
    <!--    <div class="vid-item-ptb banner_ads_item pb-3">-->
    <!--        <div class="container-fluid">-->
    <!--            <div class="row">-->
    <!--                <div class="col-md-12">-->
    <!--                    {!! stripslashes(get_web_banner('details_bottom')) !!}-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--
        @endif-->

    <script type="text/javascript">
        @if (Session::has('flash_message'))

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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Match news ticker height with player height
            function matchTickerToPlayerHeight() {
                // Only apply on desktop (md and above)
                if (window.innerWidth >= 768) {
                    var player = document.getElementById('viavi_player');
                    var ticker = document.querySelector('.news-ticker-container');

                    if (player && ticker) {
                        var playerHeight = player.offsetHeight;
                        ticker.style.height = playerHeight + 'px';
                    }
                } else {
                    // Reset height on mobile
                    var ticker = document.querySelector('.news-ticker-container');
                    if (ticker) {
                        ticker.style.height = '';
                    }
                }
            }

            // Call on load, resize, and periodically to catch player initialization
            window.addEventListener('resize', matchTickerToPlayerHeight);
            window.addEventListener('load', matchTickerToPlayerHeight);

            // Check periodically for the first few seconds after page load (player takes time to initialize)
            var attempts = 0;
            var checkInterval = setInterval(function() {
                matchTickerToPlayerHeight();
                attempts++;
                if (attempts > 20) { // Stop after ~10 seconds
                    clearInterval(checkInterval);
                }
            }, 500);

            // Auto-scroll news ticker
            function autoScrollTicker() {
                var ticker = document.querySelector('.news-ticker-container');
                if (!ticker) return;

                var scrollSpeed = 1; // pixels per interval
                var scrollInterval = 50; // milliseconds
                var pauseAtEnd = 2000; // pause at end before restarting (ms)
                var pauseAtTop = 3000; // pause at top before starting (ms)
                var isScrolling = false;
                var hasStarted = false;

                // Pause at top before starting
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

                        // Check if we've reached the bottom
                        if (ticker.scrollTop + ticker.clientHeight >= ticker.scrollHeight - 5) {
                            clearInterval(scrollTimer);
                            isScrolling = false;

                            // Pause at bottom, then scroll back to top
                            setTimeout(function() {
                                ticker.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });

                                // Wait for smooth scroll to complete, then restart
                                setTimeout(function() {
                                    startScrolling();
                                }, pauseAtTop);
                            }, pauseAtEnd);
                        } else {
                            // Scroll down smoothly
                            ticker.scrollTop += scrollSpeed;
                        }
                    }, scrollInterval);
                }

                // Pause scrolling on hover
                ticker.addEventListener('mouseenter', function() {
                    scrollSpeed = 0;
                });

                ticker.addEventListener('mouseleave', function() {
                    if (hasStarted) {
                        scrollSpeed = 1;
                    }
                });
            }

            // Initialize auto-scroll
            autoScrollTicker();
        });
    </script>

@endsection
