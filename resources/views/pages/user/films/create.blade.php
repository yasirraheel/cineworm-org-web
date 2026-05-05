@extends('site_app')

@section('head_title', 'Upload Film | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>Upload Film</h2>
                <nav id="breadcrumbs"><ul>
                    <li><a href="{{ URL::to('/') }}">Home</a></li>
                    <li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ URL::to('user/films') }}">My Films</a></li>
                    <li>Upload</li>
                </ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">

        {{-- re-use the promotion UI system classes (promo-*) already loaded globally --}}

        <div style="max-width:820px;margin:0 auto;">

            {{-- Info banner --}}
            <div class="promo-alert promo-alert-info" style="margin-bottom:24px;">
                <i class="fa fa-info-circle" style="flex-shrink:0;margin-top:2px;"></i>
                <span>Uploaded films are <strong>pending review</strong> and will go live once approved by our team. Make sure your video URL is publicly accessible.</span>
            </div>

            <form method="POST" action="{{ URL::to('user/films/store') }}">
                @csrf

                {{-- Section 1: Film Details --}}
                <div class="promo-panel">
                    <div class="promo-panel-header">
                        <div>
                            <h3><i class="fa fa-film" style="color:#ff0f28;margin-right:8px;"></i>Film Details</h3>
                            <p class="promo-subtitle">Enter the title, genres, and a short description of your film.</p>
                        </div>
                    </div>

                    <div class="promo-form-group">
                        <label class="promo-label">Film Title <span style="color:#ff0f28;">*</span></label>
                        <input type="text" name="video_title" class="promo-input form-control"
                               value="{{ old('video_title') }}"
                               placeholder="e.g. The Last Horizon" required>
                    </div>

                    <div class="promo-form-group">
                        <label class="promo-label">Genre(s) <span style="color:#ff0f28;">*</span></label>
                        <select name="genres[]" class="promo-select form-control" multiple required>
                            @foreach($genre_list as $genre)
                                <option value="{{ $genre->id }}" @if(is_array(old('genres')) && in_array($genre->id, old('genres'))) selected @endif>
                                    {{ $genre->genre_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="promo-input-hint">Hold Ctrl / Cmd to select multiple genres.</p>
                    </div>

                    <div class="promo-form-group" style="margin-bottom:0;">
                        <label class="promo-label">Description</label>
                        <textarea name="video_description" rows="4" class="promo-textarea form-control"
                                  placeholder="A brief synopsis of your film…">{{ old('video_description') }}</textarea>
                    </div>
                </div>

                {{-- Section 2: Video Source --}}
                <div class="promo-panel">
                    <div class="promo-panel-header">
                        <div>
                            <h3><i class="fa fa-play-circle" style="color:#ff0f28;margin-right:8px;"></i>Video Source</h3>
                            <p class="promo-subtitle">Paste your video URL. YouTube, Vimeo, Google Drive and direct video links are all supported.</p>
                        </div>
                    </div>

                    <div class="promo-form-group">
                        <label class="promo-label">Video URL <span style="color:#ff0f28;">*</span></label>
                        <input type="url" name="video_url" class="promo-input form-control"
                               value="{{ old('video_url') }}"
                               placeholder="https://www.youtube.com/watch?v=…  or  https://vimeo.com/…" required>
                        <p class="promo-input-hint">
                            <i class="fa fa-youtube-play" style="color:#ff0000;margin-right:4px;"></i> YouTube &nbsp;·&nbsp;
                            <i class="fa fa-vimeo" style="color:#1ab7ea;margin-right:4px;"></i> Vimeo &nbsp;·&nbsp;
                            <i class="fa fa-google" style="color:#4285f4;margin-right:4px;"></i> Google Drive &nbsp;·&nbsp;
                            Direct URL (MP4, HLS, etc.)
                        </p>
                    </div>

                    <div class="promo-form-group" style="margin-bottom:0;">
                        <label class="promo-label">Poster / Thumbnail URL <span style="color:rgba(255,255,255,0.3);font-weight:500;">(optional)</span></label>
                        <input type="url" name="poster_link" class="promo-input form-control"
                               value="{{ old('poster_link') }}"
                               placeholder="https://example.com/poster.jpg">
                        <p class="promo-input-hint">Leave blank — a thumbnail will be auto-fetched for YouTube & Vimeo.</p>
                    </div>
                </div>

                {{-- Section 3: Optional Links --}}
                <div class="promo-panel">
                    <div class="promo-panel-header">
                        <div>
                            <h3><i class="fa fa-link" style="color:#ff0f28;margin-right:8px;"></i>Optional Links</h3>
                            <p class="promo-subtitle">Add a crowdfunding or project website link to appear on your film's page.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="promo-form-group">
                                <label class="promo-label">Funding / Donation URL</label>
                                <input type="url" name="funding_url" class="promo-input form-control"
                                       value="{{ old('funding_url') }}"
                                       placeholder="https://kickstarter.com/…">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="promo-form-group">
                                <label class="promo-label">Project Website URL</label>
                                <input type="url" name="webpage_url" class="promo-input form-control"
                                       value="{{ old('webpage_url') }}"
                                       placeholder="https://myfilm.com">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:40px;flex-wrap:wrap;">
                    <a href="{{ URL::to('user/films') }}" class="promo-btn promo-btn-ghost">
                        <i class="fa fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="promo-btn promo-btn-primary">
                        <i class="fa fa-upload"></i> Submit Film for Review
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Please check the form',
            html: '@foreach($errors->all() as $error)<p style="margin:0;">{{ $error }}</p>@endforeach',
            confirmButtonColor: '#ff0015',
            background: '#1a1a1d',
            color: '#fff'
        });
    });
</script>
@endif
@endsection
