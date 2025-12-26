<!-- Start Header -->
<header style="position: relative; height: 100px;">
    <!-- Start Navigation Area -->
    <div class="main-menu">
        <nav class="header-section pin-style">
            <div class="container-fluid">
                <div class="mod-menu">
                    <div class="row">
                        <div class="col-2">
                            @if (getcong('site_logo'))
                                <a href="{{ URL::to('/') }}" title="logo" class="logo">
                                    <img src="{{ URL::asset('/' . getcong('site_logo')) }}" alt="logo"
                                        title="logo">
                                </a>
                            @else
                                <a href="{{ URL::to('/') }}" title="logo" class="logo">
                                    <img src="{{ URL::asset('site_assets/images/logo.png') }}" alt="logo"
                                        title="logo">
                                </a>
                            @endif
                        </div>

                        <div class="col-4 nav-order-last nopadding">

                        </div>
                        <div class="col-2 nav-order-last nopadding text-center"
                            style="display: flex; justify-content: center; align-items: center; height: 100px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;">

                            <div class="subscribe-btn-item stumble-button">
                                <a href="javascript:void(0);" id="stumble-btn" title="Stumble"
                                    style="display: flex; align-items: center; text-decoration: none; font-weight: bold; border: 2px solid #444; padding: 10px 20px; border-radius: 5px; transition: background 0.3s, border 0.3s; cursor: pointer;">

                                    <img src="{{ URL::asset('site_assets/images/ic-subscribe2.png') }}"
                                        alt="ic-subscribe" title="Stumble"
                                        style="width: 24px; height: 24px; margin-right: 8px;">

                                    <span style="color: #fff;" id="stumble-text">Stumble</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- End Navigation Area -->
</header>
<!-- End Header -->

<!-- Custom CSS for Responsive Behavior -->
<style>
    /* Hide the Stumble button on large screens */


    /* Show Stumble button only on mobile screens */
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle stumble button click
    var stumbleBtn = document.getElementById('stumble-btn');
    var stumbleText = document.getElementById('stumble-text');

    if (stumbleBtn) {
        stumbleBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Show loading state
            var originalText = stumbleText.textContent;
            stumbleText.textContent = 'Loading...';
            stumbleBtn.style.pointerEvents = 'none';

            // Make AJAX request to get random movie
            fetch('{{ route("ajax.random.movie") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the player section
                    var playerContainer = document.querySelector('.col-lg-9.col-md-12');
                    if (playerContainer) {
                        playerContainer.innerHTML = data.playerHtml;
                    }

                    // Reset button state
                    stumbleText.textContent = originalText;
                    stumbleBtn.style.pointerEvents = 'auto';

                    // Scroll to top smoothly
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            })
            .catch(error => {
                console.error('Error loading random movie:', error);
                alert('Failed to load video. Please try again.');

                // Reset button state
                stumbleText.textContent = originalText;
                stumbleBtn.style.pointerEvents = 'auto';
            });
        });
    }
});
</script>
