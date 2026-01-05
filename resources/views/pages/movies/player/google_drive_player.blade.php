<style>
    /* Basic styling for the container */
    .video-container {
        width: 100%;
        max-width: 100%;
        margin: 0;
        position: relative;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 0; /* Remove radius to match edge-to-edge */
        overflow: hidden;
        background-color: #000;
    }

    /* Beautiful video styling */
    video {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 0; /* Remove radius */
    }

    /* Hide the three-dot menu button */
    video::-webkit-media-controls-enclosure {
        overflow: hidden !important;
    }

    video::-webkit-media-controls-panel {
        overflow: hidden !important;
    }

    video::-webkit-media-controls-download-button {
        display: none !important;
    }

    video::-webkit-media-controls-playback-rate-button {
        display: none !important;
    }

    /* Responsive design for mobile */
    @media (max-width: 768px) {
        .video-container {
            width: 100%;
            max-width: 100%;
        }
    }
</style>

<div class="video-container">
    <!-- Video player with dynamic URL from movie_details->video_url -->
    <video id="videoPlayer" controls autoplay preload="metadata" poster={{ url($movies_info->video_image_thumb) }}>
        <source src="{{ $movies_info->video_url }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
<div
    style="text-align: center; padding: 5px 2px; font-size: 24px; font-weight: 700; background: #101011; border-radius: 10px; margin-top: 3px; line-height: 2;">
    <div class="row justify-content-center align-items-center mb-3">
        <div class="col-12">
            @if (isset($movies_info->video_slug) && isset($movies_info->id) && isset($movies_info->video_title))
                <a
                    href="{{ url('movies/details', ['slug' => $movies_info->video_slug, 'id' => $movies_info->id]) }}">
                    <p>{{ $movies_info->video_title }}</p>
                </a>
            @else
                <p>{{ 'Movie details are not available' }}</p>
            @endif
        </div>
    </div>

    <style>
        .btn-custom {
            height: 40px;
            /* Height of buttons */
            padding: 0 20px;
            /* Padding for horizontal space */
            line-height: 40px;
            /* Vertically center text */
            min-width: 100px;
            /* Ensure buttons have consistent width */
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
            /* Smooth corners */
            display: inline-flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .btn-primary.btn-custom {
            background-color: #007bff;
            border: none;
        }

        .btn-success.btn-custom {
            background-color: #28a745;
            border: none;
        }

        .btn-primary.btn-custom:hover,
        .btn-success.btn-custom:hover {
            background-color: #0056b3;
        }

        .btn-custom i {
            margin-right: 5px;
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
                        var button = form.find('button');
                        var likeText = button.find('.like-text');
                        var count = parseInt(likeText.text().match(/\d+/)[0]);

                        if (button.hasClass('btn-primary')) {
                            button.removeClass('btn-primary').addClass('btn-success');
                            likeText.text('Unlike (' + (count + 1) + ')');
                        } else {
                            button.removeClass('btn-success').addClass('btn-primary');
                            likeText.text('Like (' + (count - 1) + ')');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
</div>
