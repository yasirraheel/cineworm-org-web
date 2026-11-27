@extends('site_app')

@section('head_title', 'Watching ' . stripslashes($movie->video_title) . ' | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="vfx-item-ptb">
    <div class="container-fluid">
        <div class="table-wrapper">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <h2>{{ stripslashes($movie->video_title) }}</h2>
                <a href="{{ route('creator-films.show', ['slug' => $movie->video_slug, 'id' => $movie->id]) }}">Back to film</a>
            </div>
            <video controls playsinline preload="metadata" style="width:100%;max-height:75vh;background:#000;"
                   poster="{{ URL::to('/' . ($movie->video_image ?: 'site_assets/images/poster_placeholder.png')) }}">
                <source src="{{ route('creator-films.stream', $movie->id) }}" type="video/mp4">
                Your browser does not support video playback.
            </video>
        </div>
    </div>
</div>
@endsection
