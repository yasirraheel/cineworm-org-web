<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        
        // Password protection check
        $dbPassword = $broadcast ? $broadcast->zoom_meeting_password : '';
        $roomPassword = $request->get('roomPassword', $dbPassword);

        $cinemeetBaseUrl = rtrim(env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org'), '/');

        // Do not send a CineMeet token here. CineMeet's `token` parameter is a
        // JWT created with its own JWT_KEY; the old custom HMAC value was not a
        // valid CineMeet JWT and caused the iframe to return `Invalid Token`.
        // The current CineMeet server allows public room joins, so the room URL
        // is sufficient. If host/user authentication is enabled later, the
        // CineMeet login flow will handle authentication inside the iframe.
        $joinParams = [
            'room' => $roomId,
            'audio' => $request->get('audio', '1'),
            'video' => $request->get('video', '1'),
            'screen' => $request->get('screen', '1'),
            'chat' => $request->get('chat', '1'),
        ];
        if (!empty($roomPassword) && $roomPassword !== '0') {
            $joinParams['roomPassword'] = $roomPassword;
        }

        return redirect()->away($cinemeetBaseUrl . '/join?' . http_build_query($joinParams));
    }
}
