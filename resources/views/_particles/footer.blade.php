<!-- Start Footer Area -->
<footer>
  <div class="footer-area vfx-item-ptb">
    <div class="footer-wrapper">
      <div class="container-fluid">
        <div class="row">

        @if(getcong('footer_google_play_link')=="" AND getcong('footer_apple_store_link') =="")
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
      @else
      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
      @endif

          
            <div class="footer-bottom">
        <div class="footer-links">
          <ul>
          @foreach(\App\Pages::where('status','1')->orderBy('page_order')->get() as $page_data)
                <li><a href="{{ URL::to('page/'.$page_data->page_slug) }}" title="{{$page_data->page_title}}">{{$page_data->page_title}}</a></li>
          @endforeach                 
           
          </ul>
        </div>
        <div class="copyright-text">
          <p>{{stripslashes(getcong('site_copyright'))}}</p>
        </div>
      </div>
      </div>
      
      @if(getcong('footer_google_play_link')=="" AND getcong('footer_apple_store_link') =="")
      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
      @else
      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
      @endif    
            <div class="single-footer">
              <div class="footer-heading-wrap">
                <h3 class="footer-heading">{{trans('words.connect_with_us')}}</h3>
              </div>
              <div class="social-links">
                <ul>
                  @if(getcong('footer_fb_link'))
                  <li><a href="{{stripslashes(getcong('footer_fb_link'))}}" title="facebook"><i class="ion-social-facebook"></i></a></li>
                  @endif 

                  @if(getcong('footer_twitter_link'))
                  <li><a href="{{stripslashes(getcong('footer_twitter_link'))}}" title="twitter"><i class="ion-social-twitter"></i></a></li>
                  @endif 

                  @if(getcong('footer_instagram_link'))
                  <li><a href="{{stripslashes(getcong('footer_instagram_link'))}}" title="instagram"><i class="ion-social-instagram"></i></a></li>
                  @endif 
 
                </ul>
              </div>
            </div>
          </div>
        

          @if(getcong('footer_google_play_link') OR getcong('footer_apple_store_link'))
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="single-footer">
              <div class="footer-heading-wrap">
                <h3 class="footer-heading">{{trans('words.apps_text')}}</h3>
              </div>
              <div class="download-app-link-item"> 
                @if(getcong('footer_google_play_link'))  
                <a class="google-play-download" href="{{stripslashes(getcong('footer_google_play_link'))}}" target="_blank" title="Google Play"><img src="{{ URL::asset('site_assets/images/google-play.png') }}" alt="Google Play Download" title="Google Play Download"></a> 
                @endif

                @if(getcong('footer_apple_store_link'))  
                <a class="apple-store-download" href="{{stripslashes(getcong('footer_apple_store_link'))}}" target="_blank" title="Apple Store"><img src="{{ URL::asset('site_assets/images/app-store.png') }}" alt="Apple Store Download" title="Apple Store Download"></a>
                @endif 
              </div>
            </div>
          </div> 
          @endif   
          
          
        </div>
      </div>
    </div>
  </div>  
  
  <!-- Start Scroll Top Area -->
  <div class="scroll-top">
    <div class="scroll-icon"> <i class="fa fa-angle-up"></i> </div>
  </div>
  <!-- End Scroll Top Area -->  
  
</footer>
<!-- End Footer Area --> 

@php
    $pwa_settings = \App\PwaSettings::getSettings();
@endphp

@if($pwa_settings->pwa_enabled)
<!-- PWA Install Banner -->
<div id="pwa-install-banner" style="display:none; background: {{ $pwa_settings->theme_color }}; color: white; padding: 15px 20px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; z-index: 9999; box-shadow: 0 -2px 10px rgba(0,0,0,0.3);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 text-md-left text-center">
                <i class="fa fa-mobile" style="font-size: 24px; margin-right: 10px; vertical-align: middle;"></i>
                <strong style="font-size: 16px;">Install {{ $pwa_settings->app_name }}</strong>
                <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Get faster access, offline viewing & more!</p>
            </div>
            <div class="col-md-4 text-md-right text-center" style="margin-top: 10px;">
                <button id="pwa-install-button" style="background: white; color: {{ $pwa_settings->theme_color }}; border: none; padding: 10px 25px; border-radius: 25px; cursor: pointer; font-weight: bold; margin-right: 10px; font-size: 14px;">
                    <i class="fa fa-download"></i> Install App
                </button>
                <button onclick="document.getElementById('pwa-install-banner').style.display='none'; localStorage.setItem('pwa-banner-dismissed', Date.now());" style="background: transparent; color: white; border: 1px solid white; padding: 10px 20px; border-radius: 25px; cursor: pointer; font-size: 14px;">
                    <i class="fa fa-times"></i> Later
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Check if banner was dismissed recently (within 7 days)
    const dismissedTime = localStorage.getItem('pwa-banner-dismissed');
    const sevenDays = 7 * 24 * 60 * 60 * 1000;
    const shouldShowBanner = !dismissedTime || (Date.now() - parseInt(dismissedTime)) > sevenDays;

    window.addEventListener('beforeinstallprompt', (e) => {
        if (shouldShowBanner) {
            document.getElementById('pwa-install-banner').style.display = 'block';
        }
    });

    // Hide banner after installation
    window.addEventListener('appinstalled', () => {
        document.getElementById('pwa-install-banner').style.display = 'none';
        localStorage.removeItem('pwa-banner-dismissed');
    });
</script>
@endif 

<div id="logout_remotly" class="modal fade centered-modal in" role="dialog" aria-labelledby="logout_remotly" aria-hidden="true">  
    <div class="modal-dialog modal-dialog-centered modal-md">
       <div class="modal-content">
        <div class="modal-header">           
          <h4 class="modal-title">Remotly Logout Alert!</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Hey there! You have remotly logout from this device. You will be redirected in 5 seconds.</p>     
 
        </div>
         
      </div>      
    </div>
  </div>