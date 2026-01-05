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
        height: 400px; /* Fixed height for consistency with ticker */
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

            <!-- Right Sidebar (Accordion) -->
            <div class="col-lg-3 col-md-12 bg-dark" style="padding: 0; border-left: 1px solid #333;">
                <div id="sidebarAccordion">
                    <!-- News Ticker Item -->
                    <div class="card bg-dark text-white border-0">
                        <div class="card-header p-2" id="headingNews" style="background: #111; border-bottom: 1px solid #333;">
                            <h5 class="mb-0">
                                <button class="btn btn-link text-white text-decoration-none w-100 text-left font-weight-bold" data-toggle="collapse" data-target="#collapseNews" aria-expanded="true" aria-controls="collapseNews">
                                    <i class="fa fa-newspaper-o mr-2"></i> Latest News
                                </button>
                            </h5>
                        </div>

                        <div id="collapseNews" class="collapse show" aria-labelledby="headingNews" data-parent="#sidebarAccordion">
                            <div class="card-body p-0">
                                <div class="news-ticker-container" style="height: 400px; border: none; background: transparent;">
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
                                    @else
                                        <p style="color: #888; padding: 15px;">No news updates at the moment.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Game Item -->
                    <div class="card bg-dark text-white border-0">
                        <div class="card-header p-2" id="headingGame" style="background: #111; border-bottom: 1px solid #333;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed text-white text-decoration-none w-100 text-left font-weight-bold" data-toggle="collapse" data-target="#collapseGame" aria-expanded="false" aria-controls="collapseGame">
                                    <i class="fa fa-gamepad mr-2"></i> Games
                                </button>
                            </h5>
                        </div>
                        <div id="collapseGame" class="collapse" aria-labelledby="headingGame" data-parent="#sidebarAccordion">
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
</div>
