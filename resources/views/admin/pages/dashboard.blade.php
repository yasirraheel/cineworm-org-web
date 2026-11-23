@extends('admin.admin_app')

@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                @if (Auth::User()->usertype == 'Admin')
                    <div class="row">

                        <div class="col-xl-3 col-md-6 d-none">
                            <a href="{{ URL::to('admin/language') }}">
                                <div class="card-box widget-user">
                                    <div class="text-center">
                                        <h2 class="text-custom" data-plugin="counterup">{{ $language }}</h2>
                                        <h5 style="color: #f9f9f9;">{{ trans('words.language_text') }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-xl-3 col-md-6">
                            <a href="{{ URL::to('admin/genres') }}">
                                <div class="card-box widget-user">
                                    <div class="text-center">
                                        <h2 class="text-pink" data-plugin="counterup">{{ $genres }}</h2>
                                        <h5 style="color: #f9f9f9;">{{ trans('words.genres_text') }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>


                        @if (getcong('menu_movies'))
                            <div class="col-xl-3 col-md-6">
                                <a href="{{ URL::to('admin/movies') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-warning" data-plugin="counterup">{{ $movies }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.movies_text') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if (getcong('menu_shows'))
                            <div class="col-xl-3 col-md-6 d-none">
                                <a href="{{ URL::to('admin/series') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-dark" data-plugin="counterup">{{ $series }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.shows_text') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if (getcong('menu_sports'))
                            <div class="col-xl-3 col-md-6 d-none">
                                <a href="{{ URL::to('admin/sports') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup">{{ $sports }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.sports_text') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if (getcong('menu_livetv'))
                            <div class="col-xl-3 col-md-6">
                                <a href="{{ URL::to('admin/live_tv') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-danger" data-plugin="counterup">{{ $livetv }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.live_tv') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <div class="col-xl-3 col-md-6">
                            <a href="{{ URL::to('admin/sub_admin') }}">
                                <div class="card-box widget-user">
                                    <div class="text-center">
                                        <h2 class="text-purple" data-plugin="counterup">{{ $users }}</h2>
                                        <h5 style="color: #f9f9f9;">{{ trans('words.users') }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-xl-3 col-md-6 d-none">
                            <a href="{{ URL::to('admin/transactions') }}">
                                <div class="card-box widget-user">
                                    <div class="text-center">
                                        <h2 class="text-primary" data-plugin="counterup">{{ $transactions }}</h2>
                                        <h5 style="color: #f9f9f9;">{{ trans('words.transactions') }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-xl-3 col-md-6 d-none">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-custom" data-plugin="counterup">{{ number_format($daily_amount, 2) }}
                                    </h2>
                                    <h5 style="color: #f9f9f9;">{{ trans('words.daily_revenue') }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 d-none">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-pink" data-plugin="counterup">{{ number_format($weekly_amount, 2) }}
                                    </h2>
                                    <h5 style="color: #f9f9f9;">{{ trans('words.weekly_revenue') }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 d-none">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-warning" data-plugin="counterup">{{ number_format($monthly_amount, 2) }}
                                    </h2>
                                    <h5 style="color: #f9f9f9;">{{ trans('words.monthly_revenue') }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 d-none">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-success" data-plugin="counterup">{{ number_format($yearly_amount, 2) }}
                                    </h2>
                                    <h5 style="color: #f9f9f9;">{{ trans('words.yearly_revenue') }}</h5>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="row d-none">
                        <div class="col-xl-12">
                            <div class="card-box">

                                <h4 class="header-title m-t-0 m-b-0">{{ trans('words.users_plan_statastics') }}</h4>
                                <p>{{ trans('words.current_year') }}</p>

                                <div class="text-center">
                                    <ul class="list-inline chart-detail-list">

                                        @foreach ($plan_list as $p_key => $plan_data)
                                            <li class="list-inline-item">
                                                <h5 style="color: {{ getStatisticsColors($p_key + 1) }};"><i
                                                        class="fa fa-circle m-r-5"></i>{{ $plan_data->plan_name }}</h5>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>

                                <div id="morris-bar-example" style="height: 300px;"></div>

                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">

                        <div class="col-xl-3 col-md-6">
                            <a href="{{ URL::to('admin/language') }}">
                                <div class="card-box widget-user">
                                    <div class="text-center">
                                        <h2 class="text-custom" data-plugin="counterup">{{ $language }}</h2>
                                        <h5 style="color: #f9f9f9;">{{ trans('words.language_text') }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-xl-3 col-md-6">
                            <a href="{{ URL::to('admin/genres') }}">
                                <div class="card-box widget-user">
                                    <div class="text-center">
                                        <h2 class="text-pink" data-plugin="counterup">{{ $genres }}</h2>
                                        <h5 style="color: #f9f9f9;">{{ trans('words.genres_text') }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>

                        @if (getcong('menu_movies'))
                            <div class="col-xl-3 col-md-6">
                                <a href="{{ URL::to('admin/movies') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-warning" data-plugin="counterup">{{ $movies }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.movies_text') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if (getcong('menu_shows'))
                            <div class="col-xl-3 col-md-6">
                                <a href="{{ URL::to('admin/series') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-dark" data-plugin="counterup">{{ $series }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.shows_text') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if (getcong('menu_sports'))
                            <div class="col-xl-3 col-md-6">
                                <a href="{{ URL::to('admin/sports') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup">{{ $sports }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.sports_text') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if (getcong('menu_livetv'))
                            <div class="col-xl-3 col-md-6">
                                <a href="{{ URL::to('admin/live_tv') }}">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-danger" data-plugin="counterup">{{ $livetv }}</h2>
                                            <h5 style="color: #f9f9f9;">{{ trans('words.live_tv') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <div class="col-xl-3 col-md-6">
                            <a href="{{ URL::to('admin/transactions') }}">
                                <div class="card-box widget-user">
                                    <div class="text-center">
                                        <h2 class="text-primary" data-plugin="counterup">{{ $transactions }}</h2>
                                        <h5 style="color: #f9f9f9;">{{ trans('words.transactions') }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                @endif

            </div>


        </div>
        @include('admin.copyright')
    </div>

@endsection
