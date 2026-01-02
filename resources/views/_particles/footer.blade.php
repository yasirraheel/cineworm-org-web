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
<style>
#pwa-install-banner {
    display: none;
    background: {{ $pwa_settings->theme_color }};
    color: white;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    box-shadow: 0 -3px 15px rgba(0,0,0,0.3);
    animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}

.pwa-banner-content {
    padding: 15px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.pwa-banner-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.pwa-banner-text {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.pwa-banner-icon {
    font-size: 32px;
    flex-shrink: 0;
}

.pwa-banner-message h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.pwa-banner-message p {
    margin: 3px 0 0 0;
    font-size: 13px;
    opacity: 0.9;
}

.pwa-banner-buttons {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}

.pwa-btn-install {
    background: white;
    color: {{ $pwa_settings->theme_color }};
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.pwa-btn-install:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.pwa-btn-later {
    background: transparent;
    color: white;
    border: 2px solid rgba(255,255,255,0.5);
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.pwa-btn-later:hover {
    border-color: white;
    background: rgba(255,255,255,0.1);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .pwa-banner-inner {
        flex-direction: column;
        text-align: center;
    }
    
    .pwa-banner-text {
        flex-direction: column;
        text-align: center;
    }
    
    .pwa-banner-icon {
        font-size: 28px;
    }
    
    .pwa-banner-message h4 {
        font-size: 15px;
    }
    
    .pwa-banner-message p {
        font-size: 12px;
    }
    
    .pwa-banner-buttons {
        width: 100%;
        flex-direction: column;
    }
    
    .pwa-btn-install,
    .pwa-btn-later {
        width: 100%;
        padding: 12px 20px;
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .pwa-banner-content {
        padding: 12px 15px;
    }
    
    .pwa-banner-icon {
        font-size: 24px;
    }
    
    .pwa-banner-message h4 {
        font-size: 14px;
    }
    
    .pwa-banner-message p {
        font-size: 11px;
    }
}
</style>

<div id="pwa-install-banner">
    <div class="pwa-banner-content">
        <div class="pwa-banner-inner">
            <div class="pwa-banner-text">
                <div class="pwa-banner-icon">
                    <i class="fa fa-mobile"></i>
                </div>
                <div class="pwa-banner-message">
                    <h4>Install {{ $pwa_settings->app_name }}</h4>
                    <p>Get faster access, offline viewing & more!</p>
                </div>
            </div>
            <div class="pwa-banner-buttons">
                <button id="pwa-install-button" class="pwa-btn-install">
                    <i class="fa fa-download"></i> Install App
                </button>
                <button onclick="document.getElementById('pwa-install-banner').style.display='none'; localStorage.setItem('pwa-banner-dismissed', Date.now());" class="pwa-btn-later">
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