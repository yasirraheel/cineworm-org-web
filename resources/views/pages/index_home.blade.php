@extends('site_app_home')

@section('head_title', getcong('site_name'))

@section('head_url', Request::url())

@section('content')

    @include('pages.home.slider_home')

    {{-- <div id="player">
        <span id="fullscreen-icon" class="fwdicon fwdicon-fullscreen"></span>
    </div> --}}

    {{-- <script>
        function enterFullScreen() {
            const player = document.getElementById("player");
            const fullscreenIcon = document.getElementById("fullscreen-icon");

            if (player.requestFullscreen) {
                player.requestFullscreen();
            } else if (player.mozRequestFullScreen) { // Firefox
                player.mozRequestFullScreen();
            } else if (player.webkitRequestFullscreen) { // Chrome, Safari, Opera
                player.webkitRequestFullscreen();
            } else if (player.msRequestFullscreen) { // IE/Edge
                player.msRequestFullscreen();
            }

            // Hide the fullscreen icon after entering fullscreen mode
            if (fullscreenIcon) {
                fullscreenIcon.style.display = "none";
            }
        }

        // Delay fullscreen activation after 3 seconds
        setTimeout(() => {
            enterFullScreen();
        }, 3000); // Adjust delay as needed (3000ms = 3 seconds)

    </script> --}}

@endsection
