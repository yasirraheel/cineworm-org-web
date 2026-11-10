@extends('site_app')

@section('head_title', $home_section->section_name . ' | ' . getcong('site_name'))

@section('head_url', Request::url())

@section('content')
    <div class="breadcrumb-section bg-xs" style="background-image: url({{ URL::asset('site_assets/images/breadcrum-bg.jpg') }})">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2>{{ stripslashes($home_section->section_name) }}</h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ URL::to('/') }}" title="{{ trans('words.home') }}">{{ trans('words.home') }}</a></li>
                            <li>{{ stripslashes($home_section->section_name) }}</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="view-all-video-area vfx-item-ptb">
        <div class="container-fluid">
            <div class="row">
                @foreach (explode(',', (string) $home_section->tv_ids) as $tv_id)
                    @php($tv = App\LiveTV::where('id', trim($tv_id))->where('status', 1)->first())
                    @if ($tv)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <div class="single-video">
                                <a href="{{ URL::to('livetv/details/' . $tv->channel_slug . '/' . $tv->id) }}"
                                    title="{{ $tv->channel_name }}">
                                    <div class="video-img">
                                        @if ($tv->channel_access == 'Paid')
                                            <div class="vid-lab-premium">
                                                <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                    alt="ic-premium" title="ic-premium">
                                            </div>
                                        @endif
                                        <span class="video-item-content">{{ $tv->channel_name }}</span>
                                        <img src="{{ URL::to('/' . $tv->channel_thumb) }}"
                                            alt="{{ $tv->channel_name }}" title="{{ $tv->channel_name }}" />
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
