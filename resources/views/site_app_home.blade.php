<!DOCTYPE html>
<html lang="{{getcong('default_language')}}">
<head>
<meta name="theme-color" content="#ff0015">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="">
<title>@yield('head_title', getcong('site_name'))</title>
<meta name="description" content="@yield('head_description', getcong('site_description'))" />
<meta name="keywords" content="@yield('head_keywords', getcong('site_keywords'))" />
<link rel="canonical" href="@yield('head_url', url('/'))">

<meta property="og:type" content="movie" />
<meta property="og:title" content="@yield('head_title',  getcong('site_name'))" />
<meta property="og:description" content="@yield('head_description', getcong('site_description'))" />
<meta property="og:image" content="@yield('head_image', URL::asset('/'.getcong('site_meta_image')))" />
<meta property="og:url" content="@yield('head_url', url('/'))" />
<meta property="og:image:width" content="1024" />
<meta property="og:image:height" content="1024" />
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="@yield('head_image', URL::asset('/'.getcong('site_meta_image')))">
<link rel="image_src" href="@yield('head_image', URL::asset('/'.getcong('site_meta_image')))">

<!-- Favicon -->
<link rel="icon" href="{{ URL::asset('/'.getcong('site_favicon')) }}">


<!-- LOAD LOCAL CSS -->
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/owl.carousel.min.css') }}">

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/megamenu.css') }}">
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/ionicons.css') }}">
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/font-awesome.min.css') }}">


<link rel="stylesheet" href="{{ URL::asset('site_assets/css/color-style/'.getcong('styling').'.css') }}" id="theme">

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/responsive.css') }}">

<!-- Splide Slider CSS -->
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/splide.min.css') }}">

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/jquery-eu-cookie-law-popup.css') }}">

<!-- SweetAlert2 -->
<script src="{{ URL::asset('site_assets/js/sweetalert2@11.js') }}"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800&display=swap" rel="stylesheet">

@if(getcong('site_header_code'))
    {!!stripslashes(getcong('site_header_code'))!!}
 @endif

 @if(getcong('styling')=="style-one")

    <?php $search_bg="#22134e";?>

 @elseif(getcong('styling')=="style-two")

    <?php $search_bg="#0d0620";?>

 @elseif(getcong('styling')=="style-three")

    <?php $search_bg="#0d071e";?>

@elseif(getcong('styling')=="style-four")

    <?php $search_bg="#0d0620";?>

@elseif(getcong('styling')=="style-five")

    <?php $search_bg="#0f0823";?>

 @else

  <?php $search_bg="#000000";?>

 @endif

 <style type="text/css">
      .search .search-input input[type=text]::placeholder, .search .search-input input[type=text].focus {
          background: {{$search_bg}} !important;
      }
 </style>

 @include('_particles.pwa_meta')

</head>
<body>


@if(!classActivePathSite('login') AND !classActivePathSite('signup') AND !classActivePathSite('password'))

    @include("_particles.header_home")
    @endif



    @yield("content")



<div id="popup1" class="popup-view popup-overlay">
  <div class="search">
    <div class="search-container has-results"><span class="title">{{trans('words.search')}}</span>
      <div class="search-input">
        <input type="text" name="s" id="search_box" class="search-container-input" placeholder="{{trans('words.title')}}" onkeyup="showSuggestions(this.value)" style="background: {{$search_bg}};">
      </div>
    </div>
    <div class="search-results mt-4" id="search_output">


    </div>
  </div>
  <a class="close" href="#" title="close"><i class="ion-close-round"></i></a>
</div>

<div class="eupopup eupopup-bottom"></div>


  <!-- Load Local JS -->
<script src="{{ URL::asset('site_assets/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/jquery.easing.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ URL::asset('site_assets/js/megamenu.js') }}"></script>


<!-- Splide Slider JS -->
<script src="{{ URL::asset('site_assets/js/splide.min.js') }}"></script>

<!-- Custom Main JS -->
<script src="{{ URL::asset('site_assets/js/custom-main.js') }}"></script>


<script src="{{ URL::asset('site_assets/js/jquery-eu-cookie-law-popup.js') }}"></script>

<script type="text/javascript">

@if(getcong('gdpr_cookie_on_off'))
  $(document).ready( function() {
  if ($(".eupopup").length > 0) {
    $(document).euCookieLawPopup().init({
       'cookiePolicyUrl' : '{{stripslashes(getcong('gdpr_cookie_url'))}}',
       'buttonContinueTitle' : '{{trans('words.gdpr_continue')}}',
       'buttonLearnmoreTitle' : '{{trans('words.gdpr_learn_more')}}',
       'popupPosition' : 'bottom',
       'colorStyle' : 'default',
       'compactStyle' : false,
       'popupTitle' : '{{stripslashes(getcong('gdpr_cookie_title'))}}',
       'popupText' : '{{stripslashes(getcong('gdpr_cookie_text'))}}'
    });
  }
});
@endif

function showSuggestions(inputString) {
  if(inputString.length <= 1){
    //document.getElementById('search_output').innerHTML = 'Search field empty!';
    document.getElementById('search_output').innerHTML = '';
  }else{
    $.ajax({
      url: "{{ URL::to('search_elastic') }}",
      method:"GET",
      data: { 's' : inputString},
      dataType:'text',
      beforeSend: function(){
      $("#search_box").css("background","{{$search_bg}} url({{ URL::asset('site_assets/images/LoaderIcon.gif') }}) no-repeat 100%");
      },
      success: function(result){
        //alert(result);
          //$("#search_output").html = result;
          $("#search_output").html(result);
          $("#search_box").css("background","{{$search_bg}}");
        }
    });
  }
}


</script>

<script type="text/javascript">

  $("li[data-path]").click(function() {

    $("head link#theme").attr("href", $(this).data("path"));
});

</script>

@if(Auth::check())

@if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin")
  @if(user_device_limit_reached(Auth::user()->id,Auth::user()->plan_id))
  <script type="text/javascript">
       //alert({{Auth::user()->id}});
    $(document).ready( function() {
      $('#user_device_list').modal('show');

    });
  </script>
  @endif
@endif

@if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin")

<script type="text/javascript">

  function executeQuery() {
  $.ajax({
    url: "{{url('check_user_remotely_logout_or_not/'.Session::getId())}}",
    success: function(data) {

      if(data=="false")
      {
         jQuery('#logout_remotly').modal('show');

         var timer = setTimeout(function() {
                  window.location="{{ URL::to('/') }}"
              }, 5000);
      }

    }
  });
  setTimeout(executeQuery, 10000); // you could choose not to continue on failure...
}

$(document).ready(function() {
  // run the first time; all subsequent calls will take care of themselves
  setTimeout(executeQuery, 10000);
});

</script>

@endif


@endif

<script>
// Stumble Button AJAX Handler
$(document).ready(function() {
    console.log('===== Stumble Button Script v3.0 Loaded (Timestamp: {{ time() }}) =====');
    console.log('Available slider containers on page:', $('.slider-area').length);
    console.log('Available rows in slider:', $('.slider-area .row').length);
    console.log('Available divs in slider row:', $('.slider-area .row > div').length);
    $('.slider-area .row > div').each(function(i) {
        console.log('Container ' + i + ' classes:', $(this).attr('class'));
    });

    function loadRandomVideo() {
        console.log('loadRandomVideo triggered');

        var $randomBtn = $('#footer-next-btn, #footer-stumble-btn, .footer-stumble-btn').first();
        var originalRandomContent = '';
        if ($randomBtn.length) {
            originalRandomContent = $randomBtn.html();
            $randomBtn.addClass('disabled').css('pointer-events', 'none');
            $randomBtn.html('<i class="fas fa-spinner fa-spin"></i> <span>Loading...</span>');
        }

        console.log('Making AJAX request...');

        $.ajax({
            url: '{{ route("ajax.random.movie") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Response received:', response);
                if (response.success) {
                    console.log('Updating player container');

                    // Get the container as a DOM element - try multiple selectors with specificity
                    var $container = null;

                    // Try main domain selector
                    $container = $('.slider-area .col-md-9.col-lg-9.col-xl-9');
                    console.log('Trying main domain selector (.col-md-9.col-lg-9.col-xl-9):', $container.length);

                    // Try subdomain selector
                    if ($container.length === 0) {
                        $container = $('.slider-area .col-lg-9.col-md-12');
                        console.log('Trying subdomain selector (.col-lg-9.col-md-12):', $container.length);
                    }

                    // Try finding any column with col-lg-9 in slider area
                    if ($container.length === 0) {
                        $container = $('.slider-area .row [class*="col-lg-9"]').first();
                        console.log('Trying col-lg-9 wildcard:', $container.length);
                    }

                    // Try finding any column with col-md-9 in slider area
                    if ($container.length === 0) {
                        $container = $('.slider-area .row [class*="col-md-9"]').first();
                        console.log('Trying col-md-9 wildcard:', $container.length);
                    }

                    // Final fallback: first direct child of row in slider-area
                    if ($container.length === 0) {
                        $container = $('.slider-area .row > div').first();
                        console.log('Trying first div fallback:', $container.length);
                    }

                    console.log('Final container found:', $container.length, $container.attr('class'));

                    if ($container.length === 0) {
                        console.error('Player container not found!');
                        console.log('Available containers:', $('.slider-area .row > div').length);
                        alert('Error: Player container not found on page. Please refresh and try again.');
                        if ($randomBtn.length) {
                            $randomBtn.removeClass('disabled').css('pointer-events', 'auto');
                            $randomBtn.html(originalRandomContent);
                        }
                        return;
                    }

                    var containerElement = $container[0];

                    // Create a temporary div to parse the HTML
                    var $temp = $('<div>').html(response.playerHtml);

                    // Extract scripts
                    var scripts = [];
                    $temp.find('script').each(function() {
                        scripts.push({
                            src: this.src,
                            text: $(this).text()
                        });
                        $(this).remove(); // Remove from HTML
                    });

                    // Set HTML without scripts first
                    containerElement.innerHTML = $temp.html();

                    // Update Footer if provided
                    if (response.footerHtml) {
                        console.log('Updating footer container');
                        var $footerContainer = $('.player-footer-section');
                        if ($footerContainer.length) {
                            $footerContainer.html(response.footerHtml);

                            // Re-initialize any listeners for the new footer content if needed
                            // For example, if the next button is inside the footer, the delegated listener on document will handle it

                            // Re-initialize News Ticker if it was updated
                            if (typeof window.initNewsTicker === 'function') {
                                setTimeout(function() {
                                    window.initNewsTicker();
                                }, 500);
                            }
                        } else {
                            console.warn('Footer container not found');
                        }
                    }

                    console.log('Found ' + scripts.length + ' scripts to execute');

                    // Execute scripts in order
                    var scriptIndex = 0;
                    function executeNextScript() {
                        if (scriptIndex >= scripts.length) {
                            console.log('All scripts executed');
                            // If footer was NOT updated, restore the stumble button state
                            if (!response.footerHtml && $randomBtn.length) {
                                $randomBtn.removeClass('disabled').css('pointer-events', 'auto');
                                $randomBtn.html(originalRandomContent);
                            }
                            return;
                        }

                        var scriptData = scripts[scriptIndex];
                        scriptIndex++;

                        if (scriptData.src) {
                            // External script
                            console.log('Loading external script:', scriptData.src);
                            $.getScript(scriptData.src)
                                .done(function() {
                                    console.log('External script loaded:', scriptData.src);
                                    executeNextScript();
                                })
                                .fail(function() {
                                    console.error('Failed to load script:', scriptData.src);
                                    executeNextScript();
                                });
                        } else {
                            // Inline script
                            console.log('Executing inline script');
                            try {
                                var script = document.createElement('script');
                                script.text = scriptData.text;
                                containerElement.appendChild(script);
                                console.log('Inline script executed');
                            } catch(e) {
                                console.error('Error executing inline script:', e);
                            }
                            executeNextScript();
                        }
                    }

                    executeNextScript();
                    $('html, body').animate({ scrollTop: 0 }, 300);
                } else {
                    console.error('Server returned success=false:', response.error);
                    alert('Error: ' + (response.error || 'Failed to load video'));

                    if ($randomBtn.length) {
                        $randomBtn.removeClass('disabled').css('pointer-events', 'auto');
                        $randomBtn.html(originalRandomContent);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
                console.error('Response Text:', xhr.responseText);

                var errorMsg = 'Failed to load video. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }

                alert(errorMsg);

                if ($randomBtn.length) {
                    $randomBtn.removeClass('disabled').css('pointer-events', 'auto');
                    $randomBtn.html(originalRandomContent);
                }
            }
        });
    }

    // Bind click event to footer stumble button
    $(document).on('click', '#footer-next-btn, #footer-stumble-btn, .footer-stumble-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        loadRandomVideo();
        return false;
    });
});
</script>

</body>
</html>
