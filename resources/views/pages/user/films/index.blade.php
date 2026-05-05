@extends('site_app')

@section('head_title', 'My Films | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>My Films</h2>
                <nav id="breadcrumbs"><ul>
                    <li><a href="{{ URL::to('/') }}">Home</a></li>
                    <li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li>
                    <li>My Films</li>
                </ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">

        <style>
            .ufilm-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 14px;
                margin-bottom: 28px;
            }
            .ufilm-header h3 {
                color: #fff;
                font-size: 20px;
                font-weight: 700;
                margin: 0;
            }
            .ufilm-header p {
                color: rgba(255,255,255,0.5);
                font-size: 13px;
                margin: 4px 0 0;
            }
            .ufilm-btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 22px;
                background: linear-gradient(135deg,#ff0f28,#c8001f);
                border: none;
                border-radius: 10px;
                color: #fff !important;
                font-size: 13px;
                font-weight: 700;
                text-decoration: none;
                cursor: pointer;
                box-shadow: 0 4px 14px rgba(255,15,40,0.3);
                transition: all 0.18s ease;
                white-space: nowrap;
            }
            .ufilm-btn-primary:hover {
                background: linear-gradient(135deg,#ff2e44,#e0001f);
                box-shadow: 0 6px 20px rgba(255,15,40,0.46);
                transform: translateY(-1px);
                color: #fff !important;
            }
            .ufilm-btn-ghost {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 16px;
                background: transparent;
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 9px;
                color: rgba(255,255,255,0.75) !important;
                font-size: 12.5px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.18s ease;
                white-space: nowrap;
            }
            .ufilm-btn-ghost:hover {
                background: rgba(255,255,255,0.06);
                border-color: rgba(255,255,255,0.38);
                color: #fff !important;
            }
            .ufilm-btn-del {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 14px;
                background: transparent;
                border: 1px solid rgba(255,15,40,0.4);
                border-radius: 9px;
                color: #ff0f28 !important;
                font-size: 12px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.18s ease;
                cursor: pointer;
            }
            .ufilm-btn-del:hover {
                background: rgba(255,15,40,0.1);
                border-color: #ff0f28;
                color: #ff0f28 !important;
            }
            .ufilm-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }
            .ufilm-card {
                background: rgba(18,18,22,0.96);
                border: 1px solid rgba(255,255,255,0.07);
                border-radius: 14px;
                overflow: hidden;
                transition: border-color 0.2s ease, transform 0.2s ease;
            }
            .ufilm-card:hover {
                border-color: rgba(255,15,40,0.3);
                transform: translateY(-3px);
            }
            .ufilm-card-thumb {
                width: 100%;
                height: 150px;
                object-fit: cover;
                background: #111;
                display: block;
            }
            .ufilm-card-thumb-placeholder {
                width: 100%;
                height: 150px;
                background: rgba(255,255,255,0.04);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 38px;
                color: rgba(255,255,255,0.12);
            }
            .ufilm-card-body {
                padding: 16px;
            }
            .ufilm-card-title {
                color: #fff;
                font-size: 14px;
                font-weight: 700;
                margin: 0 0 8px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .ufilm-card-status {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 3px 10px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 700;
                margin-bottom: 12px;
            }
            .ufilm-status-pending {
                background: rgba(245,158,11,0.15);
                color: #f59e0b;
            }
            .ufilm-status-live {
                background: rgba(16,185,129,0.15);
                color: #10b981;
            }
            .ufilm-card-actions {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }
            .ufilm-empty {
                background: rgba(18,18,22,0.96);
                border: 1px solid rgba(255,255,255,0.07);
                border-radius: 16px;
                padding: 60px 24px;
                text-align: center;
            }
            .ufilm-empty i {
                font-size: 48px;
                color: rgba(255,255,255,0.1);
                display: block;
                margin-bottom: 18px;
            }
            .ufilm-empty h4 {
                color: rgba(255,255,255,0.5);
                font-size: 17px;
                margin: 0 0 8px;
            }
            .ufilm-empty p {
                color: rgba(255,255,255,0.3);
                font-size: 13.5px;
                margin: 0 0 24px;
            }
        </style>

        <div class="ufilm-header">
            <div>
                <h3><i class="fa fa-film" style="color:#ff0f28;margin-right:8px;"></i>My Films</h3>
                <p>Films you have uploaded. New uploads are reviewed before going live.</p>
            </div>
            <a href="{{ URL::to('user/films/upload') }}" class="ufilm-btn-primary">
                <i class="fa fa-upload"></i> Upload New Film
            </a>
        </div>

        @if($films->count())
            <div class="ufilm-grid">
                @foreach($films as $film)
                    <div class="ufilm-card">
                        @if($film->video_image && $film->video_image !== 'NA')
                            <img src="{{ Str::startsWith($film->video_image, 'http') ? $film->video_image : URL::asset($film->video_image) }}"
                                 alt="{{ $film->video_title }}" class="ufilm-card-thumb" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="ufilm-card-thumb-placeholder" style="display:none;"><i class="fa fa-film"></i></div>
                        @else
                            <div class="ufilm-card-thumb-placeholder"><i class="fa fa-film"></i></div>
                        @endif
                        <div class="ufilm-card-body">
                            <div class="ufilm-card-title">{{ $film->video_title }}</div>
                            @if($film->status == 1)
                                <span class="ufilm-card-status ufilm-status-live">
                                    <span style="width:5px;height:5px;border-radius:50%;background:#10b981;flex-shrink:0;"></span> Live
                                </span>
                            @else
                                <span class="ufilm-card-status ufilm-status-pending">
                                    <span style="width:5px;height:5px;border-radius:50%;background:#f59e0b;flex-shrink:0;"></span> Pending Review
                                </span>
                            @endif
                            <div class="ufilm-card-actions">
                                @if($film->status == 1)
                                    <a href="{{ URL::to('movies/details/'.$film->video_slug.'/'.$film->id) }}" class="ufilm-btn-ghost" target="_blank">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                @endif
                                <form method="POST" action="{{ URL::to('user/films/'.$film->id.'/delete') }}" style="display:inline;"
                                      onsubmit="return confirm('Remove this film? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ufilm-btn-del">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $films->links() }}
        @else
            <div class="ufilm-empty">
                <i class="fa fa-film"></i>
                <h4>No films uploaded yet</h4>
                <p>Upload your first film and it will appear here after admin review.</p>
                <a href="{{ URL::to('user/films/upload') }}" class="ufilm-btn-primary">
                    <i class="fa fa-upload"></i> Upload Your First Film
                </a>
            </div>
        @endif

    </div>
</div>

@if(Session::has('flash_message'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, icon:'success', title:'{{ Session::get("flash_message") }}' });
    });
</script>
@endif
@if(Session::has('error_flash_message'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, icon:'error', title:'{{ Session::get("error_flash_message") }}' });
    });
</script>
@endif
@endsection
