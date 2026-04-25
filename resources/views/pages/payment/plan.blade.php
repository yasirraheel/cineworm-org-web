@extends('site_app')

@section('head_title', trans('words.subscription_plan').' | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

<style type="text/css">
  .membership-plan-list.is-current-plan {
    border: 2px solid #fe0278;
    box-shadow: 0 18px 50px rgba(254, 2, 120, 0.22);
    position: relative;
  }
  .membership-plan-current-badge {
    background: linear-gradient(90deg, #fe0278, #fe8805);
    border-radius: 0 0 12px 12px;
    color: #fff;
    display: inline-block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    left: 18px;
    padding: 6px 14px;
    position: absolute;
    top: 0;
    text-transform: uppercase;
    z-index: 2;
  }
  .membership-plan-list.is-current-plan h3 {
    padding-top: 18px;
  }
  .membership-plan-disabled-btn {
    background: #4c465f !important;
    border: 1px solid #6a647b;
    box-shadow: none !important;
    cursor: not-allowed;
    opacity: .72;
    pointer-events: none;
  }
  .membership-plan-features {
    list-style: none;
    margin: 22px auto 26px;
    max-width: 260px;
    padding: 0;
    text-align: left;
  }
  .membership-plan-features li {
    color: #ffffff;
    display: flex;
    align-items: flex-start;
    font-size: 14px;
    line-height: 22px;
    margin-bottom: 10px;
    min-height: 22px;
    padding-left: 28px;
    position: relative;
    text-transform: none;
  }
  .membership-plan-features li.plan-includes-line {
    color: #ffffff;
    font-weight: 700;
  }
  .membership-plan-features li.plan-includes-line::before {
    border-color: #fe8805;
  }
  .membership-plan-features li::before {
    border-bottom: 2px solid #fe0278;
    border-right: 2px solid #fe0278;
    box-sizing: border-box;
    content: "";
    flex: 0 0 auto;
    height: 12px;
    left: 4px;
    position: absolute;
    top: 4px;
    transform: rotate(45deg);
    width: 6px;
  }
  @media only screen and (max-width: 575px) {
    .membership-plan-features {
      max-width: 230px;
    }
  }
</style>
  
 
<!-- Start Breadcrumb -->
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-12"> 
        <h2>{{trans('words.subscription_plan')}} </h2>
        
        <nav id="breadcrumbs">
            <ul>
              <li><a href="{{ URL::to('/') }}" title="{{trans('words.home')}}">{{trans('words.home')}}</a></li>
               <li>{{trans('words.subscription_plan')}}</li>

            </ul>
          </nav>
     </div>
      </div>
    </div>
  </div>
<!-- End Breadcrumb --> 

 <!-- Start Membership Plan Page -->
<div class="vfx-item-ptb vfx-item-info">
  <div class="container-fluid">
     <div class="row">
        
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">             
                {!! $message !!}
            </div>

            <?php Session::forget('success');?>
            @endif
            @if ($message = Session::get('error'))
            <div class="alert alert-danger">            
                {!! $message !!}
            </div>
            <?php Session::forget('error');?>
            @endif
          </div>
        </div>

        @foreach($plan_list as $plan_data)
        @php
          $includedPlanNames = $plan_data->getIncludedPlanNames();
          $directFeatureLabels = $plan_data->getDirectFeatureLabels();
          $isCurrentPlan = Auth::check() && Auth::user()->plan_id == $plan_data->id;
        @endphp
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="membership-plan-list @if($isCurrentPlan) is-current-plan @endif">
            @if($isCurrentPlan)
            <div class="membership-plan-current-badge">{{trans('words.current_plan')}}</div>
            @endif
            <h3>{{$plan_data->plan_name}}</h3>
            <h1>
              <span>{{html_entity_decode(getCurrencySymbols(getcong('currency_code')))}}</span>
              @if(Session::get('coupon_percentage'))
              <?php 
                   $discount_price_less =  $plan_data->plan_price * Session::get('coupon_percentage') / 100;

                   $final_plan_price = $plan_data->plan_price - $discount_price_less;

              echo number_format($final_plan_price,2);?>
              @else
              <?php echo number_format($plan_data->plan_price,2);?>
              @endif
              
            </h1>
            <p></p>
            <h4>{{ App\SubscriptionPlan::getPlanDuration($plan_data->id) }}</h4>
            <h4>{{trans('words.plan_device_limit')}} - {{ $plan_data->plan_device_limit }}</h4>
            @if(count($includedPlanNames) || count($directFeatureLabels))
            <ul class="membership-plan-features">
              @foreach($includedPlanNames as $includedPlanName)
                <li class="plan-includes-line">Everything in {{ $includedPlanName }}</li>
              @endforeach
              @foreach($directFeatureLabels as $featureLabel)
                <li>{{ $featureLabel }}</li>
              @endforeach
            </ul>
            @endif
            @if($isCurrentPlan)
            <span class="vfx-item-btn-danger membership-plan-disabled-btn text-uppercase mb-30 d-inline-block">Active</span>
            @else
            <a href="{{ URL::to('payment_method/'.$plan_data->id) }}" class="vfx-item-btn-danger text-uppercase mb-30" title="plan">{{trans('words.select_plan')}}</a>
            @endif
          </div>
        </div>
        @endforeach  
      
      </div>
    <div class="row">
      <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
            <div class="apply-coupon-code">
            <h2>{{trans('words.have_coupon_code')}}</h2>
            {!! Form::open(array('url' => 'apply_coupon_code','class'=>'','id'=>'apply_coupon','role'=>'form')) !!}
 

              <div class="apply-now-item">
                 
                  <input type="text" name="coupon_code" id="enterCode" value="{{Session::get('coupon_code')}}" class="form-control" placeholder="" required="">
                  @if(Session::get('coupon_percentage'))
                  <button class="vfx-item-btn-danger text-uppercase" type="submit">{{trans('words.coupon_applied')}}</button>
                  @else
                  <button class="vfx-item-btn-danger text-uppercase" type="submit">{{trans('words.apply_coupon')}}</button>
                  @endif
                  
                 
              </div>
            {!! Form::close() !!}  
          </div>
        </div>    
     </div>
  </div>
</div>
<!-- End Membership Plan Page -->
 
@endsection
