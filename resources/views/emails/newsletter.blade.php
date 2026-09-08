<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? 'Newsletter' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style type="text/css">
body { margin: 0; padding: 0; min-width: 100%!important; background-color: #0d0d0f; font-family: 'Rubik', Helvetica, Arial, sans-serif; color: #d0d0d5; }
img { height: auto; max-width: 100%; }
a { color: #ff4d00; text-decoration: none; }
a:hover { text-decoration: underline; }
.container { max-width: 620px; margin: 0 auto; background-color: #17171a; border: 1px solid #28282c; border-radius: 8px; overflow: hidden; }
.header { background-color: #111114; padding: 25px 30px; text-align: center; border-bottom: 1px solid #28282c; }
.content-body { padding: 35px 30px; font-size: 15px; line-height: 1.7; color: #d6d6dc; }
.content-body h1, .content-body h2, .content-body h3 { color: #ffffff; margin-top: 0; }
.content-body p { margin-bottom: 16px; }
.content-body img { border-radius: 6px; }
.btn-cta { display: inline-block; background-color: #ff4d00; color: #ffffff !important; padding: 12px 24px; border-radius: 4px; font-weight: 600; text-decoration: none; margin: 15px 0; }
.btn-cta:hover { background-color: #e04400; }
.footer { background-color: #111114; padding: 25px 30px; text-align: center; font-size: 12px; color: #767682; border-top: 1px solid #28282c; }
.footer a { color: #9c9ca8; text-decoration: underline; }
@media only screen and (max-width: 640px) {
  .container { width: 100% !important; border-radius: 0; border: none; }
  .content-body { padding: 25px 18px; }
  .header { padding: 20px 18px; }
  .footer { padding: 20px 18px; }
}
</style>
</head>
<body bgcolor="#0d0d0f" style="margin: 0; padding: 25px 0; background-color: #0d0d0f;">
  <div class="container" style="max-width: 620px; margin: 0 auto; background-color: #17171a; border: 1px solid #28282c; border-radius: 8px; overflow: hidden;">
    
    <!-- Header with Logo -->
    <div class="header" style="background-color: #111114; padding: 25px 30px; text-align: center; border-bottom: 1px solid #28282c;">
      <a href="{{ url('/') }}" target="_blank">
        @if(getcong('site_logo'))
          <img src="{{ URL::asset('/'.getcong('site_logo')) }}" alt="{{ getcong('site_name') }}" style="max-height: 40px; width: auto;" />
        @else
          <h2 style="color: #ffffff; margin: 0;">{{ getcong('site_name') }}</h2>
        @endif
      </a>
    </div>

    <!-- Main Message Body -->
    <div class="content-body" style="padding: 35px 30px; font-size: 15px; line-height: 1.7; color: #d6d6dc;">
      @if(!empty($name) && $name != 'Subscriber')
        <p style="font-size: 16px; color: #ffffff; font-weight: 500;">Hello {{ $name }},</p>
      @endif

      <div class="message-text">
        {!! $body_content !!}
      </div>
    </div>

    <!-- Footer with Unsubscribe -->
    <div class="footer" style="background-color: #111114; padding: 25px 30px; text-align: center; font-size: 12px; color: #767682; border-top: 1px solid #28282c;">
      <p style="margin: 0 0 8px 0;">&copy; {{ date('Y') }} <a href="{{ url('/') }}" style="color: #9c9ca8;">{{ getcong('site_name') }}</a>. All rights reserved.</p>
      <p style="margin: 0; color: #666672;">
        You received this email because you are subscribed to {{ getcong('site_name') }} newsletter.
        @if(!empty($unsubscribe_url))
          <br>Want out of the loop? <a href="{{ $unsubscribe_url }}" style="color: #ff4d00;">Unsubscribe here</a>.
        @endif
      </p>
    </div>

  </div>
</body>
</html>
