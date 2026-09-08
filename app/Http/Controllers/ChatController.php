<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Display live chat inbox (Admin view or redirect user)
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->guest('login');
        }

        $user = Auth::user();

        if (in_array($user->usertype, ['Admin', 'Sub_Admin', 'Moderator'], true)) {
            $page_title = 'Live Chat & Support';
            return view('admin.pages.chat.chat', compact('page_title'));
        }

        if (!$user->hasPaidSubscription()) {
            \Session::flash('error_flash_message', 'Live Chat Support is exclusively available to members with an active paid subscription.');
            return redirect('membership_plan');
        }

        return redirect('/?open_chat=1');
    }

    /* =========================================================================
       PUBLIC WIDGET ENDPOINTS (Exclusive to Paid Subscribers & Staff)
       ========================================================================= */

    /**
     * Authorize that the current request is from an authenticated user with an active paid subscription
     */
    private function authorizePaidChatAccess()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'code' => 'unauthenticated',
                'message' => 'Please sign in to access Live Chat Support.',
            ], 401);
        }

        $user = Auth::user();
        if (!$user->hasPaidSubscription()) {
            return response()->json([
                'success' => false,
                'code' => 'subscription_required',
                'message' => 'Live Chat Support is exclusively available to members with an active paid subscription.',
            ], 403);
        }

        return null;
    }

    /**
     * Fetch messages for the floating chat widget
     */
    public function fetchWidgetMessages(Request $request)
    {
        if ($authError = $this->authorizePaidChatAccess()) {
            return $authError;
        }

        $user = Auth::user();
        $lastId = (int) $request->input('last_id', 0);
        $markRead = $request->boolean('mark_read', false);

        $query = Message::query()->where('user_id', $user->id);

        if ($markRead) {
            Message::where('user_id', $user->id)
                ->where('sender', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $messages = $query->orderBy('id', 'asc')
            ->limit(100)
            ->get()
            ->map(function ($msg) {
                $isAdmin = ($msg->sender === 'admin' || $msg->sender_type === 'admin');
                return [
                    'id' => $msg->id,
                    'sender' => $isAdmin ? 'admin' : 'user',
                    'sender_name' => $isAdmin ? 'Support Agent' : ($msg->user ? $msg->user->name : 'You'),
                    'message' => e($msg->message),
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                    'is_read' => (bool) $msg->is_read,
                    'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
                ];
            });

        // Unread admin messages count
        $unreadCount = Message::where('user_id', $user->id)
            ->where('sender', 'admin')
            ->where('is_read', false)
            ->count();

        // Highest user message ID that admin has read (for double ticks ✓✓)
        $lastReadUserMsgId = (int) Message::where('user_id', $user->id)
            ->where('is_read', true)
            ->where('sender', '!=', 'admin')
            ->max('id');

        // Check if Admin is currently typing
        $adminTypingKey = 'cw_admin_typing_user_' . $user->id;
        $isAdminTyping = (bool) Cache::has($adminTypingKey);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'unread_count' => $unreadCount,
            'last_read_user_msg_id' => $lastReadUserMsgId,
            'is_admin_typing' => $isAdminTyping,
            'is_auth' => true,
            'user_name' => $user->name,
        ]);
    }

    /**
     * Record visitor / user typing activity
     */
    public function widgetTyping(Request $request)
    {
        if ($authError = $this->authorizePaidChatAccess()) {
            return $authError;
        }

        $user = Auth::user();
        $key = 'cw_typing_user_' . $user->id;
        Cache::put($key, now()->timestamp, 4);

        return response()->json(['success' => true]);
    }

    /**
     * Send message from floating chat widget
     */
    public function sendWidgetMessage(Request $request)
    {
        if ($authError = $this->authorizePaidChatAccess()) {
            return $authError;
        }

        $validated = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $user = Auth::user();

        $msg = new Message();
        $msg->message = trim($validated['message']);
        $msg->is_read = false;
        $msg->ip_address = $request->ip();
        $msg->user_id = $user->id;
        $msg->sender = 'user';
        $msg->sender_type = 'user';
        $msg->guest_token = null;
        $msg->guest_name = null;
        $msg->save();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender' => 'user',
                'sender_name' => $user->name,
                'message' => e($msg->message),
                'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
            ]
        ]);
    }

    /**
     * Get unread message count for floating widget badge
     */
    public function getWidgetUnread(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasPaidSubscription()) {
            return response()->json(['unread_count' => 0]);
        }

        $user = Auth::user();
        $unreadCount = Message::where('user_id', $user->id)
            ->where('sender', 'admin')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /* =========================================================================
       ADMIN ENDPOINTS (Live Chat Control Center)
       ========================================================================= */

    /**
     * Get conversation list with latest messages and unread counts
     */
    public function getAdminConversations(Request $request)
    {
        if (!Auth::check() || Auth::user()->usertype !== 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // 1. Fetch Registered User threads
        $userThreads = Message::whereNotNull('user_id')
            ->select('user_id', DB::raw('MAX(id) as max_id'), DB::raw('COUNT(CASE WHEN sender != "admin" AND is_read = 0 THEN 1 END) as unread_count'))
            ->groupBy('user_id')
            ->get();

        // 2. Fetch Guest threads
        $guestThreads = Message::whereNull('user_id')
            ->whereNotNull('guest_token')
            ->select('guest_token', DB::raw('MAX(id) as max_id'), DB::raw('COUNT(CASE WHEN sender != "admin" AND is_read = 0 THEN 1 END) as unread_count'))
            ->groupBy('guest_token')
            ->get();

        $allThreads = [];

        // Collect all max_ids to load latest messages in one query
        $allMaxIds = $userThreads->pluck('max_id')->merge($guestThreads->pluck('max_id'))->filter()->toArray();
        $latestMessages = Message::whereIn('id', $allMaxIds)->get()->keyBy('id');

        // Collect user ids
        $allUserIds = $userThreads->pluck('user_id')->filter()->toArray();
        $users = User::whereIn('id', $allUserIds)->get()->keyBy('id');

        foreach ($userThreads as $ut) {
            $lastMsg = $latestMessages->get($ut->max_id);
            if (!$lastMsg) continue;

            $u = $users->get($ut->user_id);
            $userName = $u ? $u->name : ('User #' . $ut->user_id);
            $userEmail = $u ? $u->email : 'Registered User';
            $avatar = ($u && !empty($u->user_image)) ? asset('upload/' . $u->user_image) : null;

            $isTyping = (bool) Cache::has('cw_typing_user_' . $ut->user_id);

            $allThreads[] = [
                'id' => $ut->user_id,
                'key' => 'user_' . $ut->user_id,
                'type' => 'user',
                'name' => $userName,
                'email' => $userEmail,
                'avatar' => $avatar,
                'last_message' => Str::limit($lastMsg->message, 50),
                'last_message_raw' => $lastMsg->message,
                'last_sender' => $lastMsg->sender,
                'last_time' => $lastMsg->created_at ? $lastMsg->created_at->diffForHumans(null, true) : '',
                'timestamp' => $lastMsg->created_at ? $lastMsg->created_at->timestamp : 0,
                'unread_count' => (int) $ut->unread_count,
                'is_typing' => $isTyping,
            ];
        }

        foreach ($guestThreads as $gt) {
            $lastMsg = $latestMessages->get($gt->max_id);
            if (!$lastMsg) continue;

            $guestName = $lastMsg->guest_name ?: ('Guest #' . substr($gt->guest_token, -4));
            $isTyping = (bool) Cache::has('cw_typing_guest_' . $gt->guest_token);

            $allThreads[] = [
                'id' => $gt->guest_token,
                'key' => 'guest_' . $gt->guest_token,
                'type' => 'guest',
                'name' => $guestName,
                'email' => 'Guest Visitor',
                'avatar' => null,
                'last_message' => Str::limit($lastMsg->message, 50),
                'last_message_raw' => $lastMsg->message,
                'last_sender' => $lastMsg->sender,
                'last_time' => $lastMsg->created_at ? $lastMsg->created_at->diffForHumans(null, true) : '',
                'timestamp' => $lastMsg->created_at ? $lastMsg->created_at->timestamp : 0,
                'unread_count' => (int) $gt->unread_count,
                'is_typing' => $isTyping,
            ];
        }

        // Sort descending by latest timestamp
        usort($allThreads, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        $totalUnread = array_sum(array_column($allThreads, 'unread_count'));

        return response()->json([
            'threads' => $allThreads,
            'total_unread' => $totalUnread,
        ]);
    }

    /**
     * Get messages for a specific conversation thread in Admin
     */
    public function getAdminThread(Request $request)
    {
        if (!Auth::check() || Auth::user()->usertype !== 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->input('type'); // 'user' or 'guest'
        $targetId = $request->input('id');
        $lastId = (int) $request->input('last_id', 0);

        if (!$type || !$targetId) {
            return response()->json(['error' => 'Invalid thread parameters'], 422);
        }

        $query = Message::query();

        if ($type === 'user') {
            $query->where('user_id', $targetId);
            // Mark incoming unread user messages as read
            Message::where('user_id', $targetId)
                ->where('sender', '!=', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $customerUser = User::find($targetId);
            $planName = 'Free';
            if ($customerUser && $customerUser->plan_id) {
                $planObj = \App\SubscriptionPlan::find($customerUser->plan_id);
                if ($planObj) {
                    $planName = $planObj->plan_name . ((float)$planObj->plan_price > 0 ? ' ($' . $planObj->plan_price . ')' : ' (Free)');
                }
            }

            $customer = [
                'type' => 'user',
                'id' => $targetId,
                'name' => $customerUser ? $customerUser->name : ('User #' . $targetId),
                'email' => $customerUser ? $customerUser->email : 'Registered User',
                'phone' => $customerUser->phone ?? 'N/A',
                'created_at' => $customerUser && $customerUser->created_at ? $customerUser->created_at->format('M d, Y') : 'N/A',
                'plan' => $planName,
                'has_paid_sub' => $customerUser ? $customerUser->hasPaidSubscription() : false,
                'expires_at' => ($customerUser && $customerUser->exp_date) ? date('M d, Y', $customerUser->exp_date) : 'N/A',
            ];
        } else {
            $query->where('guest_token', $targetId);
            // Mark incoming unread guest messages as read
            Message::where('guest_token', $targetId)
                ->where('sender', '!=', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $latestGuestMsg = Message::where('guest_token', $targetId)->latest('id')->first();
            $customer = [
                'type' => 'guest',
                'id' => $targetId,
                'name' => $latestGuestMsg && $latestGuestMsg->guest_name ? $latestGuestMsg->guest_name : ('Guest #' . substr($targetId, -4)),
                'email' => 'Guest Visitor (Unregistered)',
                'ip_address' => $latestGuestMsg->ip_address ?? 'N/A',
                'first_seen' => $latestGuestMsg && $latestGuestMsg->created_at ? $latestGuestMsg->created_at->diffForHumans() : 'Recently',
            ];
        }

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $messages = $query->orderBy('id', 'asc')
            ->limit(150)
            ->get()
            ->map(function ($m) {
                $isAdmin = ($m->sender === 'admin' || $m->sender_type === 'admin');
                return [
                    'id' => $m->id,
                    'sender' => $isAdmin ? 'admin' : 'user',
                    'sender_name' => $isAdmin ? 'Admin' : ($m->user ? $m->user->name : ($m->guest_name ?: 'Visitor')),
                    'message' => e($m->message),
                    'time' => $m->created_at ? $m->created_at->format('h:i A') : '',
                    'date' => $m->created_at ? $m->created_at->format('M d, Y') : '',
                    'is_read' => (bool) $m->is_read,
                    'created_at' => $m->created_at ? $m->created_at->toIso8601String() : null,
                ];
            });

        // Highest admin message ID that the user has read (for double ticks ✓✓)
        $lastReadAdminQuery = Message::where('is_read', true)->where('sender', 'admin');
        if ($type === 'user') {
            $lastReadAdminQuery->where('user_id', $targetId);
        } else {
            $lastReadAdminQuery->where('guest_token', $targetId);
        }
        $lastReadAdminMsgId = (int) $lastReadAdminQuery->max('id');

        $isUserTyping = (bool) Cache::has('cw_typing_' . $type . '_' . $targetId);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'customer' => $customer,
            'last_read_admin_msg_id' => $lastReadAdminMsgId,
            'is_user_typing' => $isUserTyping,
        ]);
    }

    /**
     * Record admin typing activity for current active thread
     */
    public function adminTyping(Request $request)
    {
        if (!Auth::check() || Auth::user()->usertype !== 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->input('type');
        $targetId = $request->input('target_id');

        if (!$type || !$targetId) {
            return response()->json(['error' => 'Invalid parameters'], 422);
        }

        $key = 'cw_admin_typing_' . $type . '_' . $targetId;
        Cache::put($key, now()->timestamp, 4);

        return response()->json(['success' => true]);
    }

    /**
     * Send Admin reply to a conversation thread
     */
    public function sendAdminReply(Request $request)
    {
        if (!Auth::check() || Auth::user()->usertype !== 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:user,guest',
            'target_id' => 'required|string',
            'message' => 'required|string|max:4000',
        ]);

        $msg = new Message();
        $msg->message = trim($validated['message']);
        $msg->sender = 'admin';
        $msg->sender_type = 'admin';
        $msg->admin_id = Auth::id();
        $msg->is_read = false;
        $msg->ip_address = $request->ip();

        if ($validated['type'] === 'user') {
            $msg->user_id = (int) $validated['target_id'];
            $msg->guest_token = null;
        } else {
            $msg->user_id = null;
            $msg->guest_token = $validated['target_id'];
            // Retain guest name from thread if available
            $prev = Message::where('guest_token', $validated['target_id'])->whereNotNull('guest_name')->first();
            $msg->guest_name = $prev ? $prev->guest_name : ('Guest #' . substr($validated['target_id'], -4));
        }

        $msg->save();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender' => 'admin',
                'sender_name' => 'Admin',
                'message' => e($msg->message),
                'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
            ]
        ]);
    }

    /**
     * Delete an entire conversation thread (Admin only)
     */
    public function deleteAdminThread(Request $request)
    {
        if (!Auth::check() || Auth::user()->usertype !== 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'user') {
            Message::where('user_id', $id)->delete();
        } elseif ($type === 'guest') {
            Message::where('guest_token', $id)->delete();
        }

        return response()->json(['success' => true]);
    }

    /* =========================================================================
       LEGACY COMPATIBILITY
       ========================================================================= */

    public function fetchMessages($userId)
    {
        $messages = Message::where('user_id', $userId)
            ->orderBy('created_at')
            ->get();

        return view('admin.pages.chat.partials.messages', compact('messages'))->render();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $message = new Message();

        if ($user && $user->usertype === 'Admin') {
            $message->user_id = $request->user_id;
            $message->sender = 'admin';
            $message->sender_type = 'admin';
            $userId = $request->user_id;
        } else {
            $message->user_id = $user ? $user->id : null;
            $message->sender = 'user';
            $message->sender_type = 'user';
            $userId = $user ? $user->id : null;
        }

        $message->message = $request->messages;
        $message->is_read = false;
        $message->save();

        return redirect()->back()->with('user_id', $userId);
    }
}
