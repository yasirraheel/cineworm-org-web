<link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
<script type="text/javascript" src="{{ URL::asset('site_assets/player/java/' . $FWDEVPlayer) }}"></script>
<!-- Include FontAwesome for the donate icon -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="slider-area">
    <div class="container">
        <div class="row align-items-stretch">
            @if (request()->getHost() != 'home.cineworm.org')
                <!-- Left Side Buttons -->
                <div class="col-md-2 d-none d-md-flex justify-content-center align-items-center pe-md-5">
                    <div class="d-flex flex-column w-100">
                        @php
                            $buttons = get_web_button_banner('buttons'); // Fetch all button components
                            $banners = get_web_button_banner('banners'); // Fetch all banner components
                        @endphp

                        @if ($buttons->isNotEmpty())
                            @foreach ($buttons as $button)
                                <a href="{{ $button->link ?? '#' }}" class="btn btn-primary w-100 mb-4"
                                    style="padding: 8px; font-size: 18px; font-weight: bold; border-radius: 8px;
                                       box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                                       background-color: #{{ $button->color ? $button->color : '007bff' }};
                                       font-family: 'Perpetua', serif; text-align: center;">
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
            <div class="col-md-7 px-md-5">
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
                <div class="col-md-3 d-none d-md-flex flex-column justify-content-between ps-md-5">
                    @if ($banners->isNotEmpty())
                        @foreach ($banners as $banner)
                            <div class="mb-4 flex-grow-1">
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
                <div class="btn-group" role="group" aria-label="Action Buttons">
                    @if ($movies_info->funding_url)
                        <a href="{{ $movies_info->funding_url }}" target="_blank" class="btn btn-primary btn-custom">
                            <i class="fas fa-donate"></i> Fund/Donate
                        </a>
                    @endif

                    @if ($movies_info->webpage_url)
                        <a href="{{ $movies_info->webpage_url }}" target="_blank" class="btn btn-primary btn-custom">
                            <i class="fas fa-globe"></i> Webpage
                        </a>
                    @endif


                </div>
                @auth
                    <form
                        action="{{ $user_has_liked ? route('movie-videos.unlike', $movies_info->id) : route('movie-videos.like', $movies_info->id) }}"
                        method="POST" style="display:inline;">
                        @csrf
                        <button type="submit"
                            class="btn btn-sm {{ $user_has_liked ? 'btn-success' : 'btn-primary' }} btn-custom">
                            <i class="fas fa-heart"></i>
                            <span class="like-text">{{ $user_has_liked ? 'Unlike' : 'Like' }}
                                ({{ $movies_info->likes }})
                            </span>
                        </button>
                    </form>
                @endauth
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
            .btn-custom {
                height: 25px;
                /* Adjust height as needed */
                line-height: 25px;
                /* Center text vertically */
                min-width: 80px;
                /* Adjust width as needed */
                text-align: center;
                /* Center text horizontally */
                display: inline-flex;
                justify-content: center;
                align-items: center;
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
                            if (button.hasClass('btn-primary')) {
                                button.removeClass('btn-primary').addClass('btn-success');
                                likeText.text('Unlike (' + (parseInt(likeText.text().match(/\d+/)[
                                    0]) + 1) + ')');
                            } else {
                                button.removeClass('btn-success').addClass('btn-primary');
                                likeText.text('Like (' + (parseInt(likeText.text().match(/\d+/)[
                                    0]) - 1) + ')');
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
