<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->usertype !== 'Admin' && Auth::user()->usertype !== 'Sub_Admin') {
            $user = Auth::user();
            if ($user->plan_id > 0 && $user->exp_date) {
                // Check if expired (exp_date is a timestamp)
                if (strtotime(date('m/d/Y')) > $user->exp_date) {
                    $user->plan_id = 0;
                    $user->exp_date = null;
                    $user->save();
                }
            }
        }

        return $next($request);
    }
}
