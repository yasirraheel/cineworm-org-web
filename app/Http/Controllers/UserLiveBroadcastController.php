<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class UserLiveBroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function hasLiveBroadcastFeature()
    {
        $user = Auth::user();
        if (!$user->plan_id) {
            return false;
        }

        $plan = \App\SubscriptionPlan::find($user->plan_id);

        if (!$plan) {
            return false;
        }

        $features = $plan->getEffectiveFeatureKeys();
        return in_array('live_broadcast', $features);
    }

    public function index()
    {
        if (!$this->hasLiveBroadcastFeature()) {
            \Session::flash('error_flash_message', 'Your current plan does not support Live Broadcasts.');
            return redirect('dashboard');
        }

        $live_broadcasts = \App\Models\LiveBroadcast::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(10);
        return view('pages.user.live_broadcasts.index', compact('live_broadcasts'));
    }

    public function create()
    {
        if (!$this->hasLiveBroadcastFeature()) {
            \Session::flash('error_flash_message', 'Your current plan does not support Live Broadcasts.');
            return redirect('dashboard');
        }

        return view('pages.user.live_broadcasts.add');
    }

    public function store(Request $request)
    {
        if (!$this->hasLiveBroadcastFeature()) {
            \Session::flash('error_flash_message', 'Your current plan does not support Live Broadcasts.');
            return redirect('dashboard');
        }

        $request->validate([
            'title' => 'required',
        ]);

        $user = Auth::user();
        if (empty($user->zoom_access_token)) {
            \Session::flash('error_flash_message', 'Please connect your Zoom account first.');
            return redirect('user/live_broadcasts');
        }

        // Call Zoom API to create a meeting
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('POST', 'https://api.zoom.us/v2/users/me/meetings', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $user->zoom_access_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'topic' => $request->title,
                    'type' => 1, // Instant meeting
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => false, // Treat as broadcast, guests can turn on later
                        'mute_upon_entry' => true,
                        'auto_recording' => 'cloud',
                    ]
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            
            if (!isset($body['id'])) {
                throw new \Exception('Failed to get meeting ID from Zoom');
            }

            $broadcast = new \App\Models\LiveBroadcast();
            $broadcast->user_id = $user->id;
            $broadcast->title = $request->title;
            $broadcast->zoom_meeting_id = $body['id'];
            $broadcast->zoom_join_url = $body['join_url'];
            $broadcast->zoom_start_url = $body['start_url'];
            $broadcast->zoom_meeting_password = $body['password'] ?? null;
            $broadcast->status = 0; // pending approval
            $broadcast->save();

            \Session::flash('flash_message', 'Live Broadcast created successfully. It will be visible after admin approval.');
            return redirect('user/live_broadcasts');

        } catch (\Exception $e) {
            // Check if token expired, handle refresh token in a real app
            \Session::flash('error_flash_message', 'Error creating broadcast: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
