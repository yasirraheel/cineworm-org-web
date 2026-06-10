@extends('site_app')

@section('head_title', 'My News Tickers | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>My News Tickers</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li>My News Tickers</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="edit-profile-area vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                @include('pages.user._sidebar')
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    <div class="edit-profile-form">
                        
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-6">
                                <h3 style="color:#fff;margin-bottom:5px;"><i class="fa fa-newspaper-o" style="color:#e50914;margin-right:8px;"></i> News Tickers</h3>
                                <p style="color:#ccc;font-size:14px;">Manage your news ticker submissions. All news requires admin approval.</p>
                            </div>
                            <div class="col-md-6 text-right" style="text-align: right; padding-top: 10px;">
                                <a href="{{ URL::to('user/news_tickers/create') }}" class="vfx-item-btn-danger text-uppercase" style="text-decoration:none;">
                                    <i class="fa fa-plus"></i> Submit News Ticker
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" style="color: #fff; border-color: rgba(255,255,255,0.1);">
                                <thead>
                                    <tr>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Headline</th>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Breaking</th>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Status</th>
                                        <th style="border-color: rgba(255,255,255,0.1); color:#fff;">Submitted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($news_tickers as $news)
                                        <tr>
                                            <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">{{ $news->headline }}</td>
                                            <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">
                                                @if($news->is_breaking)
                                                    <span class="badge" style="background-color: #e50914; padding: 5px 10px;">Breaking</span>
                                                @else
                                                    Normal
                                                @endif
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.1);">
                                                @if($news->status == 1)
                                                    <span class="badge" style="background-color: #28a745; padding: 5px 10px;">Approved</span>
                                                @else
                                                    <span class="badge" style="background-color: #ffc107; color:#000; padding: 5px 10px;">Pending</span>
                                                @endif
                                            </td>
                                            <td style="border-color: rgba(255,255,255,0.1); color:#ccc;">{{ $news->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center" style="border-color: rgba(255,255,255,0.1); padding: 30px;">
                                                <i class="fa fa-newspaper-o" style="font-size:32px;display:block;margin-bottom:14px;opacity:0.2;"></i>
                                                No news tickers submitted yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div style="margin-top:20px;">
                            @include('_particles.pagination', ['paginator' => $news_tickers])
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
