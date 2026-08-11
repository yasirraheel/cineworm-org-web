<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LiveBroadcast;
use Illuminate\Support\Str;

class UserLiveBroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if user's subscription plan grants access to live_broadcast feature
     */
    private function hasLiveBroadcastFeature()
    {
        $user = Auth::user();

        // Admin & Sub-Admin always have access
        if ($user->usertype === 'Admin' || $user->usertype === 'Sub_Admin') {
            return true;
        }

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

    /**
     * Live Broadcasts Dashboard & Meeting Workspace
     */
    public function index(Request $request)
    {
        if (!$this->hasLiveBroadcastFeature()) {
            \Session::flash('error_flash_message', 'Your current plan does not support Live Broadcasts. Please upgrade your subscription plan.');
            return redirect('dashboard');
        }

        $user = Auth::user();
        $cinemeetBaseUrl = rtrim(env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org'), '/');

        // Check if user requested to enter/start a call
        $inCall = $request->has('room');
        $requestedRoom = $request->get('room');

        if ($requestedRoom) {
            $roomId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $requestedRoom);
        } else {
            $roomId = 'cineworm_' . $user->id . '_' . Str::lower(Str::random(6));
        }

        // Find active broadcast record if exists
        $currentBroadcast = LiveBroadcast::where('user_id', $user->id)
            ->where('zoom_meeting_id', $roomId)
            ->first();

        $meetingTitle = $currentBroadcast->title ?? ($user->name . "'s Live Meeting");
        $roomPassword = $currentBroadcast->zoom_meeting_password ?? '';

        // Construct embedded CineMeet URL with user's name & auto-join params
        $nameEncoded   = urlencode($user->name ?? 'User-' . rand(1000, 9999));
        $avatarUrl     = $user->user_icon ? asset($user->user_icon) : '';
        $avatarEncoded = urlencode($avatarUrl);
        $passParam     = !empty($roomPassword) ? ('&roomPassword=' . urlencode($roomPassword)) : '';

        $audioParam    = $request->get('audio', '1');
        $videoParam    = $request->get('video', '1');
        $screenParam   = $request->get('screen', '1');

        $cinemeetEmbedUrl = "{$cinemeetBaseUrl}/join?room={$roomId}&name={$nameEncoded}&avatar={$avatarEncoded}{$passParam}&audio={$audioParam}&video={$videoParam}&screen={$screenParam}";
        $shareableJoinUrl = "{$cinemeetBaseUrl}/join?room={$roomId}" . (!empty($roomPassword) ? ('&roomPassword=' . urlencode($roomPassword)) : '');

        // Paginated history list of broadcasts
        $live_broadcasts = LiveBroadcast::where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('pages.user.live_broadcasts.index', compact(
            'inCall',
            'live_broadcasts',
            'roomId',
            'meetingTitle',
            'roomPassword',
            'cinemeetEmbedUrl',
            'shareableJoinUrl',
            'cinemeetBaseUrl'
        ));
    }

    /**
     * Show Create Meeting form
     */
    public function create()
    {
        if (!$this->hasLiveBroadcastFeature()) {
            \Session::flash('error_flash_message', 'Your current plan does not support Live Broadcasts.');
            return redirect('dashboard');
        }

        return view('pages.user.live_broadcasts.add');
    }

    /**
     * Store new CineMeet Live Broadcast Meeting with Custom Settings
     */
    public function store(Request $request)
    {
        if (!$this->hasLiveBroadcastFeature()) {
            \Session::flash('error_flash_message', 'Your current plan does not support Live Broadcasts.');
            return redirect('dashboard');
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $cinemeetBaseUrl = rtrim(env('CINEMEET_API_URL', 'https://cinemeet.cineworm.org'), '/');

        // Generate unique room ID
        $roomId = 'cineworm_' . $user->id . '_' . Str::lower(Str::random(6));
        $title  = $request->title ? trim($request->title) : ($user->name . "'s Live Meeting (" . date('M d, H:i') . ")");
        $password = $request->password ? trim($request->password) : '';

        $shareUrl = "{$cinemeetBaseUrl}/join?room={$roomId}" . (!empty($password) ? ('&roomPassword=' . urlencode($password)) : '');

        $broadcast = new LiveBroadcast();
        $broadcast->user_id               = $user->id;
        $broadcast->title                 = $title;
        $broadcast->zoom_meeting_id       = $roomId;
        $broadcast->zoom_join_url         = $shareUrl;
        $broadcast->zoom_start_url        = $shareUrl;
        $broadcast->zoom_meeting_password = $password;
        $broadcast->scheduled_at          = now();
        $broadcast->status                = 1; // Active
        $broadcast->save();

        \Session::flash('flash_message', 'New customized live meeting created successfully!');
        return redirect()->to('user/live_broadcasts?room=' . $roomId);
    }

    /**
     * Update room settings
     */
    public function updateRoom(Request $request, $id)
    {
        if (!$this->hasLiveBroadcastFeature()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $broadcast = LiveBroadcast::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if (!$broadcast) {
            return response()->json(['success' => false, 'message' => 'Meeting not found'], 404);
        }

        if ($request->has('title')) {
            $broadcast->title = trim($request->title);
        }
        if ($request->has('password')) {
            $broadcast->zoom_meeting_password = trim($request->password);
        }

        $broadcast->save();

        \Session::flash('flash_message', 'Meeting customization settings saved!');
        return redirect()->back();
    }
}
