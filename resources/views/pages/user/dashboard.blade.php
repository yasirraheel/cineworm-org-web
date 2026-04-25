@extends('site_app')

@section('head_title', trans('words.dashboard_text').' | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

<style>
  .dashboard-card-grid {
    display: flex;
    flex-wrap: wrap;
  }

  .dashboard-card-col {
    margin-bottom: 30px;
  }

  .dashboard-card-col .member-ship-option {
    height: 100%;
  }

  .dashboard-card-action {
    display: block;
    width: 100%;
    text-align: center;
    padding: 12px;
    box-sizing: border-box;
    text-decoration: none;
  }

  @media only screen and (max-width: 991px) {
    .dashboard-card-col {
      margin-bottom: 25px;
    }
  }
</style>


<!-- Start Breadcrumb -->
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xl-12">
        <h2>{{trans('words.dashboard_text')}}</h2>
        <nav id="breadcrumbs">
          <ul>
            <li><a href="{{ URL::to('/') }}" title="{{trans('words.home')}}">{{trans('words.home')}}</a></li>
            <li>{{trans('words.dashboard_text')}}</li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Dashboard Page -->
<div class="vfx-item-ptb vfx-item-info">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


        <div class="profile-section">
          <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
              <div class="img-profile">
                @if(Auth::User()->user_image)
                <img src="{{ URL::asset('upload/'.Auth::User()->user_image) }}" class="img-rounded" alt="profile pic" title="profile pic">
                @else
                <img src="{{ URL::asset('site_assets/images/user-avatar.png') }}" class="img-rounded" alt="profile_img" title="profile pic">
                @endif

              </div>
              <div class="profile_title_item">
                <h5>{{Auth::User()->name}}</h5>
                <p>{{Auth::User()->email}}</p>
                <a href="{{ URL::to('profile') }}" class="vfx-item-btn-danger text-uppercase"><i class="fa fa-edit"></i>{{trans('words.edit')}}</a><br /><br />

                <a href="#" class="vfx-item-btn-danger text-uppercase data_remove"><i class="fa fa-trash"></i>Account Delete</a>
              </div>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
              <div class="row dashboard-card-grid">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 dashboard-card-col">
                  <div class="member-ship-option">
                    <h5 class="color-up">Stats</h5>
                    <span class="premuim-memplan-bold-text"><strong>Joined Since:</strong>
                        <span>{{ date('F, d, Y',strtotime(Auth::User()->created_at)) }}</span>
                    </span>
                    <span class="premuim-memplan-bold-text"><strong>Total Films:</strong>
                        <span>{{ \App\Movies::where('added_by', Auth::user()->id)->count() }}</span>
                    </span>
                    <span class="premuim-memplan-bold-text"><strong>Active Device:</strong>
                        <span>{{ \App\UsersDeviceHistory::where('user_id', Auth::user()->id)->first()->user_device_name ?? 'No Active Device' }}</span>
                    </span>
                  </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 dashboard-card-col">
                  <div class="member-ship-option">
                    <h5 class="color-up">{{trans('words.my_subscription')}}</h5>
                    @if($currentPlan)
                    <span class="premuim-memplan-bold-text"><strong>{{trans('words.current_plan')}}:</strong><span>{{ $currentPlan->plan_name }}</span></span>
                    @if($user->start_date)
                    <span class="premuim-memplan-bold-text"><strong>Plan Started:</strong><span>{{ date('F, d, Y',$user->start_date) }}</span></span>
                    @endif
                    @if($user->exp_date)
                    <span class="premuim-memplan-bold-text"><strong>{{trans('words.subscription_expires_on')}}:</strong><span>{{ date('F, d, Y',$user->exp_date) }}</span></span>
                    @endif
                    @if(!empty($currentPlan->plan_price) || $currentPlan->plan_price === '0' || $currentPlan->plan_price === 0)
                    <span class="premuim-memplan-bold-text"><strong>Plan Price:</strong><span>{{ html_entity_decode(getcong('currency_sign')) }} {{ number_format((float) $currentPlan->plan_price, 2) }}</span></span>
                    @endif
                    <div class="mt-3"><a href="{{ URL::to('membership_plan') }}" class="vfx-item-btn-danger text-uppercase">{{trans('words.upgrade_plan')}}</a></div>
                    @else
                    <span class="premuim-memplan-bold-text"><strong>{{trans('words.current_plan')}}:</strong><span>No Plan Selected</span></span>
                    <div class="mt-3"><a href="{{ URL::to('membership_plan') }}" class="vfx-item-btn-danger text-uppercase">{{trans('words.select_plan')}}</a></div>
                    @endif
                  </div>
                </div>
                @foreach($dashboardCards as $dashboardCard)
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 dashboard-card-col">
                  <div class="member-ship-option">
                    <h5 class="color-up">{{ $dashboardCard['title'] }}</h5>
                    <div class="mt-3">
                      <a href="{{ $dashboardCard['url'] }}" class="vfx-item-btn-danger text-uppercase dashboard-card-action">{{ $dashboardCard['button_text'] }}</a>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-wrapper">
          <div class="vfx-item-section">
            <h3>Watch History</h3>
          </div>

          @if ($recently_watched->count() > 0)
            <div class="recently-watched-video-carousel owl-carousel">
              @foreach ($recently_watched as $i => $watched_videos)
                  <div class="single-video">
                      @if ($watched_videos->video_type == 'Movies')
                          @php
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <a href="{{ URL::to('movies/details/' . $info->video_slug . '/' . $info->id) }}"
                                  title="{{ $info->video_title }}">
                                  <div class="video-img">
                                      <span class="video-item-content">{{ $info->video_title }}</span>
                                      <img src="{{ URL::to('/' . $info->video_image) }}"
                                          alt="{{ $info->video_title }}"
                                          title="Movies-{{ $info->video_title }}">
                                  </div>
                              </a>
                          @endif
                      @endif

                      @if ($watched_videos->video_type == 'Episodes')
                          @php
                              $episode_series_id = \App\Episodes::getEpisodesInfo(
                                  $watched_videos->video_id,
                                  'episode_series_id',
                              );
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <div class="single-video">
                                  <a href="{{ URL::to('shows/' . \App\Series::getSeriesInfo($episode_series_id, 'series_slug') . '/' . $info->video_slug . '/' . $info->id) }}"
                                      title="{{ $info->video_title }}">
                                      <div class="video-img">
                                          <span class="video-item-content">{{ $info->video_title }}</span>
                                          <img src="{{ URL::to('/' . $info->video_image) }}"
                                              alt="{{ $info->video_title }}"
                                              title="Episodes-{{ $info->video_title }}">
                                      </div>
                                  </a>
                              </div>
                          @endif
                      @endif

                      @if ($watched_videos->video_type == 'Sports')
                          @php
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <div class="single-video">
                                  <a href="{{ URL::to('sports/details/' . $info->video_slug . '/' . $info->id) }}"
                                      title="{{ $info->video_title }}">
                                      <div class="video-img">
                                          <span class="video-item-content">{{ $info->video_title }}</span>
                                          <img src="{{ URL::to('/' . $info->video_image) }}"
                                              alt="{{ $info->video_title }}"
                                              title="Sports-{{ $info->video_title }}">
                                      </div>
                                  </a>
                              </div>
                          @endif
                      @endif

                      @if ($watched_videos->video_type == 'LiveTV')
                          @php
                              $info = recently_watched_info(
                                  $watched_videos->video_type,
                                  $watched_videos->video_id,
                              );
                          @endphp
                          @if ($info)
                              <div class="single-video">
                                  <a href="{{ URL::to('livetv/details/' . $info->channel_slug . '/' . $info->id) }}"
                                      title="{{ $info->channel_name }}">
                                      <div class="video-img">
                                          <span class="video-item-content">{{ $info->channel_name }}</span>
                                          <img src="{{ URL::to('/' . $info->channel_thumb) }}"
                                              alt="{{ $info->channel_name }}"
                                              title="LiveTV-{{ $info->channel_name }}">
                                      </div>
                                  </a>
                              </div>
                          @endif
                      @endif
                  </div>
              @endforeach
            </div>

            <div class="col-xs-12">
              @include('_particles.pagination', ['paginator' => $transactions_list])
            </div>
          @else
            <p class="mb-0">No watch history found yet.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Dashboard Page -->

@include("pages.user.logged_in_device_list")

<script src="{{ URL::asset('site_assets/js/jquery-3.3.1.min.js') }}"></script>

<script type="text/javascript">
  @if(Session::has('flash_message'))

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'success',
    title: '{{ Session::get('
    flash_message ') }}'
  })

  @endif

  @if(Session::has('success'))

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'success',
    title: '{{ Session::get('
    success ') }}'
  })

  @endif

  @if(Session::has('error_flash_message'))

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'error',
    title: '{{ Session::get('
    error_flash_message ') }}'
  })

  @endif


  $(".data_remove").click(function(event) {
  event.preventDefault(); // Prevent the default anchor behavior

  Swal.fire({
    title: '{{trans('words.dlt_warning')}}',
    text: "{{trans('words.user_dlt_confirm')}}",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: '{{trans('words.dlt_confirm')}}',
    cancelButtonText: "{{trans('words.btn_cancel')}}",
    background: "#1a2234",
    color: "#fff"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: 'get',
        url: "{{ URL::to('account_delete') }}",
        dataType: 'json',
        data: {
          id: '' // Add the necessary ID if needed
        },
        success: function(res) {
          // Handle the response
          if (res.status == '1') {
            Swal.fire({
              position: 'center',
              icon: 'success',
              title: '{{trans('words.deleted')}}!',
              text: '{{trans('words.user_dlt_success')}}',
              showConfirmButton: true,
              confirmButtonColor: '#10c469',
              background: "#1a2234",
              color: "#fff"
            }).then(function() {
              window.location = "{{ URL::to('/') }}"; // Redirect or refresh
            });
          } else {
            Swal.fire({
              position: 'center',
              icon: 'error',
              title: 'Something went wrong!',
              showConfirmButton: true,
              confirmButtonColor: '#10c469',
              background: "#1a2234",
              color: "#fff"
            });
          }
        }
      });
    }
  });
});

</script>

@endsection
