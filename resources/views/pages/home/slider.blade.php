<link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
<script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>
<!-- Include FontAwesome for the donate icon -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="slider-area p-0">
    <div class="container-fluid px-0">
        <div class="row g-2 align-items-stretch">
            @if (request()->getHost() != 'home.cineworm.org')
                <!-- Left Side Buttons -->
                <div class="col-md-2 col-lg-2 col-xl-1 d-none d-md-flex justify-content-center align-items-center">
                    <div class="d-flex flex-column w-100">
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
                        @else
                            <p>No buttons available.</p>
                        @endif
                    </div>
                </div>
            @endif
            <!-- Video Player -->
            <div class="col-md-8 col-lg-8 col-xl-10">
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
                <!-- Right Side Banners -->
                <div class="col-md-2 col-lg-2 col-xl-1 d-none d-md-flex flex-column justify-content-between">
                    @if ($banners->isNotEmpty())
                        @foreach ($banners as $banner)
                            <div class="mb-2 flex-grow-1">
                                <a href="{{ $banner->link ?? '#' }}" target="_blank">
                                    <img src="{{ url($banner->image) }}" alt="Ad Image"
                                        class="img-fluid mx-auto d-block h-100"
                                        style="object-fit: cover; border-radius: 5px; width: 100%; max-height: 350px;">
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    </div>


    <div
        style="text-align: center; padding: 5px 2px; font-size: 24px; font-weight: 700; background: #101011; border-radius: 10px; margin-top: 3px; line-height: 2;">
        @if(request()->getHost() != 'home.cineworm.org')
        <div class="row justify-content-center align-items-center mb-3">
            <div class="col-3">
                <a href="{{ url('movies/details', ['slug' => $movies_info->video_slug, 'id' => $movies_info->id]) }}">
                    <p>{{ $movies_info->video_title }}</p>
                </a>
            </div>
            <div class="col-9 text-right">
                <div class="action-buttons-group" role="group" aria-label="Action Buttons">
                    @if ($movies_info->funding_url)
                        <a href="{{ $movies_info->funding_url }}" target="_blank" class="action-btn donate-btn">
                            <i class="fas fa-donate"></i> Fund/Donate
                        </a>
                    @endif

                    @if ($movies_info->webpage_url)
                        <a href="{{ $movies_info->webpage_url }}" target="_blank" class="action-btn webpage-btn">
                            <i class="fas fa-globe"></i> Webpage
                        </a>
                    @endif

                    @auth
                        <form
                            action="{{ $user_has_liked ? route('movie-videos.unlike', $movies_info->id) : route('movie-videos.like', $movies_info->id) }}"
                            method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="action-btn like-btn {{ $user_has_liked ? 'liked' : '' }}">
                                <i class="fas fa-heart"></i>
                                <span class="like-text">{{ $user_has_liked ? 'Unlike' : 'Like' }}
                                    ({{ $movies_info->likes }})
                                </span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
            <div class="video-post-date">
                <span class="video-posts-author"><i
                        class="fa fa-eye"></i>{{ number_format_short($movies_info->views) }}
                    {{ trans('words.video_views') }}</span>
                @if ($movies_info->release_date)
                    <span class="video-posts-author"><i
                            class="fa fa-calendar-alt"></i>{{ isset($movies_info->release_date) ? date('M d Y', $movies_info->release_date) : null }}</span>
                @endif

                @if ($movies_info->duration)
                    <span class="video-posts-author"><i class="fa fa-clock"></i>{{ $movies_info->duration }}</span>
                @endif

                @if ($movies_info->imdb_rating)
                    <span class="video-imdb-view"><img src="{{ URL::to('site_assets/images/imdb-logo.png') }}"
                            alt="imdb-logo" title="imdb-logo" />{{ $movies_info->imdb_rating }}</span>
                @endif
                <span class="btn-share"><a href="#" class="nav-link" data-bs-toggle="modal"
                        data-bs-target="#social-media"><i
                            class="fas fa-share-alt mr-5"></i>{{ trans('words.share_text') }}</a></span>
            </div>
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
            /* Sidebar Action Buttons */
            .sidebar-action-btn {
                display: block;
                padding: 12px 15px;
                font-size: 16px;
                font-weight: 700;
                text-align: center;
                text-transform: uppercase;
                color: #ffffff;
                background: linear-gradient(90deg, #ff8508, #fd0575);
                border: none;
                border-radius: 6px;
                text-decoration: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(253, 5, 117, 0.3);
            }

            .sidebar-action-btn:hover {
                background: linear-gradient(90deg, #fd0575, #ff8508);
                color: #ffffff;
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(253, 5, 117, 0.5);
            }

            /* Action Buttons Group */
            .action-buttons-group {
                display: inline-flex;
                gap: 10px;
                flex-wrap: wrap;
                align-items: center;
            }

            /* Base Action Button Style */
            .action-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 8px 16px;
                font-size: 14px;
                font-weight: 600;
                text-transform: uppercase;
                color: #ffffff;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                gap: 6px;
            }

            .action-btn i {
                font-size: 14px;
            }

            /* Donate Button */
            .donate-btn {
                background: linear-gradient(90deg, #fe8805, #ff6b00);
                box-shadow: 0 3px 8px rgba(254, 136, 5, 0.3);
            }

            .donate-btn:hover {
                background: linear-gradient(90deg, #ff6b00, #fe8805);
                transform: translateY(-2px);
                box-shadow: 0 5px 12px rgba(254, 136, 5, 0.5);
                color: #ffffff;
            }

            /* Webpage Button */
            .webpage-btn {
                background: linear-gradient(90deg, #167ac6, #0a789c);
                box-shadow: 0 3px 8px rgba(22, 122, 198, 0.3);
            }

            .webpage-btn:hover {
                background: linear-gradient(90deg, #0a789c, #167ac6);
                transform: translateY(-2px);
                box-shadow: 0 5px 12px rgba(22, 122, 198, 0.5);
                color: #ffffff;
            }

            /* Like Button */
            .like-btn {
                background: linear-gradient(90deg, #fe0278, #d10257);
                box-shadow: 0 3px 8px rgba(254, 2, 120, 0.3);
            }

            .like-btn:hover {
                background: linear-gradient(90deg, #d10257, #fe0278);
                transform: translateY(-2px);
                box-shadow: 0 5px 12px rgba(254, 2, 120, 0.5);
            }

            .like-btn.liked {
                background: linear-gradient(90deg, #118d04, #0d6b03);
                box-shadow: 0 3px 8px rgba(17, 141, 4, 0.3);
            }

            .like-btn.liked:hover {
                background: linear-gradient(90deg, #0d6b03, #118d04);
                transform: translateY(-2px);
                box-shadow: 0 5px 12px rgba(17, 141, 4, 0.5);
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .action-btn {
                    padding: 6px 12px;
                    font-size: 12px;
                }

                .sidebar-action-btn {
                    padding: 10px 12px;
                    font-size: 14px;
                }
            }
        </style>

        <script>
            $(document).ready(function() {
                $('form').on('submit', function(e) {
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
