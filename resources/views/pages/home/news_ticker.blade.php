<div class="news-ticker-container">
    <h4 style="color: #fff; border-bottom: 2px solid #e50914; padding-bottom: 10px; margin-bottom: 15px;">
        Latest News
    </h4>

    <div class="news-scroll-mask">
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
                            <a href="{{ $news['link'] }}" target="_blank" style="color: #e50914; text-decoration: none;">
                                {{ $news['headline'] }}
                            </a>
                        </div>
                        <div class="news-details">
                            {!! \Illuminate\Support\Str::limit(strip_tags($news['details']), 150) !!}
                        </div>
                        <span class="news-time">
                            <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news['created_at'])->diffForHumans() }}
                        </span>
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
                                <a href="{{ $news['link'] }}" target="_blank" style="color: #e50914; text-decoration: none;">
                                    {{ $news['headline'] }}
                                </a>
                            </div>
                            <div class="news-details">
                                {!! \Illuminate\Support\Str::limit(strip_tags($news['details']), 150) !!}
                            </div>
                            <span class="news-time">
                                <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($news['created_at'])->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                @endif
            @else
                <div class="text-white">No news available from DW.</div>
            @endif
        </div>
    </div>
</div>
