<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List all newsletter subscribers with search & filtering.
     */
    public function subscribers(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $query = NewsletterSubscriber::query();

        // Search by email or name
        if ($request->filled('s')) {
            $keyword = trim($request->get('s'));
            $query->where(function ($q) use ($keyword) {
                $q->where('email', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        // Filter by status (1 = active, 0 = unsubscribed)
        if ($request->filled('status') && in_array($request->get('status'), ['0', '1'], true)) {
            $query->where('status', (int)$request->get('status'));
        }

        $subscribers = $query->orderBy('id', 'desc')->paginate(15);

        // Stats counts
        $total_count = NewsletterSubscriber::count();
        $active_count = NewsletterSubscriber::where('status', 1)->count();
        $unsubscribed_count = NewsletterSubscriber::where('status', 0)->count();

        return view('admin.pages.newsletter.subscribers', compact(
            'subscribers',
            'total_count',
            'active_count',
            'unsubscribed_count'
        ));
    }

    /**
     * Add a new subscriber manually from admin.
     */
    public function addSubscriber(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $request->validate([
            'email' => 'required|email|max:191',
            'name' => 'nullable|string|max:191',
        ]);

        $email = strtolower(trim($request->input('email')));
        $name = trim($request->input('name', ''));

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            $subscriber->status = 1;
            if (!empty($name)) {
                $subscriber->name = $name;
            }
            $subscriber->save();
            Session::flash('flash_message', 'Subscriber already existed and has been reactivated.');
        } else {
            NewsletterSubscriber::create([
                'email' => $email,
                'name' => $name ?: null,
                'status' => 1,
                'unsubscribe_token' => Str::random(32),
                'ip_address' => $request->ip(),
            ]);
            Session::flash('flash_message', 'Subscriber added successfully.');
        }

        return redirect()->back();
    }

    /**
     * Toggle subscriber status (active/unsubscribed).
     */
    public function toggleStatus($id)
    {
        if (Auth::User()->usertype != "Admin") {
            return response()->json(['status' => 'error', 'message' => trans('words.access_denied')], 403);
        }

        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->status = $subscriber->status == 1 ? 0 : 1;
        $subscriber->save();

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'new_status' => $subscriber->status,
                'message' => 'Subscriber status updated successfully.',
            ]);
        }

        Session::flash('flash_message', 'Subscriber status updated successfully.');
        return redirect()->back();
    }

    /**
     * Delete subscriber.
     */
    public function deleteSubscriber($id)
    {
        if (Auth::User()->usertype != "Admin") {
            return response()->json(['status' => 'error', 'message' => trans('words.access_denied')], 403);
        }

        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        if (request()->ajax()) {
            return response()->json([
                'status' => 1,
                'action' => 'dlt',
                'message' => 'Subscriber removed successfully.',
            ]);
        }

        Session::flash('flash_message', 'Subscriber removed successfully.');
        return redirect()->back();
    }

    /**
     * Export subscribers as CSV file.
     */
    public function exportSubscribers(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $query = NewsletterSubscriber::query();

        if ($request->filled('status') && in_array($request->get('status'), ['0', '1'], true)) {
            $query->where('status', (int)$request->get('status'));
        }

        $subscribers = $query->orderBy('id', 'desc')->get();

        $filename = 'newsletter_subscribers_' . date('Y-m-d_His') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Email', 'Name', 'Status', 'IP Address', 'Subscribed At']);

            foreach ($subscribers as $s) {
                fputcsv($file, [
                    $s->id,
                    $s->email,
                    $s->name ?? '',
                    $s->status == 1 ? 'Active' : 'Unsubscribed',
                    $s->ip_address ?? '',
                    $s->created_at ? $s->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * View compose & broadcast newsletter page.
     */
    public function composeView()
    {
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $active_count = NewsletterSubscriber::where('status', 1)->count();
        $admin_email = Auth::User()->email;

        return view('admin.pages.newsletter.send', compact('active_count', 'admin_email'));
    }

    /**
     * Send a test email to the admin or given address.
     */
    public function sendTestEmail(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            return response()->json(['status' => 'error', 'message' => trans('words.access_denied')], 403);
        }

        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $test_email = trim($request->input('test_email'));
        $subject = '[TEST] ' . trim($request->input('subject'));
        $body_content = $request->input('content');

        try {
            Mail::send('emails.newsletter', [
                'subject' => $subject,
                'name' => 'Admin Tester',
                'body_content' => $body_content,
                'unsubscribe_url' => url('/'),
            ], function ($message) use ($test_email, $subject) {
                $message->to($test_email)
                        ->from(getcong('site_email'), getcong('site_name'))
                        ->subject($subject);
            });

            return response()->json([
                'status' => 'success',
                'message' => "Test newsletter successfully sent to {$test_email}!",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Broadcast newsletter to all active subscribers.
     */
    public function sendNewsletter(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $subject = trim($request->input('subject'));
        $body_content = $request->input('content');

        $subscribers = NewsletterSubscriber::where('status', 1)->get();

        if ($subscribers->isEmpty()) {
            Session::flash('flash_message', 'No active subscribers found to send to.');
            return redirect()->back();
        }

        $success_count = 0;
        $failed_count = 0;

        foreach ($subscribers as $subscriber) {
            try {
                Mail::send('emails.newsletter', [
                    'subject' => $subject,
                    'name' => $subscriber->name ?: 'Subscriber',
                    'body_content' => $body_content,
                    'unsubscribe_url' => $subscriber->unsubscribe_url,
                ], function ($message) use ($subscriber, $subject) {
                    $message->to($subscriber->email, $subscriber->name ?: null)
                            ->from(getcong('site_email'), getcong('site_name'))
                            ->subject($subject);
                });
                $success_count++;
            } catch (\Throwable $e) {
                $failed_count++;
            }
        }

        $msg = "Newsletter broadcast finished: {$success_count} emails sent successfully.";
        if ($failed_count > 0) {
            $msg .= " ({$failed_count} failed)";
        }

        Session::flash('flash_message', $msg);
        return redirect('admin/newsletter/subscribers');
    }
}
