@extends('site_app')

@section('head_title', 'My Films | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<style>
    .cine-films-page { color: #fff; }
    .cine-films-page .vfx-item-ptb { padding-top: 42px; padding-bottom: 58px; }
    .cine-films-hero { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:28px; }
    .cine-films-hero h1 { margin:0 0 8px; font-size:32px; font-weight:800; }
    .cine-films-hero p { margin:0; color:rgba(255,255,255,.65); font-size:15px; }
    .cine-films-btn { display:inline-flex; align-items:center; justify-content:center; gap:9px; padding:12px 20px; border-radius:9px; color:#fff !important; text-decoration:none !important; font-size:13px; font-weight:800; border:1px solid transparent; transition:transform .18s, box-shadow .18s, filter .18s; white-space:nowrap; }
    .cine-films-btn:hover { color:#fff !important; transform:translateY(-2px); filter:brightness(1.08); }
    .cine-films-btn-primary { background:linear-gradient(135deg,#ff0f78,#ff7b00); box-shadow:0 9px 22px rgba(255,15,120,.22); }
    .cine-films-btn-ghost { background:rgba(255,255,255,.04); border-color:rgba(255,255,255,.16); color:rgba(255,255,255,.82) !important; }
    .cine-films-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin-bottom:24px; }
    .cine-films-summary-card { padding:17px 19px; background:rgba(24,16,53,.94); border:1px solid rgba(255,255,255,.10); border-radius:12px; }
    .cine-films-summary-card small { display:block; margin-bottom:6px; color:rgba(255,255,255,.54); font-size:11px; text-transform:uppercase; letter-spacing:.08em; font-weight:800; }
    .cine-films-summary-card strong { color:#fff; font-size:22px; }
    .cine-films-panel { padding:24px; background:rgba(24,16,53,.94); border:1px solid rgba(255,255,255,.10); border-radius:14px; box-shadow:0 12px 35px rgba(0,0,0,.15); }
    .cine-films-panel-title { display:flex; align-items:center; gap:11px; margin:0 0 20px; font-size:19px; font-weight:800; }
    .cine-films-panel-title i { color:#ff0f78; }
    .cine-films-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(235px,1fr)); gap:18px; }
    .cine-film-card { overflow:hidden; background:rgba(8,5,23,.62); border:1px solid rgba(255,255,255,.10); border-radius:12px; transition:transform .18s,border-color .18s,box-shadow .18s; }
    .cine-film-card:hover { transform:translateY(-3px); border-color:rgba(255,15,120,.62); box-shadow:0 14px 26px rgba(0,0,0,.20); }
    .cine-film-thumb, .cine-film-thumb-placeholder { display:block; width:100%; height:154px; object-fit:cover; background:rgba(255,255,255,.04); }
    .cine-film-thumb-placeholder { display:grid; place-items:center; color:rgba(255,255,255,.18); font-size:38px; }
    .cine-film-body { padding:15px; }
    .cine-film-title { margin:0 0 10px; color:#fff; font-size:15px; font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .cine-film-status { display:inline-flex; align-items:center; gap:6px; margin-bottom:14px; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; }
    .cine-film-status::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .cine-film-status-pending { color:#f5b642; background:rgba(245,182,66,.13); }
    .cine-film-status-live { color:#63d59a; background:rgba(99,213,154,.13); }
    .cine-film-actions { display:flex; gap:8px; flex-wrap:wrap; }
    .cine-film-actions .cine-films-btn { padding:8px 12px; font-size:11px; }
    .cine-film-actions form { margin:0; }
    .cine-film-remove { cursor:pointer; background:transparent; border:1px solid rgba(255,80,105,.50); color:#ff7183 !important; }
    .cine-film-empty { padding:58px 24px; text-align:center; background:rgba(8,5,23,.46); border:1px dashed rgba(255,255,255,.16); border-radius:12px; }
    .cine-film-empty-icon { display:grid; place-items:center; width:68px; height:68px; margin:0 auto 18px; border-radius:50%; color:#ff0f78; background:rgba(255,15,120,.12); font-size:28px; }
    .cine-film-empty h2 { margin:0 0 8px; font-size:21px; font-weight:800; }
    .cine-film-empty p { max-width:520px; margin:0 auto 22px; color:rgba(255,255,255,.60); font-size:14px; line-height:1.6; }
    .cine-film-next { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-top:18px; }
    .cine-film-next-item { padding:15px; border-radius:10px; background:rgba(255,255,255,.035); border:1px solid rgba(255,255,255,.08); }
    .cine-film-next-item i { margin-right:6px; color:#ff8a00; }
    .cine-film-next-item strong { display:block; margin-bottom:5px; color:#fff; font-size:13px; }
    .cine-film-next-item span { color:rgba(255,255,255,.52); font-size:12px; line-height:1.45; }
    .cine-films-pagination { margin-top:24px; }
    .cine-films-pagination .pagination { justify-content:center; }
    .cine-films-pagination .page-link { color:#fff; background:rgba(24,16,53,.9); border-color:rgba(255,255,255,.12); }
    .cine-films-pagination .page-item.active .page-link { background:#ff0f78; border-color:#ff0f78; }
    @media (max-width:700px) { .cine-films-hero { align-items:stretch; flex-direction:column; } .cine-films-summary,.cine-film-next { grid-template-columns:1fr; } .cine-films-panel { padding:18px; } .cine-films-hero h1 { font-size:27px; } }
</style>

<div class="cine-films-page">
    <div class="breadcrumb-section bg-xs" style="background-image:url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid"><div class="row"><div class="col-xl-12">
            <h2>My Films</h2>
            <nav id="breadcrumbs"><ul><li><a href="{{ URL::to('/') }}">Home</a></li><li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li><li>My Films</li></ul></nav>
        </div></div></div>
    </div>

    <div class="vfx-item-ptb">
        <div class="container-fluid">
            <div class="cine-films-hero">
                <div><h1>Your films</h1><p>Manage your submissions and track their review status in one place.</p></div>
                <a href="{{ URL::to('user/films/upload') }}" class="cine-films-btn cine-films-btn-primary"><i class="fa fa-upload"></i> Upload new film</a>
            </div>

            @php
                $totalFilms = $films->total();
                $liveFilms = $films->getCollection()->where('status', 1)->count();
                $pendingFilms = $films->getCollection()->where('status', '!=', 1)->count();
            @endphp
            <div class="cine-films-summary">
                <div class="cine-films-summary-card"><small>Total submissions</small><strong>{{ $totalFilms }}</strong></div>
                <div class="cine-films-summary-card"><small>Live films</small><strong>{{ $liveFilms }}</strong></div>
                <div class="cine-films-summary-card"><small>Pending review</small><strong>{{ $pendingFilms }}</strong></div>
            </div>

            <section class="cine-films-panel">
                <h2 class="cine-films-panel-title"><i class="fa fa-film"></i> My film library</h2>
                @if($films->count())
                    <div class="cine-films-grid">
                        @foreach($films as $film)
                            <article class="cine-film-card">
                                @if($film->video_image && $film->video_image !== 'NA')
                                    <img src="{{ Str::startsWith($film->video_image, 'http') ? $film->video_image : URL::asset($film->video_image) }}" alt="{{ $film->video_title }}" class="cine-film-thumb" onerror="this.style.display='none';this.nextElementSibling.style.display='grid';">
                                    <div class="cine-film-thumb-placeholder" style="display:none;"><i class="fa fa-film"></i></div>
                                @else
                                    <div class="cine-film-thumb-placeholder"><i class="fa fa-film"></i></div>
                                @endif
                                <div class="cine-film-body">
                                    <h3 class="cine-film-title" title="{{ $film->video_title }}">{{ $film->video_title }}</h3>
                                    @if($film->status == 1 && $film->creator_sale_enabled && $film->creator_sale_status === 'pending')
                                        <span class="cine-film-status cine-film-status-pending">Sale awaiting approval</span>
                                    @elseif($film->status == 1)
                                        <span class="cine-film-status cine-film-status-live">Live</span>
                                    @else
                                        <span class="cine-film-status cine-film-status-pending">Pending review</span>
                                    @endif
                                    <div class="cine-film-actions">
                                        @if($film->status == 1)
                                            <a href="{{ $film->creator_sale_enabled && $film->creator_sale_status === 'approved' ? route('creator-films.show', ['slug' => $film->video_slug, 'id' => $film->id]) : URL::to('movies/details/'.$film->video_slug.'/'.$film->id) }}" class="cine-films-btn cine-films-btn-ghost" target="_blank"><i class="fa fa-eye"></i> View</a>
                                        @endif
                                        <form method="POST" action="{{ URL::to('user/films/'.$film->id.'/delete') }}" onsubmit="return confirm('Remove this film? This cannot be undone.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="cine-films-btn cine-film-remove"><i class="fa fa-trash"></i> Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="cine-films-pagination">{{ $films->links() }}</div>
                @else
                    <div class="cine-film-empty">
                        <div class="cine-film-empty-icon"><i class="fa fa-film"></i></div>
                        <h2>No films uploaded yet</h2>
                        <p>Upload your first film and it will appear here after the admin team reviews it. You can submit a free film or request paid access if you own the content.</p>
                        <a href="{{ URL::to('user/films/upload') }}" class="cine-films-btn cine-films-btn-primary"><i class="fa fa-upload"></i> Upload your first film</a>
                        <div class="cine-film-next">
                            <div class="cine-film-next-item"><strong><i class="fa fa-pencil"></i> Add details</strong><span>Title, description, language and genre.</span></div>
                            <div class="cine-film-next-item"><strong><i class="fa fa-link"></i> Add a source</strong><span>Provide a working video link.</span></div>
                            <div class="cine-film-next-item"><strong><i class="fa fa-check-circle"></i> Submit for review</strong><span>We review it before it goes live.</span></div>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

@if(Session::has('flash_message'))
<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire({toast:true,position:'top-end',showConfirmButton:false,timer:4000,icon:'success',title:@json(Session::get('flash_message'))});});</script>
@endif
@if(Session::has('error_flash_message'))
<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire({toast:true,position:'top-end',showConfirmButton:false,timer:4000,icon:'error',title:@json(Session::get('error_flash_message'))});});</script>
@endif
@endsection
