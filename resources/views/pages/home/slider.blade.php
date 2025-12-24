<link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
<script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>
<!-- Include FontAwesome for the donate icon -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<style>
    .news-ticker-container {
        background: #1a1a1a;
        height: 100%;
        max-height: 600px; /* Limit height */
        overflow-y: auto;
        padding: 15px;
        color: #fff;
        border-radius: 5px;
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
        background: #444;
        border-radius: 3px;
    }
    .news-ticker-container::-webkit-scrollbar-thumb:hover {
        background: #666;
    }

    @media (max-width: 767px) {
        .news-ticker-container {
            height: 300px; /* Fixed height for mobile */
            margin-top: 15px;
        }
    }
</style>

<div class="slider-area p-0">
    <div class="container-fluid px-0">
        <div class="row g-3 align-items-stretch">
            @if (request()->getHost() != 'home.cineworm.org')
                <!-- Left Side Buttons -->
                <div class="col-md-2 col-lg-2 col-xl-2 d-none d-md-flex justify-content-center align-items-center">
                    <div class="d-flex flex-column w-100 sidebar-buttons-container">
                        @php
                            $buttons = get_web_button_banner('buttons'); // Fetch all button components
                            $banners = get_web_button_banner('banners'); // Fetch all banner components
                        @endphp

                        @if ($buttons->isNotEmpty())
                            @foreach ($buttons as $button)
                                <a href="{{ $button->link ?? '#' }}" class="sidebar-action-btn w-100 mb-2">
                                    {{ $button->title }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
            <!-- Video Player -->
            <div class="col-md-7 col-lg-7 col-xl-7">
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
            @if (request()->getHost() != 'home.cineworm.org')
                <!-- Right Side News Ticker -->
                <div class="col-md-3 col-lg-3 col-xl-3 d-flex flex-column justify-content-start">

                     <div class="news-ticker-container">
                        <h4 style="color: #fff; border-bottom: 2px solid #e50914; padding-bottom: 10px; margin-bottom: 15px;">
                            Latest News
                        </h4>

                        <div class="news-scroll-mask">
                            <div class="news-scroll-content @if(isset($news_tickers) && count($news_tickers) > 2) scrolling @endif">
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

                                    @if(count($news_tickers) > 2)
                                        <!-- Duplicate for smooth infinite scroll -->
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
                                @else
                                    <div class="text-white">No news available.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-banners-container mt-3">
                        @if ($banners->isNotEmpty())
                            @foreach ($banners as $banner)
                                <div class="banner-item">
                                    <a href="{{ $banner->link ?? '#' }}" target="_blank" class="banner-link">
                                        <div class="banner-wrapper">
                                            <img src="{{ url($banner->image) }}" alt="Advertisement" class="banner-image">
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
            @endif
        </div>
    </div>


    <div class="player-footer-section">
        @if(request()->getHost() != 'home.cineworm.org')
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
        @endif
        <!-- Start Social Media Icon Popup -->
        <div id="social-media" class="modal fade centered-modal in" tabindex="-1" role="dialog"
            aria-labelledby="myModal" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content bg-dark-2 text-light">
                    <div class="modal-header">
                        <h4 class="modal-title text-white">
                            {{ trans('words.share_text') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="social-media-modal">
                            <ul>
                                <li><a title="Sharing"
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ share_url_get('movies', $movies_info->video_slug, $movies_info->id) }}"
                                        class="facebook-icon" target="_blank"><i class="ion-social-facebook"></i></a>
                                </li>
                                <li><a title="Sharing"
                                        href="https://twitter.com/intent/tweet?text={{ $movies_info->video_title }}&amp;url={{ share_url_get('movies', $movies_info->video_slug, $movies_info->id) }}"
                                        class="twitter-icon" target="_blank"><i class="ion-social-twitter"></i></a></li>
                                <li><a title="Sharing"
                                        href="https://www.instagram.com/?url={{ share_url_get('movies', $movies_info->video_slug, $movies_info->id) }}"
                                        class="instagram-icon" target="_blank"><i class="ion-social-instagram"></i></a>
                                </li>
                                <li><a title="Sharing"
                                        href="https://wa.me?text={{ share_url_get('movies', $movies_info->video_slug, $movies_info->id) }}"
                                        class="whatsapp-icon" target="_blank"><i class="ion-social-whatsapp"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Social Media Icon Popup -->

        <style>
            /* Sidebar Containers */
            .sidebar-buttons-container {
                padding: 0 5px;
            }

            .sidebar-banners-container {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 0;
            }

            /* Sidebar Action Buttons */
            .sidebar-action-btn {
                display: block;
                padding: 10px 8px;
                font-size: 14px;
                font-weight: 700;
                text-align: center;
                color: #ffffff;
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 6px;
                text-decoration: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
                word-wrap: break-word;
                overflow-wrap: break-word;
                hyphens: auto;
                line-height: 1.4;
                min-height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar-action-btn:hover {
                background: linear-gradient(135deg, #16213e 0%, #1a1a2e 100%);
                border-color: rgba(22, 122, 198, 0.5);
                color: #ffffff;
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(22, 122, 198, 0.4);
            }

            /* Banner Items */
            .banner-item {
                position: relative;
                width: 100%;
                margin-bottom: 10px;
            }

            .banner-item:last-child {
                margin-bottom: 0;
            }

            .banner-link {
                display: block;
                text-decoration: none;
            }

            .banner-wrapper {
                position: relative;
                background: #0d0d0d;
                border-radius: 6px;
                padding: 5px;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.8);
                transition: all 0.3s ease;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .banner-wrapper::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(22, 122, 198, 0.05) 0%, rgba(142, 68, 173, 0.05) 100%);
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }

            .banner-wrapper:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 24px rgba(22, 122, 198, 0.3);
                border-color: rgba(22, 122, 198, 0.4);
            }

            .banner-wrapper:hover::after {
                opacity: 1;
            }

            .banner-image {
                width: 100%;
                height: auto;
                min-height: 180px;
                max-height: 350px;
                object-fit: cover;
                border-radius: 4px;
                display: block;
                transition: all 0.3s ease;
            }

            .banner-wrapper:hover .banner-image {
                opacity: 0.92;
            }

            .banner-overlay {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(22, 122, 198, 0.95);
                color: #ffffff;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: all 0.3s ease;
                font-size: 18px;
                box-shadow: 0 4px 16px rgba(22, 122, 198, 0.6);
            }

            .banner-wrapper:hover .banner-overlay {
                opacity: 1;
            }

            /* Advertisement Label */
            .banner-item::before {
                content: 'AD';
                position: absolute;
                top: 3px;
                left: 3px;
                background: rgba(0, 0, 0, 0.85);
                color: #fe8805;
                font-size: 9px;
                font-weight: 800;
                padding: 3px 7px;
                border-radius: 3px;
                z-index: 10;
                letter-spacing: 1px;
                border: 1px solid rgba(254, 136, 5, 0.3);
            }

            /* Player Footer Section */
            .player-footer-section {
                background: linear-gradient(135deg, #0d0620 0%, #1a0d33 100%);
                border-radius: 8px;
                margin-top: 8px;
                padding: 20px 25px;
                box-shadow: 0 2px 15px rgba(0, 0, 0, 0.4);
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

                .sidebar-action-btn {
                    padding: 8px 6px;
                    font-size: 13px;
                    min-height: 45px;
                }

                .banner-wrapper {
                    padding: 4px;
                }

                .banner-image {
                    max-height: 280px;
                    min-height: 140px;
                }

                .banner-overlay {
                    width: 40px;
                    height: 40px;
                    font-size: 16px;
                }
            }

            @media (max-width: 992px) {
                .banner-wrapper {
                    padding: 4px;
                }

                .banner-image {
                    max-height: 300px;
                    min-height: 160px;
                }
            }

            @media (max-width: 576px) {
                .sidebar-banners-container,
                .sidebar-buttons-container {
                    display: none !important;
                }
            }
        </style>

        <script>
            $(document).ready(function() {
                $('.like-form').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var url = form.attr('action');
                    var method = form.attr('method');
                    $.ajax({
                        type: method,
                        url: url,
                        data: form.serialize(),
                        success: function(response) {
                            // Update the like button text and count
                            var button = form.find('button');
                            var likeText = button.find('.like-text');
                            if (button.hasClass('liked')) {
                                button.removeClass('liked');
                                var currentCount = parseInt(likeText.text().match(/\d+/)[0]);
                                likeText.text('Like (' + (currentCount - 1) + ')');
                            } else {
                                button.addClass('liked');
                                var currentCount = parseInt(likeText.text().match(/\d+/)[0]);
                                likeText.text('Unlike (' + (currentCount + 1) + ')');
                            }
                        },
                        error: function(xhr) {
                            // Handle error response
                            console.log(xhr.responseText);
                        }
                    });
                });
            });
        </script>
    </div>
</div>
