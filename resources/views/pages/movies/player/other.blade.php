    <!-- Include Plyr CSS -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.6.8/plyr.css">
    <!-- Include your custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('site_assets/player/content/global.css') }}">
   <style>
        /* Forcefully setting the width and height of the player */
        #viavi_player {
            width: 100%;
            max-width: 100%;
            height: auto;
            aspect-ratio: 16 / 9;
            margin: 0;
        }
        .plyr__video-embed {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .plyr__video-embed iframe {
            position: absolute;
            width: 100%;
            height: 100%;
        }
    </style>

    <div id="viavi_player"></div>

    <!-- Include jQuery if not already included -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Include Plyr JavaScript -->
    <script src="https://cdn.plyr.io/3.6.8/plyr.polyfilled.js"></script>
    <!-- Setup FWDEVPlayer -->
    <script type="text/javascript">
        var videoUrls = [
            "{{ base64_encode($movies_info->video_url) }}",
            // "{{ base64_encode($movies_info->video_url) }}" // Add next video URL here
        ];

        var currentVideoIndex = 0;

        var currentUrl = window.location.pathname;
        @php
            $redirect_movie = isset($random_movie) ? $random_movie : $movies_info;
        @endphp
        var redirectURL = currentUrl === '/' ? "{{ url('/') }}" : "{{ url('movies/watch/' . $redirect_movie->video_slug . '/' . $redirect_movie->id) }}";

        FWDEVPUtils.onReady(function() {
             var isMobile = window.matchMedia("only screen and (max-width: 767px)").matches;
        var stickySetting = isMobile ? "yes" : "no";
         var controllerHideDelaySeconds = isMobile ? 1000 : 3;
            FWDEVPlayer.videoStartBehaviour = "play";

            var player = new FWDEVPlayer({
                // Main settings
                instanceName: "player1",
                parentId: "viavi_player",
                mainFolderPath: "{{ URL::asset('/site_assets/player/content/') }}",
                initializeOnlyWhenVisible: "no",
                skinPath: "{{ get_player_cong('player_style') }}",
                displayType: "responsive",
                autoScale: "yes",
                fillEntireVideoScreen: "no",
                playsinline: "yes",
                useWithoutVideoScreen: "no",
                openDownloadLinkOnMobile: "no",
                googleAnalyticsMeasurementId: "",
                useVectorIcons: "{{ get_player_cong('player_vector_icons') }}",
                useResumeOnPlay: "yes",
                goFullScreenOnButtonPlay: "no",
                useHEXColorsForSkin: "no",
                normalHEXButtonsColor: "#FF0000",
                privateVideoPassword: "428c841430ea18a70f7b06525d4b748a",
                startAtVideoSource: 0,
                startAtTime: "",
                stopAtTime: "",
                videoSource: [{
                    source: "encrypt:" + videoUrls[currentVideoIndex],
                    label: "",
                    isLive: "no"
                }],
                posterPath: "{{ URL::to('/' . $movies_info->video_image) }}",
                showErrorInfo: "yes",
                fillEntireScreenWithPoster: "no",
                disableDoubleClickFullscreen: "no",
                useChromeless: "no",
                showPreloader: "yes",
                preloaderColors: ["#999999", "#FFFFFF"],
                addKeyboardSupport: "yes",
                autoPlay: "yes",
                autoPlayText: "",
                loop: "no",
                scrubAtTimeAtFirstPlay: "00:00:00",
                maxWidth: 1600,
                maxHeight: 900,
                volume: 1,
                greenScreenTolerance: 200,
                backgroundColor: "#000000",
                posterBackgroundColor: "#000000",
                // Lightbox settings
                closeLightBoxWhenPlayComplete: "no",
                lightBoxBackgroundOpacity: .6,
                lightBoxBackgroundColor: "#000000",
                // Logo settings
                logoSource: "{{ get_player_cong('player_logo') ? URL::asset('/' . get_player_cong('player_logo')) : URL::asset('/' . getcong('site_logo')) }}",
                showLogo: "{{ get_player_cong('player_watermark') }}",
                hideLogoWithController: "yes",
                logoPosition: "{{ get_player_cong('player_logo_position') }}",
                logoLink: "{{ get_player_cong('player_url') }}",
                logoMargins: 5,
                // Controller settings
                showController: "yes",
                showDefaultControllerForVimeo: "yes",
                showScrubberWhenControllerIsHidden: "yes",
                showControllerWhenVideoIsStopped: "yes",
                showVolumeScrubber: "yes",
                showVolumeButton: "yes",
                showTime: "yes",
                showAudioTracksButton: "yes",
                showRewindButton: "{{ get_player_cong('rewind_forward') }}",
                showQualityButton: "yes",
                showShareButton: "no",
                showEmbedButton: "no",
                showDownloadButton: "no",
                showMainScrubberToolTipLabel: "yes",
                showChromecastButton: "yes",
                show360DegreeVideoVrButton: "no",
                showFullScreenButton: "yes",
                repeatBackground: "no",
                controllerHeight: 43,
                controllerHideDelay: controllerHideDelaySeconds,
                startSpaceBetweenButtons: 11,
                spaceBetweenButtons: 11,
                mainScrubberOffestTop: 15,
                scrubbersOffsetWidth: 2,
                timeOffsetLeftWidth: 1,
                timeOffsetRightWidth: 2,
                volumeScrubberWidth: 80,
                volumeScrubberOffsetRightWidth: 0,
                timeColor: "#bdbdbd",
                showYoutubeRelAndInfo: "no",
                youtubeQualityButtonNormalColor: "#888888",
                youtubeQualityButtonSelectedColor: "#FFFFFF",
                scrubbersToolTipLabelBackgroundColor: "#FFFFFF",
                scrubbersToolTipLabelFontColor: "#5a5a5a",
                redirectURL: redirectURL,
                // Cuepoints
                executeCuepointsOnlyOnce: "no",
                cuepoints: [],
                // Annotations
                annotiationsListId: "none",
                showAnnotationsPositionTool: "no",
                // Subtitles
                showSubtitleButton: "yes",
                subtitlesOffLabel: "Subtitle off",
                startAtSubtitle: 1,
                subtitlesSource: [
                    @if ($movies_info->subtitle_on_off)
                        @if ($movies_info->subtitle_url1)
                            {
                                subtitlePath: "{{ $movies_info->subtitle_url1 }}",
                                subtileLabel: "{{ $movies_info->subtitle_language1 ? $movies_info->subtitle_language1 : 'English' }}"
                            },
                        @endif
                        @if ($movies_info->subtitle_url2)
                            {
                                subtitlePath: "{{ $movies_info->subtitle_url2 }}",
                                subtileLabel: "{{ $movies_info->subtitle_language2 ? $movies_info->subtitle_language2 : 'English' }}"
                            },
                        @endif
                        @if ($movies_info->subtitle_url3)
                            {
                                subtitlePath: "{{ $movies_info->subtitle_url3 }}",
                                subtileLabel: "{{ $movies_info->subtitle_language3 ? $movies_info->subtitle_language3 : 'English' }}"
                            },
                        @endif
                    @endif
                ],
                // Audio visualizer
                audioVisualizerLinesColor: "#0099FF",
                audioVisualizerCircleColor: "#FFFFFF",
                // Advertisement on pause window
                aopwTitle: "Advertisement",
                aopwSource: "",
                aopwWidth: 400,
                aopwHeight: 240,
                aopwBorderSize: 6,
                aopwTitleColor: "#FFFFFF",
                // Playback rate / speed
                showPlaybackRateButton: "yes",
                defaultPlaybackRate: "1", // 0.25, 0.5, 1, 1.25, 1.5, 2
                // Sticky on scroll
                stickyOnScroll: stickySetting,
                stickyOnScrollShowOpener: "yes",
                stickyOnScrollWidth: "700",
                stickyOnScrollHeight: "394",
                // Sticky display settings
                showOpener: "yes",
                showOpenerPlayPauseButton: "yes",
                verticalPosition: "bottom",
                horizontalPosition: "center",
                showPlayerByDefault: "yes",
                animatePlayer: "yes",
                openerAlignment: "right",
                mainBackgroundImagePath: "{{ URL::asset('/site_assets/player/content/minimal_skin_dark/main-background.png') }}",
                openerEqulizerOffsetTop: -1,
                openerEqulizerOffsetLeft: 3,
                offsetX: 0,
                offsetY: 0,
                // Embed window
                embedWindowCloseButtonMargins: 15,
                borderColor: "#333333",
                mainLabelsColor: "#FFFFFF",
                secondaryLabelsColor: "#a1a1a1",
                shareAndEmbedTextColor: "#5a5a5a",
                inputBackgroundColor: "#000000",
                inputColor: "#FFFFFF",
                // A to B loop
                useAToB: "no",
                atbTimeBackgroundColor: "transparent",
                atbTimeTextColorNormal: "#FFFFFF",
                atbTimeTextColorSelected: "#0099ff",
                atbButtonTextNormalColor: "#888888",
                atbButtonTextSelectedColor: "#FFFFFF",
                atbButtonBackgroundNormalColor: "#2e2e2e",
                atbButtonBackgroundSelectedColor: "#0099FF",
                // Thumbnails preview
                thumbnailsPreview: "no",
                thumbnailsPreviewWidth: 196,
                thumbnailsPreviewHeight: 110
            });

            // Force unmute and autoplay - multiple attempts
            setTimeout(function() {
                if (player && player.setVolume) {
                    player.setVolume(1);
                }
                if (player && player.play) {
                    player.play();
                }
            }, 500);

            setTimeout(function() {
                if (player && player.setVolume) {
                    player.setVolume(1);
                }
                if (player && player.play) {
                    player.play();
                }
            }, 1000);

            setTimeout(function() {
                if (player && player.setVolume) {
                    player.setVolume(1);
                }
                if (player && player.play) {
                    player.play();
                }
            }, 2000);

        });
    </script>








     <!-- Custom Script to Auto-click the Unmute Button and Force Autoplay -->
    <script type="text/javascript">
        // Disable cache to ensure fresh load
        if (performance.navigation.type !== 1) {
            // Not a reload, force cache clear
            window.onbeforeunload = function() {
                window.scrollTo(0, 0);
            };
        }

        // Run immediately without waiting for ready
        (function forceAutoplayImmediately() {
            var attempts = 0;
            var maxAttempts = 20;

            var forceAutoplay = setInterval(function() {
                attempts++;

                // Try to click any unmute button
                var unmuteElements = document.querySelectorAll('*');
                unmuteElements.forEach(function(elem) {
                    if (elem.textContent && elem.textContent.trim() === 'Click to Unmute') {
                        elem.click();
                    }
                });

                // Try jQuery if available
                if (typeof $ !== 'undefined') {
                    $("*:contains('Click to Unmute')").each(function() {
                        if ($(this).text().trim() === 'Click to Unmute') {
                            $(this).click();
                        }
                    });

                    // Try to click volume button
                    $('.fwdevp-volume-button').click();

                    // Try to click play button
                    $('.fwdevp-play-button').click();
                }

                // Stop after max attempts
                if (attempts >= maxAttempts) {
                    clearInterval(forceAutoplay);
                }
            }, 300);
        })();

        // Also run on document ready
        $(document).ready(function() {
            setTimeout(function() {
                $("*:contains('Click to Unmute')").each(function() {
                    if ($(this).text().trim() === 'Click to Unmute') {
                        $(this).click();
                    }
                });

                $('.fwdevp-volume-button').click();
                $('.fwdevp-play-button').click();
            }, 500);
        });

        // Also run on window load
        $(window).on('load', function() {
            setTimeout(function() {
                $("*:contains('Click to Unmute')").each(function() {
                    if ($(this).text().trim() === 'Click to Unmute') {
                        $(this).click();
                    }
                });

                $('.fwdevp-volume-button').click();
                $('.fwdevp-play-button').click();
            }, 500);

            setTimeout(function() {
                $('.fwdevp-volume-button').click();
                $('.fwdevp-play-button').click();
            }, 1500);
        });

        // Listen for any player ready events
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var playBtn = document.querySelector('.fwdevp-play-button');
                var volBtn = document.querySelector('.fwdevp-volume-button');

                if (playBtn) playBtn.click();
                if (volBtn) volBtn.click();

                // Also try to directly manipulate video elements
                var videos = document.querySelectorAll('video');
                videos.forEach(function(video) {
                    video.muted = false;
                    video.volume = 1;
                    video.play().catch(function(e) {
                        console.log('Autoplay prevented:', e);
                    });
                });

                // Try iframes
                var iframes = document.querySelectorAll('iframe');
                iframes.forEach(function(iframe) {
                    try {
                        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        var iframeVideos = iframeDoc.querySelectorAll('video');
                        iframeVideos.forEach(function(video) {
                            video.muted = false;
                            video.volume = 1;
                            video.play();
                        });
                    } catch(e) {
                        // Cross-origin, can't access
                    }
                });
            }, 1000);

            // Try again after 2 seconds
            setTimeout(function() {
                var videos = document.querySelectorAll('video');
                videos.forEach(function(video) {
                    video.muted = false;
                    video.volume = 1;
                    video.play().catch(function(e) {
                        console.log('Autoplay prevented:', e);
                    });
                });
            }, 2000);
        });

        // Force autoplay on any user interaction
        var hasInteracted = false;
        function forcePlayOnInteraction() {
            if (hasInteracted) return;
            hasInteracted = true;

            var videos = document.querySelectorAll('video');
            videos.forEach(function(video) {
                video.muted = false;
                video.volume = 1;
                video.play();
            });

            if (typeof $ !== 'undefined') {
                $('.fwdevp-volume-button').click();
                $('.fwdevp-play-button').click();
            }
        }

        // Listen for first interaction
        document.addEventListener('click', forcePlayOnInteraction, { once: true });
        document.addEventListener('touchstart', forcePlayOnInteraction, { once: true });
        document.addEventListener('keydown', forcePlayOnInteraction, { once: true });
    </script>
</body>
</html>
