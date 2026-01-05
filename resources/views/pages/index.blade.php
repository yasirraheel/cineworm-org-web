@extends('site_app')

@section('head_title', getcong('site_name'))
@if(request()->getHost() != 'home.cineworm.org')
@section('head_url', Request::url())
@endif
@section('content')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
    <script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>
    <!-- Include FontAwesome for the donate icon -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style>
        .news-ticker-container {
            background: #1a1a1a;
            height: 250px;
            overflow-y: auto;
            padding: 15px;
            color: #fff;
            margin-top: 0;
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
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #e50914; /* Netflix red or your brand color */
        }
        .news-details {
            font-size: 14px;
            color: #ccc;
            line-height: 1.4;
        }
        .news-time {
            font-size: 12px;
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
            background: #444;
            border-radius: 3px;
        }
        .news-ticker-container::-webkit-scrollbar-thumb:hover {
            background: #666;
        }

        /* Pacman Game Container Styling */
        .pacman-game-container {
            background: #1a1a1a;
            height: 100%;
            overflow: hidden;
            padding: 0;
            color: #fff;
        }

        .pacman-game-wrapper {
            background: #000;
            height: 250px; /* Reduced height to fit with news under player */
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
    </style>

    <div class="slider-area p-0">
        <div class="container-fluid px-0">
            <div class="row no-gutters">
                <!-- Video Player Column -->
                <div class="col-lg-9 col-md-12" style="padding: 0;">
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

                <!-- Right Sidebar -->
                <div class="col-lg-3 col-md-12 bg-dark" style="padding: 0; border-left: 1px solid #333; max-height: 100%; overflow-y: auto;">

                    <!-- News Ticker Section -->
                    <div class="card bg-dark text-white border-0 mb-2">
                        <div class="card-header p-2" id="headingNews" style="background: #111; border-bottom: 1px solid #333;">
                            <h5 class="mb-0">
                                <button class="btn btn-link text-white text-decoration-none w-100 text-left font-weight-bold" data-toggle="collapse" data-target="#collapseNews" aria-expanded="true" aria-controls="collapseNews">
                                    <i class="fa fa-newspaper-o mr-2"></i> Latest News
                                </button>
                            </h5>
                        </div>

                        <div id="collapseNews" class="collapse show" aria-labelledby="headingNews">
                            <div class="card-body p-0">
                                <div class="news-ticker-container" style="height: 250px; border: none; background: transparent;">
                                    @if(isset($news_tickers) && count($news_tickers) > 0)
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
                                    @elseif(isset($rss_news) && count($rss_news) > 0)
                                        @foreach($rss_news as $news)
                                            <div class="news-item">
                                                <div class="news-headline">
                                                    <a href="{{ $news['link'] }}" target="_blank" style="color: #e50914; text-decoration: none;">
                                                        {{ $news['headline'] }}
                                                    </a>
                                                </div>
                                                <div class="news-details">
                                                    {!! \Illuminate\Support\Str::limit(strip_tags($news['details']), 150) !!}
                                                </div>
                                                <span class="news-time">
                                                    <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news['created_at'])->diffForHumans() }}
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <p style="color: #888; padding: 15px;">No news updates at the moment.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Game Section -->
                    <div class="card bg-dark text-white border-0">
                        <div class="card-header p-2" id="headingGame" style="background: #111; border-bottom: 1px solid #333; border-top: 1px solid #333;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed text-white text-decoration-none w-100 text-left font-weight-bold" data-toggle="collapse" data-target="#collapseGame" aria-expanded="false" aria-controls="collapseGame">
                                    <i class="fa fa-gamepad mr-2"></i> Games
                                </button>
                            </h5>
                        </div>
                        <div id="collapseGame" class="collapse" aria-labelledby="headingGame">
                            <div class="card-body p-0">
                                <div class="pacman-game-container">
                                    <div class="pacman-game-wrapper">
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

                </div>
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



@endsection
