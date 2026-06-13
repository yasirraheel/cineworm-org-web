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

        // Return a coming soon message since full implementation is planned for later
        \Session::flash('flash_message', 'The Live Broadcast feature is coming soon!');
        return redirect('dashboard');
    }
}
