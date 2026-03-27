<!-- Combined Title, Meta Info, and Action Buttons Row -->
<div class="player-footer-unified">
    <div class="video-title-section">
        <a href="{{ url('movies/details', ['slug' => $movies_info->video_slug, 'id' => $movies_info->id]) }}" class="video-title-link">
            <h3 class="video-title">{{ $movies_info->video_title }}</h3>
        </a>
    </div>

    <!-- Video Info Meta -->
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

        <a href="{{ $random_movie ? url('movies/'.$random_movie->video_slug.'/'.$random_movie->id) : URL::to('/') }}" class="action-btn next-btn" id="footer-stumble-btn">
            <i class="fas fa-random"></i>
            <span>Stumble</span>
        </a>
    </div>
</div>

<script>
// AJAX Like Button Handler for dynamically loaded content
(function() {
    const likeForm = document.querySelector('.like-form');
    if (likeForm) {
        likeForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const button = form.querySelector('.like-btn');
            const likeText = form.querySelector('.like-text');
            const formData = new FormData(form);

            // Disable button during request
            button.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle liked state
                    button.classList.toggle('liked');

                    // Update like count and text
                    if (data.liked) {
                        likeText.textContent = 'Unlike (' + data.likes + ')';
                        form.action = form.action.replace('/like/', '/unlike/');
                    } else {
                        likeText.textContent = 'Like (' + data.likes + ')';
                        form.action = form.action.replace('/unlike/', '/like/');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                // Re-enable button
                button.disabled = false;
            });
        });
    }
})();
</script>
