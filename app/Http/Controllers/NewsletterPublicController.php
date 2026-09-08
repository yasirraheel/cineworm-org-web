<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NewsletterSubscriber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsletterPublicController extends Controller
{
    /**
     * Subscribe to newsletter via AJAX form.
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
            'name' => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $name = trim($request->input('name', ''));
        $ip = $request->ip();

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->status == 1) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'You are already subscribed to our newsletter!',
                ]);
            } else {
                $subscriber->status = 1;
                if (!empty($name)) {
                    $subscriber->name = $name;
                }
                $subscriber->ip_address = $ip;
                $subscriber->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Welcome back! Your newsletter subscription has been reactivated.',
                ]);
            }
        }

        NewsletterSubscriber::create([
            'email' => $email,
            'name' => $name ?: null,
            'status' => 1,
            'unsubscribe_token' => Str::random(32),
            'ip_address' => $ip,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you! You have successfully subscribed to our newsletter.',
        ]);
    }

    /**
     * One-click unsubscribe action.
     */
    public function unsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return view('pages.newsletter_unsubscribed', [
                'success' => false,
                'message' => 'Invalid or expired unsubscribe link.',
                'subscriber' => null,
            ]);
        }

        $subscriber->status = 0;
        $subscriber->save();

        return view('pages.newsletter_unsubscribed', [
            'success' => true,
            'message' => 'You have been successfully unsubscribed from our newsletter.',
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Re-subscribe if unsubscribed by accident.
     */
    public function resubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return redirect('/')->with('error_flash_message', 'Invalid link.');
        }

        $subscriber->status = 1;
        $subscriber->save();

        return view('pages.newsletter_unsubscribed', [
            'success' => true,
            'is_resubscribed' => true,
            'message' => 'You have been successfully re-subscribed to our newsletter!',
            'subscriber' => $subscriber,
        ]);
    }
}
