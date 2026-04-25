@extends("admin.admin_app")

@section("content")

@php
  $availableFeatures = \App\SubscriptionPlan::AVAILABLE_FEATURES;
  $selectedFeatures = isset($plan_info) ? $plan_info->getDirectFeatureKeys() : [];
  $inheritedFeatures = isset($plan_info) ? $plan_info->getInheritedFeatureKeys() : [];
  $includedPlanIds = isset($plan_info) ? $plan_info->getIncludedPlanIds() : [];
  $planFeatureMap = isset($plan_list) ? $plan_list->mapWithKeys(function ($plan) {
      return [$plan->id => $plan->getEffectiveFeatureKeys()];
  }) : collect();
@endphp

<style type="text/css">
  .iframe-container {
  overflow: hidden;
  padding-top: 56.25% !important;
  position: relative;
}
 
.iframe-container iframe {
   border: 0;
   height: 100%;
   left: 0;
   position: absolute;
   top: 0;
   width: 100%;
}
.plan-features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 10px 16px;
}
.plan-feature-option {
  align-items: center;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 4px;
  display: flex;
  gap: 8px;
  margin: 0;
  min-height: 38px;
  padding: 8px 10px;
}
.plan-feature-option input {
  margin: 0;
}
.plan-feature-option.is-inherited {
  opacity: .58;
}
.plan-feature-option small {
  color: #98a6ad;
  margin-left: auto;
}
</style>
 
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box">
                 
                  <div class="row">
                     <div class="col-sm-6">
                          <a href="{{ URL::to('admin/subscription_plan') }}"><h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;"><i class="fa fa-arrow-left"></i> {{trans('words.back')}}</h4></a>
                     </div>
                     
                   </div> 

                 {!! Form::open(array('url' => array('admin/subscription_plan/add_edit_plan'),'class'=>'form-horizontal','name'=>'slider_form','id'=>'slider_form','role'=>'form','enctype' => 'multipart/form-data')) !!}  
                  
                  <input type="hidden" name="id" value="{{ isset($plan_info->id) ? $plan_info->id : null }}">
  
                   
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{trans('words.plan_name')}} *</label>
                    <div class="col-sm-8">
                      <input type="text" name="plan_name" value="{{ isset($plan_info->plan_name) ? $plan_info->plan_name : null }}" class="form-control">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{trans('words.duration')}} *</label>
                    <div class="col-sm-4">
                      <input type="number" name="plan_duration" value="{{ isset($plan_info->plan_duration) ? $plan_info->plan_duration : null }}" class="form-control" placeholder="7">
                    </div>
                    <div class="col-sm-4">
                        <select name="plan_duration_type" class="form-control">
                         <option value="1" @if(isset($plan_info->plan_duration_type) AND $plan_info->plan_duration_type=='1') selected @endif>Day(s)</option>
                         <option value="30" @if(isset($plan_info->plan_duration_type) AND $plan_info->plan_duration_type=='30') selected @endif>Month(s)</option>
                         <option value="365" @if(isset($plan_info->plan_duration_type) AND $plan_info->plan_duration_type=='365') selected @endif>Year(s)</option>
                        </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{trans('words.price')}} *</label>
                    <div class="col-sm-8">
                      <input type="number" name="plan_price" value="{{ isset($plan_info->plan_price) ? $plan_info->plan_price : null }}" class="form-control" placeholder="9.99" step="0.01" min="0">
                      <small id="emailHelp" class="form-text text-muted mb-2">The minimum amount for processing a transaction through Stripe in USD is $0.50. For more info <a href="https://support.chargebee.com/support/solutions/articles/228511-transaction-amount-limit-in-stripe" target="_blank">click here</a></small>
                    </div>
                  </div>   

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{trans('words.plan_device_limit')}} *</label>
                    <div class="col-sm-8">
                      <input type="text" name="plan_device_limit" value="{{ isset($plan_info->plan_device_limit) ? $plan_info->plan_device_limit : null }}" class="form-control" placeholder="1" min="1">
                       
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Includes Plans</label>
                    <div class="col-sm-8">
                      <select name="included_plan_ids[]" id="included_plan_ids" class="form-control" multiple size="5">
                        @if(isset($plan_list))
                          @foreach($plan_list as $plan_data)
                            <option value="{{ $plan_data->id }}" @if(in_array($plan_data->id, $includedPlanIds, true)) selected @endif>Everything in {{ $plan_data->plan_name }}</option>
                          @endforeach
                        @endif
                      </select>
                      <small class="form-text text-muted mb-2">Hold Ctrl/Cmd to select multiple plans. Features from selected plans will be included automatically and locked below so you can add only extra features.</small>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Plan Features</label>
                    <div class="col-sm-8">
                      <div class="plan-features-grid" id="plan_features_grid">
                        @foreach($availableFeatures as $featureKey => $featureLabel)
                          <label class="plan-feature-option @if(in_array($featureKey, $inheritedFeatures, true)) is-inherited @endif" data-feature-key="{{ $featureKey }}">
                            <input type="checkbox" name="features[]" value="{{ $featureKey }}" @if(in_array($featureKey, array_merge($selectedFeatures, $inheritedFeatures), true)) checked @endif @if(in_array($featureKey, $inheritedFeatures, true)) disabled @endif>
                            <span>{{ $featureLabel }}</span>
                            <small @if(!in_array($featureKey, $inheritedFeatures, true)) style="display:none;" @endif>Included</small>
                          </label>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{trans('words.ads_on_off')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="ads_on_off">                               
                                <option value="1" @if(isset($plan_info->ads_on_off) AND $plan_info->ads_on_off==1) selected @endif>{{trans('words.on')}}</option>
                                <option value="0" @if(isset($plan_info->ads_on_off) AND $plan_info->ads_on_off==0) selected @endif>{{trans('words.off')}}</option>                            
                            </select>
                      </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">{{trans('words.status')}}</label>
                      <div class="col-sm-8">
                            <select class="form-control" name="status">                               
                                <option value="1" @if(isset($plan_info->status) AND $plan_info->status==1) selected @endif>{{trans('words.active')}}</option>
                                <option value="0" @if(isset($plan_info->status) AND $plan_info->status==0) selected @endif>{{trans('words.inactive')}}</option>                            
                            </select>
                      </div>
                  </div>

                  <div class="form-group row mb-0">
                     
                  </div>

                  <div class="form-group">
                    <div class="offset-sm-2 col-sm-9 pl-1">
                      <button type="submit" class="btn btn-primary waves-effect waves-light"> {{trans('words.save')}} </button>                      
                    </div>
                  </div>
                {!! Form::close() !!} 
              </div>
            </div>            
          </div>              
        </div>
      </div>
      @include("admin.copyright") 
    </div> 
 
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
        title: '{{ Session::get('flash_message') }}'
      })     
     
  @endif

  @if (count($errors) > 0)
                  
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '<p>@foreach ($errors->all() as $error) {{$error}}<br/> @endforeach</p>',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background:"#1a2234",
            color:"#fff"
           }) 
  @endif

  </script>    
  <script type="text/javascript">
    (function() {
      var planFeatureMap = @json($planFeatureMap);
      var originalDirectFeatures = @json($selectedFeatures);
      var featureGrid = document.getElementById('plan_features_grid');
      var includedPlanSelect = document.getElementById('included_plan_ids');

      function updateInheritedFeatures() {
        if (!featureGrid || !includedPlanSelect) {
          return;
        }

        var selectedPlanIds = Array.prototype.map.call(includedPlanSelect.selectedOptions, function(option) {
          return option.value;
        });
        var inheritedFeatures = [];

        selectedPlanIds.forEach(function(planId) {
          inheritedFeatures = inheritedFeatures.concat(planFeatureMap[planId] || []);
        });

        inheritedFeatures = inheritedFeatures.filter(function(featureKey, index) {
          return inheritedFeatures.indexOf(featureKey) === index;
        });

        featureGrid.querySelectorAll('.plan-feature-option').forEach(function(option) {
          var input = option.querySelector('input[type="checkbox"]');
          var note = option.querySelector('small');
          var featureKey = option.getAttribute('data-feature-key');
          var isInherited = inheritedFeatures.indexOf(featureKey) !== -1;
          var wasInherited = option.classList.contains('is-inherited');

          input.disabled = isInherited;
          option.classList.toggle('is-inherited', isInherited);

          if (isInherited) {
            input.checked = true;
            note.style.display = '';
          } else {
            if (wasInherited && originalDirectFeatures.indexOf(featureKey) === -1) {
              input.checked = false;
            }
            note.style.display = 'none';
          }
        });
      }

      if (includedPlanSelect) {
        includedPlanSelect.addEventListener('change', updateInheritedFeatures);
        updateInheritedFeatures();
      }
    })();
  </script>
 

@endsection
