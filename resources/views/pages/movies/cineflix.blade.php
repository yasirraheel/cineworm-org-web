@extends('site_app')

@section('head_title', 'CineFlix | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
    <div class="view-all-video-area view-movie-list-item vfx-item-ptb">
        <div class="container-fluid">
            <div class="filter-list-area">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-title-item">CineFlix</div>
                        <p class="mb-0">Creator films available to watch on Cineworm. Select a film to view its price and access options.</p>
                    </div>
                </div>
            </div>

            @if ($films->count())
                <div class="row">
                    @foreach ($films as $film)
                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 col-6">
                            <div class="single-video">
                                <a href="{{ route('creator-films.show', ['slug' => $film->video_slug, 'id' => $film->id]) }}"
                                    title="{{ stripslashes($film->video_title) }}">
                                    <div class="video-img">
                                        <div class="vid-lab-premium">
                                            <img src="{{ URL::asset('site_assets/images/ic-premium.svg?v=3') }}" alt="premium" title="premium">
                                        </div>
                                        <span class="video-item-content">{{ Str::limit(stripslashes($film->video_title), 20) }}</span>
                                        <img src="{{ URL::to('/' . ($film->video_image_thumb ?: 'site_assets/images/poster_placeholder.png')) }}"
                                            alt="{{ stripslashes($film->video_title) }}"
                                            title="{{ stripslashes($film->video_title) }}"
                                            onerror="this.src='{{ URL::asset('site_assets/images/poster_placeholder.png') }}';">
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-xs-12">
                    @include('_particles.pagination', ['paginator' => $films])
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-film d-block mb-3" style="font-size:44px; color:rgba(255,255,255,.22);"></i>
                    <h2>No CineFlix films available yet</h2>
                    <p>Approved creator films will appear here when they are ready to watch.</p>
                    <a href="{{ URL::to('movies') }}" class="vfx-item-btn-danger text-uppercase">Browse Cineworm Movies</a>
                </div>
            @endif
        </div>
    </div>
@endsection
