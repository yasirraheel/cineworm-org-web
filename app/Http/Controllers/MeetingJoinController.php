<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LiveBroadcast;
use Illuminate\Support\Str;

class MeetingJoinController extends Controller
{
    /**
     * Handle guest/subscriber meeting join URL (https://cineworm.org/meeting/join/{roomId})
     */
    public function join(Request $request, $roomId)
    {
        // Require authentication on cineworm.org
        if (!Auth::check()) {
            // Save return URL so user is redirected back to meeting after login
            session(['url.intended' => $request->fullUrl()]);
            \Session::flash('error_flash_message', 'Please log in or sign up on CineWorm to join this live meeting.');
            return redirect('login');
        }

        $user = Auth::user();
        $roomId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $roomId);

        if (empty($roomId)) {
            \Session::flash('error_flash_message', 'Invalid meeting room ID.');
            return redirect('dashboard');
        }

        // Find broadcast meeting record if exists
        $broadcast = LiveBroadcast::where('zoom_meeting_id', $roomId)->first();
        
        $meetingTitle = $broadcast ? $broadcast->title : 'Live Meeting Room';
        $hostUser     = $broadcast ? $broadcast->user : null;

        // Password protection check
        $dbPassword = $broadcast ? $broadcast->zoom_meeting_password : '';
        $roomPassword = $request->get('roomPassword', $dbPassword);

        $cinemeetBaseUrl = rtrim(env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org'), '/');

        // HMAC signature token for CineMeet iframe verification
        $secret  = env('CINEMEET_SECRET', 'cineworm_secret_key_2026');
        $expires = time() + 14400; // 4 hours valid
        $tokenData = "{$roomId}.{$user->id}.{$expires}";
        $hmac     = hash_hmac('sha256', $tokenData, $secret);
        $signedToken = "{$tokenData}.{$hmac}";

        // Encode user credentials
        $nameEncoded   = urlencode($user->name ?? 'User-' . rand(1000, 9999));
        $avatarUrl     = $user->user_icon ? asset($user->user_icon) : '';
        $avatarEncoded = urlencode($avatarUrl);
        $passParam     = !empty($roomPassword) ? ('&roomPassword=' . urlencode($roomPassword)) : '&roomPassword=0';

        $audioParam    = $request->get('audio', '1');
        $videoParam    = $request->get('video', '1');
        $screenParam   = $request->get('screen', '1');
        $chatParam     = $request->get('chat', '1');

        $cinemeetEmbedUrl = "{$cinemeetBaseUrl}/join?room={$roomId}&name={$nameEncoded}&avatar={$avatarEncoded}{$passParam}&token={$signedToken}&audio={$audioParam}&video={$videoParam}&screen={$screenParam}&chat={$chatParam}";
        $shareableJoinUrl = url("meeting/join/{$roomId}") . (!empty($roomPassword) ? ('?roomPassword=' . urlencode($roomPassword)) : '');

        return view('pages.meeting.join', compact(
            'roomId',
            'meetingTitle',
            'hostUser',
            'roomPassword',
            'cinemeetEmbedUrl',
            'shareableJoinUrl'
        ));
    }
}
