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
@if(request()->getHost() != 'home.cineworm.org')
    @include("_particles.header")
    @endif

@endif

    @yield("content")

@if(!classActivePathSite('login') AND !classActivePathSite('signup') AND !classActivePathSite('password'))

    @include("_particles.footer")

@endif

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
    console.log('Stumble button script loaded');

    function loadRandomVideo() {
        console.log('loadRandomVideo triggered');

        var originalText = $('#stumble-text').text();
        $('#stumble-text').text('Loading...');
        $('#stumble-btn').css('pointer-events', 'none');

        // Also disable next button if it exists and show loading
        var $nextBtn = $('#footer-next-btn');
        var originalNextContent = '';
        if ($nextBtn.length) {
            originalNextContent = $nextBtn.html();
            $nextBtn.addClass('disabled').css('pointer-events', 'none');
            $nextBtn.html('<i class="fas fa-spinner fa-spin"></i> <span>Loading...</span>');
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

                    // Get the container as a DOM element - try multiple selectors
                    var $container = $('.col-md-7.col-lg-7.col-xl-7');
                    if ($container.length === 0) {
                        $container = $('.col-lg-9.col-md-12');
                    }

                    // Specific selector for Watch page
                    if ($container.length === 0) {
                        $container = $('.col-md-9.p-0');
                    }

                    // Fallback for different layouts
                    if ($container.length === 0) {
                        $container = $('.slider-area .row > div').first();
                    }

                    console.log('Found container:', $container.length);

                    if ($container.length === 0) {
                        console.error('Player container not found!');
                        alert('Error: Player container not found on page');
                        $('#stumble-text').text(originalText);
                        $('#stumble-btn').css('pointer-events', 'auto');
                        if ($nextBtn.length) {
                            $nextBtn.removeClass('disabled').css('pointer-events', 'auto');
                            $nextBtn.html(originalNextContent);
                        }
                        return;
                    }

                    var containerElement = $container[0];

                    // Save the player buttons HTML before replacing content
                    var $playerButtons = $container.find('.player-buttons-container');
                    var savedButtonsHtml = $playerButtons.length > 0 ? $playerButtons[0].outerHTML : '';

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

                    // Restore the player buttons at the top if they existed
                    if (savedButtonsHtml) {
                        $container.prepend(savedButtonsHtml);
                    }

                    // Update Footer if provided
                    if (response.footerHtml) {
                        console.log('Updating footer container');
                        var $footerContainer = $('.player-footer-section');
                        if ($footerContainer.length) {
                            $footerContainer.html(response.footerHtml);

                            // If on Watch page, ensure Next button is visible (it defaults to hidden in the partial)
                            if ($('.video-posts-video .col-md-7').length > 0) {
                                $('#footer-next-btn').show();
                            }

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
                            $('#stumble-text').text(originalText);
                            $('#stumble-btn').css('pointer-events', 'auto');
                            // If footer was NOT updated, restore next button
                            if (!response.footerHtml && $nextBtn.length) {
                                $nextBtn.removeClass('disabled').css('pointer-events', 'auto');
                                $nextBtn.html(originalNextContent);
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

                    $('#stumble-text').text(originalText);
                    $('#stumble-btn').css('pointer-events', 'auto');
                    if ($nextBtn.length) {
                        $nextBtn.removeClass('disabled').css('pointer-events', 'auto');
                        $nextBtn.html(originalNextContent);
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

                $('#stumble-text').text(originalText);
                $('#stumble-btn').css('pointer-events', 'auto');
                if ($nextBtn.length) {
                    $nextBtn.removeClass('disabled').css('pointer-events', 'auto');
                    $nextBtn.html(originalNextContent);
                }
            }
        });
    }

    // Bind click event to Stumble button
    $('#stumble-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        loadRandomVideo();
        return false;
    });

    // Bind click event to Next button (delegated since it might be dynamic)
    $(document).on('click', '#footer-next-btn', function(e) {
        e.preventDefault();
        loadRandomVideo();
        return false;
    });

    // Handle DW News Link Click (In-place loading)
    $(document).on('click', '.read-dw-news', function(e) {
        e.preventDefault();
        var link = $(this).data('link');

        // Find the parent container
        var container = $(this).closest('.news-ticker-container');
        var listView = container.find('#news-list-view');
        var detailView = container.find('#news-detail-view');
        var contentBody = container.find('#news-content-body');

        // Hide list, show detail
        listView.hide();
        detailView.css('display', 'flex');
        contentBody.html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading news...</p></div>');

        // Fetch content via AJAX
        $.ajax({
            url: '{{ url("ajax/get_news_content") }}',
            data: { url: link },
            success: function(res) {
                if(res.content) {
                    contentBody.html(res.content);
                    // Add some basic styling to the loaded content
                    contentBody.find('img').css({'max-width': '100%', 'height': 'auto', 'border-radius': '5px'});
                    contentBody.find('a').css('color', '#fe8805');
                } else {
                    contentBody.html('<p class="text-danger" style="padding: 15px;">Could not load full content. <br><a href="'+link+'" target="_blank" style="color: #fe8805;">Click here to read on DW.com</a></p>');
                }
            },
            error: function() {
                 contentBody.html('<p class="text-danger" style="padding: 15px;">Failed to load news. <br><a href="'+link+'" target="_blank" style="color: #fe8805;">Click here to read on DW.com</a></p>');
            }
        });
    });

    // Handle Back Button Click
    $(document).on('click', '#back-to-news-list', function() {
        var container = $(this).closest('.news-ticker-container');
        var listView = container.find('#news-list-view');
        var detailView = container.find('#news-detail-view');

        detailView.hide();
        listView.show();
    });
});
</script>

@if(getcong('site_footer_code'))
    {!!stripslashes(getcong('site_footer_code'))!!}
@endif



</body>
</html>
