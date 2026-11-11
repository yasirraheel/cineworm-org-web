<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LiveBroadcast;

class MeetingJoinController extends Controller
{
    /**
     * Handle guest/subscriber meeting join URL (https://cineworm.org/meeting/join/{roomId})
     */
    public function join(Request $request, $roomId)
    {
        $roomId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $roomId);

        if (empty($roomId)) {
            abort(404, 'Invalid CineMeet room ID.');
        }

        // Find broadcast meeting record if exists
        $broadcast = LiveBroadcast::where('zoom_meeting_id', $roomId)->first();

        $user = Auth::user();
        $meetingTitle = $broadcast ? $broadcast->title : 'CineMeet Room';
        $hostUser = $broadcast ? $broadcast->user : null;
        
        // Password protection check
        $dbPassword = $broadcast ? $broadcast->zoom_meeting_password : '';
        $roomPassword = $request->get('roomPassword', $dbPassword);

        $cinemeetBaseUrl = rtrim(env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org'), '/');

        // Keep the CineMeet wrapper on cineworm.org. Guests may use this page
        // without logging in; only the embedded SFU loads from the subdomain.
        $joinParams = [
            'room' => $roomId,
            'audio' => $request->get('audio', '1'),
            'video' => $request->get('video', '1'),
            'screen' => $request->get('screen', '1'),
            'chat' => $request->get('chat', '1'),
            'roomPassword' => !empty($roomPassword) ? $roomPassword : '0',
        ];
        if ($user) {
            $joinParams['name'] = $user->name ?? '';
            $joinParams['avatar'] = $user->user_icon ? asset($user->user_icon) : '';
        }

        $cinemeetEmbedUrl = $cinemeetBaseUrl . '/join?' . http_build_query($joinParams);
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
