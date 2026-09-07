<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        if ($user->usertype === 'Admin') {
            $page_title = 'Live Chat & Support';
            return view('admin.pages.chat.chat', compact('page_title'));
        } else {
            // For regular users visiting /messages, redirect to home page with chat trigger
            return redirect('/?open_chat=1');
        }
    }

    /* =========================================================================
       PUBLIC WIDGET ENDPOINTS (Guest & Authenticated User)
       ========================================================================= */

    /**
     * Fetch messages for the floating chat widget
     */
    public function fetchWidgetMessages(Request $request)
    {
        $user = Auth::user();
        $guestToken = $request->input('guest_token');
        $lastId = (int) $request->input('last_id', 0);
        $markRead = $request->boolean('mark_read', false);

        if (!$user && empty($guestToken)) {
            return response()->json([
                'success' => true,
                'messages' => [],
                'unread_count' => 0,
            ]);
        }

        $query = Message::query();

        if ($user) {
            $query->where('user_id', $user->id);
            if ($markRead) {
                Message::where('user_id', $user->id)
                    ->where('sender', 'admin')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        } else {
            $query->where('guest_token', $guestToken);
            if ($markRead) {
                Message::where('guest_token', $guestToken)
                    ->where('sender', 'admin')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
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
                    'sender_name' => $isAdmin ? 'Support Agent' : ($msg->user ? $msg->user->name : ($msg->guest_name ?: 'You')),
                    'message' => e($msg->message),
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                    'is_read' => (bool) $msg->is_read,
                    'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
                ];
            });

        // Unread admin messages count
        $unreadQuery = Message::query()->where('sender', 'admin')->where('is_read', false);
        if ($user) {
            $unreadQuery->where('user_id', $user->id);
        } else {
            $unreadQuery->where('guest_token', $guestToken);
        }
        $unreadCount = $unreadQuery->count();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'unread_count' => $unreadCount,
            'is_auth' => (bool) $user,
            'user_name' => $user ? $user->name : null,
        ]);
    }

    /**
     * Send message from floating chat widget
     */
    public function sendWidgetMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:3000',
            'guest_token' => 'nullable|string|max:64',
            'guest_name' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $guestToken = $validated['guest_token'] ?? null;
        $guestName = $validated['guest_name'] ?? null;

        $msg = new Message();
        $msg->message = trim($validated['message']);
        $msg->is_read = false;
        $msg->ip_address = $request->ip();

        if ($user) {
            $msg->user_id = $user->id;
            $msg->sender = 'user';
            $msg->sender_type = 'user';
            $msg->guest_token = null;
            $msg->guest_name = null;
        } else {
            if (empty($guestToken)) {
                $guestToken = 'gw_' . Str::random(24);
            }
            if (empty($guestName)) {
                $guestName = 'Guest #' . substr($guestToken, -4);
            }
            $msg->user_id = null;
            $msg->sender = 'user';
            $msg->sender_type = 'guest';
            $msg->guest_token = $guestToken;
            $msg->guest_name = $guestName;
        }

        $msg->save();

        return response()->json([
            'success' => true,
            'guest_token' => $guestToken,
            'guest_name' => $guestName,
            'message' => [
                'id' => $msg->id,
                'sender' => 'user',
                'sender_name' => $user ? $user->name : ($guestName ?: 'You'),
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
        $user = Auth::user();
        $guestToken = $request->input('guest_token');

        if (!$user && empty($guestToken)) {
            return response()->json(['unread_count' => 0]);
        }

        $query = Message::where('sender', 'admin')->where('is_read', false);

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('guest_token', $guestToken);
        }

        return response()->json([
            'unread_count' => $query->count()
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
            ];
        }

        foreach ($guestThreads as $gt) {
            $lastMsg = $latestMessages->get($gt->max_id);
            if (!$lastMsg) continue;

            $guestName = $lastMsg->guest_name ?: ('Guest #' . substr($gt->guest_token, -4));

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
            $customer = [
                'type' => 'user',
                'id' => $targetId,
                'name' => $customerUser ? $customerUser->name : ('User #' . $targetId),
                'email' => $customerUser ? $customerUser->email : 'Registered User',
                'phone' => $customerUser->phone ?? 'N/A',
                'created_at' => $customerUser && $customerUser->created_at ? $customerUser->created_at->format('M d, Y') : 'N/A',
                'plan' => $customerUser->plan_name ?? 'Free',
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

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'customer' => $customer,
        ]);
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
