<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsTicker;
use App\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;

class UserNewsTickerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function hasNewsTickerFeature()
    {
        $user = Auth::user();
        $plan = $user->subscription_plan;

        if (!$plan) {
            return false;
        }

        $features = $plan->features ?? [];
        return in_array('news_ticker', $features);
    }

    public function index()
    {
        if (!$this->hasNewsTickerFeature()) {
            \Session::flash('flash_message', 'Your current plan does not support News Tickers.');
            return redirect('dashboard');
        }

        $news_tickers = NewsTicker::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(10);
        return view('pages.user.news_tickers.index', compact('news_tickers'));
    }

    public function create()
    {
        if (!$this->hasNewsTickerFeature()) {
            \Session::flash('flash_message', 'Your current plan does not support News Tickers.');
            return redirect('dashboard');
        }

        return view('pages.user.news_tickers.add');
    }

    public function store(Request $request)
    {
        if (!$this->hasNewsTickerFeature()) {
            \Session::flash('flash_message', 'Your current plan does not support News Tickers.');
            return redirect('dashboard');
        }

        $request->validate([
            'headline' => 'required',
            'details' => 'required',
        ]);

        $news = new NewsTicker();
        $news->user_id = Auth::user()->id;
        $news->headline = $request->headline;
        $news->details = $request->details;
        $news->is_breaking = $request->is_breaking ?? 0;
        $news->status = 0; // Pending approval by default
        $news->save();

        \Session::flash('flash_message', 'News Ticker submitted successfully. It will be visible after admin approval.');
        return redirect('user/news_tickers');
    }
}
