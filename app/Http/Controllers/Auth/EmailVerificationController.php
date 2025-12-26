<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Handle the email verification
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        // Check if the hash matches
        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect('/login')->with('flash_error', 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('flash_success', 'Email already verified. Please login.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect('/login')->with('flash_success', 'Email verified successfully! You can now login.');
    }

    /**
     * Resend the email verification notification
     */
    public function resend(Request $request)
    {
        // Get authenticated user or find by email
        $user = null;

        if (Auth::check()) {
            $user = Auth::user();
        } elseif ($request->email) {
            $user = User::where('email', $request->email)->first();
        }

        if (!$user) {
            return back()->with('flash_error', 'User not found.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('flash_success', 'Email already verified.');
        }

        // Send verification email
        $this->sendVerificationEmail($user);

        return back()->with('flash_success', 'Verification link sent to your email!');
    }

    /**
     * Send the email verification notification
     */
    public static function sendVerificationEmail($user)
    {
        // Generate signed verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'verificationUrl' => $verificationUrl,
        ];

        try {
            Mail::send('emails.verify_email', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->from(getcong('site_email'), getcong('site_name'))
                    ->subject('Verify Your Email Address - ' . getcong('site_name'));
            });
        } catch (\Exception $e) {
            \Log::error('Email verification send failed: ' . $e->getMessage());
        }
    }
}
