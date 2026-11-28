@extends('site_app')

@section('head_title', stripslashes($movie->video_title) . ' | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
@php
    $imdbUrl = null;
    if (!empty($movie->imdb_id)) {
        $imdbUrl = filter_var($movie->imdb_id, FILTER_VALIDATE_URL)
            ? $movie->imdb_id
            : 'https://www.imdb.com/title/' . ltrim($movie->imdb_id, '/');
    }
@endphp
<div class="vfx-item-ptb">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-4 col-md-5 mb-4">
                <img src="{{ URL::to('/' . ($movie->video_image ?: 'site_assets/images/poster_placeholder.png')) }}"
                     alt="{{ stripslashes($movie->video_title) }}" class="img-fluid rounded"
                     onerror="this.src='{{ URL::asset('site_assets/images/poster_placeholder.png') }}';">
            </div>
            <div class="col-lg-8 col-md-7">
                <span class="text-uppercase" style="color:#ff0f78;font-weight:700;letter-spacing:.08em;">Creator film</span>
                <h1 class="mt-2">{{ stripslashes($movie->video_title) }}</h1>
                <div class="mt-3">{!! stripslashes($movie->video_description) !!}</div>

                <div class="mt-3 d-flex flex-wrap align-items-center" style="gap:10px;">
                    @if($movie->imdb_rating)
                        <span class="video-imdb-view"><img src="{{ URL::to('site_assets/images/imdb-logo.png') }}" alt="IMDb"> {{ $movie->imdb_rating }}</span>
                    @endif
                    @if($movie->content_rating)<span class="channel_info_count">{{ $movie->content_rating }}</span>@endif
                    @if($movie->duration)<span class="video-posts-author"><i class="fa fa-clock"></i> {{ $movie->duration }}</span>@endif
                    @if($imdbUrl)<a href="{{ $imdbUrl }}" target="_blank" rel="noopener" class="vfx-item-btn">IMDb</a>@endif
                    @if($movie->webpage_url)<a href="{{ $movie->webpage_url }}" target="_blank" rel="noopener" class="vfx-item-btn">Website</a>@endif
                    @if($movie->funding_url)<a href="{{ $movie->funding_url }}" target="_blank" rel="noopener" class="vfx-item-btn">Support / Donate</a>@endif
                </div>
                @if($movie->writer || $movie->production_company)
                    <div class="mt-3">
                        @if($movie->writer)<div><strong>Writer:</strong> {{ $movie->writer }}</div>@endif
                        @if($movie->production_company)<div><strong>Production:</strong> {{ $movie->production_company }}</div>@endif
                    </div>
                @endif

                @if(!empty($movie->creator_trailer_url))
                    <p class="mt-3"><a href="{{ $movie->creator_trailer_url }}" target="_blank" rel="noopener" class="vfx-item-btn">Watch free trailer</a></p>
                @endif

                @if($hasAccess)
                    <a href="{{ route('creator-films.watch', ['slug' => $movie->video_slug, 'id' => $movie->id]) }}" class="vfx-item-btn-danger text-uppercase">Watch film</a>
                @else
                    <div class="mt-4">
                        <h3>{{ $totals['currency'] }} {{ number_format($totals['total_amount'], 2) }}</h3>
                        <p>Pay once to watch this creator-owned film. It will not appear in the free/random stream.</p>
                        @auth
                            <a href="{{ route('creator-films.checkout', $movie->id) }}" class="vfx-item-btn-danger text-uppercase">Buy to watch</a>
                        @else
                            <a href="{{ URL::to('login') }}" class="vfx-item-btn-danger text-uppercase">Login to continue</a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
