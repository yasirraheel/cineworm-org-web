<style>
    /* Basic styling for the container */
    .video-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        position: relative;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
        background-color: #000;
    }

    /* Beautiful video styling */
    video {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 10px;
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
    <video id="videoPlayer" controls autoplay preload="metadata" poster="{{ url($movies_info->video_image_thumb) }}">
        <source src="{{ $movies_info->video_url }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<script>
$(document).ready(function() {
    var video = document.getElementById('videoPlayer');
    if (video) {
        video.muted = false;
        video.volume = 1;
        video.play().catch(function(e) {
            console.log('Autoplay prevented:', e);
        });
    }
});
</script>
