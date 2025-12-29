<div class="news-ticker-container" id="news-ticker-main">
    <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 2px solid #e50914; padding-bottom: 10px;">
        <h4 style="color: #fff; margin: 0;">Latest News</h4>
        <button id="back-to-news-btn" class="btn btn-sm btn-danger" style="display: none; font-size: 12px; padding: 2px 8px;">
            <i class="fa fa-arrow-left"></i> Back
        </button>
    </div>

    <div id="news-list-view" class="news-scroll-mask">
        <div class="news-scroll-content @if(isset($rss_news) && count($rss_news) > 2) scrolling @endif">
            @if(isset($rss_news) && count($rss_news) > 0)
                @foreach($rss_news as $news)
                    <div class="news-item">
                        @if(!empty($news['image']))
                            <div class="news-image" style="margin-bottom: 10px;">
                                <img src="{{ $news['image'] }}" alt="{{ $news['headline'] }}" style="width: 100%; border-radius: 4px;">
                            </div>
                        @endif
                        <div class="news-headline">
                            <a href="#" class="news-link" style="color: #e50914; text-decoration: none;">
                                {{ $news['headline'] }}
                            </a>
                        </div>
                        <div class="news-details-teaser">
                            {!! \Illuminate\Support\Str::limit(strip_tags($news['details']), 150) !!}
                        </div>
                        <span class="news-time">
                            <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news['created_at'])->diffForHumans() }}
                        </span>

                        <!-- Hidden Full Content -->
                        <div class="news-full-content" style="display: none;">
                            <h5 style="color: #e50914; margin-bottom: 15px;">{{ $news['headline'] }}</h5>
                            @if(!empty($news['image']))
                                <div class="mb-3">
                                    <img src="{{ $news['image'] }}" alt="{{ $news['headline'] }}" style="width: 100%; border-radius: 4px;">
                                </div>
                            @endif
                            <div class="news-body" style="font-size: 14px; line-height: 1.6; color: #ddd; margin-bottom: 15px;">
                                {!! $news['details'] !!}
                            </div>
                            <div class="news-meta mb-3" style="font-size: 12px; color: #888;">
                                <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news['created_at'])->format('F j, Y, g:i a') }}
                            </div>
                            <a href="{{ $news['link'] }}" target="_blank" class="btn btn-outline-light btn-sm w-100">
                                Read Full Article <i class="fa fa-external-link"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                @if(count($rss_news) > 2)
                    <!-- Duplicate for smooth infinite scroll -->
                    @foreach($rss_news as $news)
                        <div class="news-item">
                            @if(!empty($news['image']))
                                <div class="news-image" style="margin-bottom: 10px;">
                                    <img src="{{ $news['image'] }}" alt="{{ $news['headline'] }}" style="width: 100%; border-radius: 4px;">
                                </div>
                            @endif
                            <div class="news-headline">
                                <a href="#" class="news-link" style="color: #e50914; text-decoration: none;">
                                    {{ $news['headline'] }}
                                </a>
                            </div>
                            <div class="news-details-teaser">
                                {!! \Illuminate\Support\Str::limit(strip_tags($news['details']), 150) !!}
                            </div>
                            <span class="news-time">
                                <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news['created_at'])->diffForHumans() }}
                            </span>

                             <!-- Hidden Full Content -->
                             <div class="news-full-content" style="display: none;">
                                <h5 style="color: #e50914; margin-bottom: 15px;">{{ $news['headline'] }}</h5>
                                @if(!empty($news['image']))
                                    <div class="mb-3">
                                        <img src="{{ $news['image'] }}" alt="{{ $news['headline'] }}" style="width: 100%; border-radius: 4px;">
                                    </div>
                                @endif
                                <div class="news-body" style="font-size: 14px; line-height: 1.6; color: #ddd; margin-bottom: 15px;">
                                    {!! $news['details'] !!}
                                </div>
                                <div class="news-meta mb-3" style="font-size: 12px; color: #888;">
                                    <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news['created_at'])->format('F j, Y, g:i a') }}
                                </div>
                                <a href="{{ $news['link'] }}" target="_blank" class="btn btn-outline-light btn-sm w-100">
                                    Read Full Article <i class="fa fa-external-link"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            @else
                <div class="text-white">No news available from DW.</div>
            @endif
        </div>
    </div>

    <!-- Detail View Container -->
    <div id="news-detail-view" style="display: none; animation: fadeIn 0.3s;">
        <!-- Content will be injected here -->
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle click on news headline
        $(document).on('click', '.news-link', function(e) {
            e.preventDefault();

            // Find the parent news item
            var $item = $(this).closest('.news-item');

            // Get the full content
            var content = $item.find('.news-full-content').html();

            // Inject content into detail view
            $('#news-detail-view').html(content);

            // Toggle views
            $('#news-list-view').hide();
            $('#news-detail-view').show();
            $('#back-to-news-btn').show();

            // Reset scroll position of the main container
            $('.news-ticker-container').scrollTop(0);
        });

        // Handle Back button click
        $('#back-to-news-btn').click(function() {
            $('#news-detail-view').hide();
            $('#news-list-view').show();
            $(this).hide();

            // Clear detail view content
            $('#news-detail-view').empty();
        });
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
