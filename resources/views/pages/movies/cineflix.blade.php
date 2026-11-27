@extends('site_app')

@section('head_title', 'CineFlix | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<style>
    .cineflix-page { color:#fff; }
    .cineflix-intro { margin-bottom:28px; }
    .cineflix-intro .page-title-item { margin-bottom:8px; }
    .cineflix-intro p { max-width:760px; margin:0; color:rgba(255,255,255,.65); font-size:15px; line-height:1.6; }
    .cineflix-note { display:flex; align-items:flex-start; gap:10px; max-width:760px; margin-top:16px; padding:12px 14px; border-left:3px solid #ff0f78; background:rgba(255,15,120,.08); color:rgba(255,255,255,.72); font-size:13px; line-height:1.5; }
    .cineflix-note i { color:#ff7b00; margin-top:2px; }
    .cineflix-card { height:100%; overflow:hidden; background:rgba(24,16,53,.94); border:1px solid rgba(255,255,255,.10); border-radius:10px; transition:transform .18s,border-color .18s,box-shadow .18s; }
    .cineflix-card:hover { transform:translateY(-3px); border-color:rgba(255,15,120,.62); box-shadow:0 12px 26px rgba(0,0,0,.24); }
    .cineflix-card .video-img { position:relative; height:250px; overflow:hidden; background:#0d071e; }
    .cineflix-card .video-img > img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .25s ease; }
    .cineflix-card:hover .video-img > img { transform:scale(1.04); }
    .cineflix-badge { position:absolute; z-index:2; top:12px; left:12px; padding:5px 9px; border-radius:999px; background:linear-gradient(135deg,#ff0f78,#ff7b00); color:#fff; font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
    .cineflix-price-badge { position:absolute; z-index:2; right:12px; bottom:12px; padding:6px 10px; border:1px solid rgba(255,255,255,.20); border-radius:7px; background:rgba(13,7,30,.88); color:#fff; font-size:12px; font-weight:800; }
    .cineflix-card-body { padding:16px; }
    .cineflix-card-body h3 { min-height:42px; margin:0 0 12px; color:#fff; font-size:16px; font-weight:800; line-height:1.35; }
    .cineflix-card-meta { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:14px; color:rgba(255,255,255,.55); font-size:12px; }
    .cineflix-card-meta span:first-child { color:#ff72ae; }
    .cineflix-card-body .vfx-item-btn-danger { display:inline-flex; align-items:center; justify-content:center; width:100%; padding:10px 14px; font-size:12px; }
    .cineflix-empty { padding:58px 24px; text-align:center; background:rgba(24,16,53,.94); border:1px solid rgba(255,255,255,.10); border-radius:12px; }
    .cineflix-empty i { display:block; margin-bottom:16px; color:rgba(255,255,255,.20); font-size:44px; }
    .cineflix-empty h2 { margin:0 0 8px; font-size:22px; }
    .cineflix-empty p { margin:0 auto 22px; color:rgba(255,255,255,.58); font-size:14px; }
    @media (max-width:767px) { .cineflix-card .video-img { height:220px; } }
</style>

<div class="cineflix-page">
    <div class="vfx-item-ptb">
        <div class="container-fluid">
            <div class="cineflix-intro">
                <div class="page-title-item">CineFlix</div>
                <p>Discover creator-owned films available to watch on Cineworm. These films are separate from CineStock and may require a one-time purchase.</p>
                <div class="cineflix-note"><i class="fa fa-info-circle"></i><span>Choose a film to view its details, price and payment options. After payment, it becomes available in your account to watch.</span></div>
            </div>

            @if($films->count())
                <div class="row">
                    @foreach($films as $film)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                            <article class="cineflix-card">
                                <a href="{{ route('creator-films.show', ['slug' => $film->video_slug, 'id' => $film->id]) }}" title="{{ stripslashes($film->video_title) }}">
                                    <div class="video-img">
                                        <span class="cineflix-badge">CineFlix</span>
                                        <span class="cineflix-price-badge">{{ strtoupper($film->creator_currency ?: getcong('currency_code')) }} {{ number_format((float) $film->creator_price, 2) }}</span>
                                        <img src="{{ URL::to('/' . ($film->video_image_thumb ?: $film->video_image ?: 'site_assets/images/poster_placeholder.png')) }}" alt="{{ stripslashes($film->video_title) }}" onerror="this.src='{{ URL::asset('site_assets/images/poster_placeholder.png') }}';">
                                    </div>
                                </a>
                                <div class="cineflix-card-body">
                                    <h3>{{ stripslashes($film->video_title) }}</h3>
                                    <div class="cineflix-card-meta"><span><i class="fa fa-play-circle"></i> Creator film</span><span>One-time access</span></div>
                                    <a href="{{ route('creator-films.show', ['slug' => $film->video_slug, 'id' => $film->id]) }}" class="vfx-item-btn-danger text-uppercase">View film</a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                @include('_particles.pagination', ['paginator' => $films])
            @else
                <div class="cineflix-empty">
                    <i class="fa fa-film"></i>
                    <h2>No CineFlix films available yet</h2>
                    <p>Approved creator films will appear here when they are ready to watch.</p>
                    <a href="{{ URL::to('movies') }}" class="vfx-item-btn text-uppercase">Browse Cineworm movies</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
