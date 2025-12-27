@extends('site_app')


@if($sports_info->seo_title)
  @section('head_title', stripslashes($sports_info->seo_title).' | '.getcong('site_name'))
@else
  @section('head_title', stripslashes($sports_info->video_title).' | '.getcong('site_name') )
@endif

@if($sports_info->seo_description)
  @section('head_description', stripslashes($sports_info->seo_description))
@else
  @section('head_description', Str::limit(stripslashes($sports_info->video_description),160))
@endif

@if($sports_info->seo_keyword)
  @section('head_keywords', stripslashes($sports_info->seo_keyword))
@endif


@section('head_image', URL::to('/'.$sports_info->video_image) )

@section('head_url', Request::url())

@section('content')

<!-- Banner -->
@if(get_web_banner('details_top')!="")
<div class="vid-item-ptb banner_ads_item">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				{!!stripslashes(get_web_banner('details_top'))!!}
			</div>
		</div>
	</div>
</div>
@endif

<link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
<script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>

<style>
    .news-ticker-container {
        background: #1a1a1a;
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
            height: 300px;
            margin-top: 15px;
        }
    }
</style>

<!-- Start Page Content Area -->
<div class="page-content-area vfx-item-ptb pt-0">

  <div class="container-fluid bg-dark video-player-base">
    <div class="container">
        <div class="row align-items-stretch">
            <!-- Left Side Buttons -->
            <div class="col-md-2 d-none d-md-flex justify-content-center align-items-center pe-md-5">
                <div class="d-flex flex-column w-100">
                    @php
                        $buttons = get_web_button_banner('buttons');
                        $banners = get_web_button_banner('banners');
                    @endphp

                    @if ($buttons->isNotEmpty())
                        @foreach ($buttons as $button)
                            <a href="{{ $button->link ?? '#' }}" class="btn btn-primary w-100 mb-4"
                                style="padding: 6px; font-size: 14px; font-weight: bold; border-radius: 8px;
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
                    @endif
                </div>
            </div>

            <!-- Video Player -->
            <div class="col-md-7 px-md-5">
                <div class="video-posts-video">
                    @if($sports_info->video_url!="")
                        @if($sports_info->video_type=="Embed")
                            @include("pages.sports.player.embed")
                        @elseif($sports_info->video_type=="Local")
                            @include("pages.sports.player.local")
                        @elseif($sports_info->video_type=="URL")
                            @include("pages.sports.player.url")
                        @else
                            @include("pages.sports.player.other")
                        @endif
                    @else
                        <div style="text-align: center;padding: 70px 30px;font-size: 24px; font-weight: 700; background: #101011;border-radius: 10px;margin-top: 15px;min-height: 280px;
                        line-height: 6;">NO Source URL Set</div>
                    @endif
                </div>
            </div>

            <!-- Right Side News Ticker and Banners -->
            <div class="col-md-3 d-none d-md-flex flex-column justify-content-start ps-md-5">

                @include('pages.home.news_ticker')

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
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <!-- Start Video Post -->
        <div class="video-post-wrapper">
        <div class="row mt-30">

          <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="video-posts-data mt-0 mb-0">
            <div class="video-post-info">
            <h2>{{stripslashes($sports_info->video_title)}}</h2>
            <div class="video-post-date">
                <span class="video-posts-author"><i class="fa fa-eye"></i>{{number_format_short($sports_info->views)}} {{trans('words.video_views')}}</span>
                @if($sports_info->date)
                  <span class="video-posts-author"><i class="fa fa-calendar-alt"></i>{{ isset($sports_info->date) ? date('M d Y',$sports_info->date) : null }}</span>
                @endif

                @if($sports_info->duration)
                 <span class="video-posts-author"><i class="fa fa-clock"></i>{{$sports_info->duration}}</span>
                @endif

            </div>
        <ul class="actor-video-link">
          <li><a href="{{ URL::to('sports/?cat_id='.$sports_info->sports_cat_id) }}" title="{{App\SportsCategory::getSportsCategoryInfo($sports_info->sports_cat_id,'category_name')}}">{{App\SportsCategory::getSportsCategoryInfo($sports_info->sports_cat_id,'category_name')}}</a></li>
        </ul>
        <div class="video-watch-share-item">
          @if(Auth::check())

             @if(check_watchlist(Auth::user()->id,$sports_info->id,'Sports'))
              <span class="btn-watchlist"><a href="{{URL::to('watchlist/remove')}}?post_id={{$sports_info->id}}&post_type=Sports" title="watchlist"><i class="fa fa-check"></i>{{trans('words.remove_from_watchlist')}}</a></span>
             @else
              <span class="btn-watchlist"><a href="{{URL::to('watchlist/add')}}?post_id={{$sports_info->id}}&post_type=Sports" title="watchlist"><i class="fa fa-plus"></i>{{trans('words.add_to_watchlist')}}</a></span>
             @endif
          @else
             <span class="btn-watchlist"><a href="{{URL::to('watchlist/add')}}?post_id={{$sports_info->id}}&post_type=Sports" title="watchlist"><i class="fa fa-plus"></i>{{trans('words.add_to_watchlist')}}</a></span>
          @endif
          <span class="btn-share"><a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#social-media"><i class="fas fa-share-alt mr-5"></i>{{trans('words.share_text')}}</a></span>

          <!-- Start Social Media Icon Popup -->
          <div id="social-media" class="modal fade centered-modal in" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content bg-dark-2 text-light">
              <div class="modal-header">
              <h4 class="modal-title text-white">{{trans('words.share_text')}}</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
              <div class="social-media-modal">
                <ul>
                  <li><a title="Sharing" href="https://www.facebook.com/sharer/sharer.php?u={{share_url_get('sports',$sports_info->video_slug,$sports_info->id)}}" class="facebook-icon" target="_blank"><i class="ion-social-facebook"></i></a></li>
                  <li><a title="Sharing" href="https://twitter.com/intent/tweet?text={{$sports_info->video_title}}&amp;url={{share_url_get('sports',$sports_info->video_slug,$sports_info->id)}}" class="twitter-icon" target="_blank"><i class="ion-social-twitter"></i></a></li>
                  <li><a title="Sharing" href="https://www.instagram.com/?url={{share_url_get('sports',$sports_info->video_slug,$sports_info->id)}}" class="instagram-icon" target="_blank"><i class="ion-social-instagram"></i></a></li>
                   <li><a title="Sharing" href="https://wa.me?text={{share_url_get('sports',$sports_info->video_slug,$sports_info->id)}}" class="whatsapp-icon" target="_blank"><i class="ion-social-whatsapp"></i></a></li>
                </ul>
              </div>
              </div>
            </div>
            </div>
          </div>
          <!-- End Social Media Icon Popup -->

        </div>
      </div>
          </div>
        </div>

          </div>
          <div class="vfx-tabs-item mt-30">
        <input checked="checked" id="tab1" type="radio" name="pct" />
        <input id="tab2" type="radio" name="pct" />
        <input id="tab3" type="radio" name="pct" />
        <nav>
        <ul>
          <li class="tab1">
          <label for="tab1">{{trans('words.description')}}</label>
          </li>
        </ul>
        </nav>
        <section class="tabs_item_block">
        <div class="tab1">
          <div class="description-detail-item">

            <p>{!!stripslashes($sports_info->video_description)!!}</p>

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
          <h3>{{trans('words.you_may_like')}}</h3>
        </div>
        <div class="tv-season-video-carousel owl-carousel">

           @foreach($related_sports_list as $sports_data)
          <div class="single-video">
          <a href="{{ URL::to('sports/details/'.$sports_data->video_slug.'/'.$sports_data->id) }}" title="{{stripslashes($sports_data->video_title)}}">
             <div class="video-img">

              @if($sports_data->video_access =="Paid")
              <div class="vid-lab-premium">
                <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}" alt="ic-premium" title="ic-premium">
              </div>
              @endif

              <span class="video-item-content">{{stripslashes($sports_data->video_title)}}</span>
              <img src="{{URL::to('/'.$sports_data->video_image)}}" alt="{{stripslashes($sports_data->video_title)}}" title="{{stripslashes($sports_data->video_title)}}">
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
@if(get_web_banner('details_bottom')!="")
<div class="vid-item-ptb banner_ads_item pb-3">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				{!!stripslashes(get_web_banner('details_bottom'))!!}
			</div>
		</div>
	</div>
</div>
@endif


 <script type="text/javascript">

    @if(Session::has('flash_message'))

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
