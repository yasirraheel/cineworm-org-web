<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="description" content="">
<meta name="msapplication-tap-highlight" content="yes" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Email Verification</title>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style type="text/css">
@import url(https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap);
body {margin: 0; padding: 0; min-width: 100%!important;}
img {height: auto;}
a {transition: 0.4s;}
a:active, a:hover {outline: 0;transition: 0.4s;}
.content {width: 100%; max-width: 520px;}
.header {padding: 40px 30px 20px 30px;}
.innerpadding {padding:25px 30px;}

@media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
.innerpadding {padding:30px 20px;}
body[yahoo] .hide {display: none!important;}
.h1 {font-size: 26px;line-height: 36px;}
}
</style>
</head>

<body yahoo bgcolor="#ffffff">
<div style="word-spacing:normal;background-color:#efefef"><div class="adM">
</div><div style="background-color:#efefef"><div class="adM">
</div><div style="margin:0px auto;max-width:600px"><div class="adM">
</div><table style="width:100%" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="direction:ltr;font-size:0px;padding:0;text-align:center">
<div style="margin:0px auto;max-width:600px">
<table style="width:100%" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="direction:ltr;font-size:0px;padding:0;text-align:center">
<div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
<table style="vertical-align:top" role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td style="font-size:0px;padding:0;word-break:break-word" align="center">
<table style="min-width:100%;max-width:100%;border-collapse:collapse;border-spacing:0px" role="presentation" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td><a href="{{ url('/') }}" target="_blank"><img style="border:0;display:block;outline:none;text-decoration:none;height:auto;min-width: 20%;width: 20%;max-width: 100%;font-size: 13px;margin: 0 auto;padding: 25px;" src="{{ URL::asset('/'.getcong('site_logo')) }}" width="200" height="25" style="width:200px;height:25px"></a></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>

<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px">
<table style="background:#ffffff;background-color:#ffffff;width:100%" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="direction:ltr;font-size:0px;padding:5px 5px 5px 5px;text-align:center">
<div style="margin:0px auto;max-width:590px">
<table style="width:100%" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="direction:ltr;font-size:0px;padding:5px 5px 5px 5px;text-align:center">
<div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
<table style="vertical-align:top" role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td dir="ltr" style="font-size:0px;padding:5px 5px 10px 5px;word-break:break-word" align="left">
<div style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif;font-size:22px;font-weight:900;line-height:28px;text-align:left;color:#000000">Verify Your Email Address</div>
</td>
</tr>
<tr>
<td dir="ltr" style="font-size:0px;padding:5px 5px 5px 5px;word-break:break-word" align="left">
<div style="font-family:Helvetica Neue, Helvetica, Arial;font-size:16px;line-height:24px;text-align:left;color:#282828">Hi {{$name}},</div>
</td>
</tr>
<tr>
<td dir="ltr" style="font-size:0px;padding:5px 5px 5px 5px;word-break:break-word" align="left">
<div style="font-family:Helvetica Neue, Helvetica;font-size:16px;line-height:24px;text-align:left;color:#282828">
Thank you for signing up for {{getcong('site_name')}}! To complete your registration and access all features, please verify your email address by clicking the button below.
</div>
</td>
</tr>
<tr>
<td dir="ltr" style="font-size:0px;padding:20px 5px 20px 5px;word-break:break-word" align="center">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
<td style="border-radius:5px;background:#fe0531;text-align:center;">
<a href="{{$verificationUrl}}" target="_blank" style="background:#fe0531;border:15px solid #fe0531;border-radius:5px;color:#ffffff;display:inline-block;font-family:Helvetica Neue,Helvetica,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:1.5;text-align:center;text-decoration:none;-webkit-text-size-adjust:none;mso-hide:all;">Verify Email Address</a>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td dir="ltr" style="font-size:0px;padding:15px 5px 5px 5px;word-break:break-word" align="left">
<div style="font-family:Helvetica Neue, Helvetica;font-size:14px;line-height:22px;text-align:left;color:#666666">
If the button above doesn't work, copy and paste the following link into your browser:
<br><br>
<a href="{{$verificationUrl}}" style="color:#fe0531;word-break:break-all;">{{$verificationUrl}}</a>
</div>
</td>
</tr>
<tr>
<td dir="ltr" style="font-size:0px;padding:15px 5px 5px 5px;word-break:break-word" align="left">
<div style="font-family:Helvetica Neue, Helvetica;font-size:14px;line-height:22px;text-align:left;color:#666666">
This verification link will expire in 60 minutes for security reasons.
</div>
</td>
</tr>
<tr>
<td dir="ltr" style="font-size:0px;padding:15px 5px 5px 5px;word-break:break-word" align="left">
<div style="font-family:Helvetica Neue, Helvetica;font-size:14px;line-height:22px;text-align:left;color:#666666">
If you did not create an account, no further action is required.
</div>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>

<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px">
<table style="background:#ffffff;background-color:#ffffff;width:100%" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="direction:ltr;font-size:0px;padding:5px 5px 5px 5px;text-align:center">
<div style="margin:0px auto;max-width:590px">
<table style="width:100%" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="direction:ltr;font-size:0px;padding:0 0 0 0px;padding-bottom:0;padding-right:0;padding-top:0;text-align:center">
<div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
<table style="vertical-align:top" role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td style="font-size:0px;padding:0;word-break:break-word" align="center">
<p style="border-top:solid 1px #1e2026;font-size:1px;margin:0px auto;width:100%">&nbsp;</p>
</td>
</tr>
<tr>
<td dir="ltr" style="font-size:0px;padding:5px 5px 5px 5px;word-break:break-word" align="center">
<div style="font-family:BinancePlex,Arial,PingFangSC-Regular,'Microsoft YaHei',sans-serif;font-size:18px;font-weight:800;line-height:30px;text-align:center;color:#343565">Stay Connected</div>
</td>
</tr>
<tr>
<td style="font-size:0px;padding:0;word-break:break-word" align="center">

<table style="float:none;display:inline-table" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="padding:4px;vertical-align:middle">
<table style="" role="presentation" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td style="font-family:Helvetica Neue, Helvetica;color:#1e2026;font-weight:500;font-size: 14px;">
<a href="{{stripslashes(getcong('footer_fb_link'))}}" target="_blank" style="color:#1e2026">Facebook</a> |
<a href="{{stripslashes(getcong('footer_instagram_link'))}}" target="_blank" style="color:#1e2026">Instagram</a> |
<a href="{{stripslashes(getcong('footer_twitter_link'))}}" target="_blank" style="color:#1e2026">Twitter</a>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>

<div style="margin:0px auto;max-width:590px">
<table style="width:100%" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
<td style="direction:ltr;font-size:0px;padding:5px 5px 5px 5px;text-align:center">
<div style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
<table style="vertical-align:top" role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td dir="ltr" style="font-size:0px;padding:25px 5px 15px 5px;word-break:break-word" align="center">
<div style="font-family:Helvetica Neue, Helvetica;font-size:14px;font-weight:500;line-height:16px;text-align:center;color:#1e2026">© {{date('Y')}} {{getcong('site_name')}}, All Rights Reserved.</div>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>

</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</body>
</html>
