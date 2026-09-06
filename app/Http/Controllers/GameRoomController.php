<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Events\GameControlEvent;
use App\WatermelonScore;


class GameRoomController extends Controller
{
    /**
     * Generate a new room code for game session
     */
    public function generateRoomCode()
    {
        // Generate a 4-digit room code
        $roomCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

        // Store room code in cache for 1 hour
        Cache::put('game_room_' . $roomCode, ['created_at' => now()], 3600);

        return response()->json([
            'success' => true,
            'room_code' => $roomCode
        ]);
    }

    /**
     * Verify if a room code exists
     */
    public function verifyRoomCode(Request $request)
    {
        $roomCode = $request->input('room_code');

        $exists = Cache::has('game_room_' . $roomCode);

        return response()->json([
            'success' => $exists,
            'message' => $exists ? 'Room found' : 'Invalid room code'
        ]);
    }

    /**
     * Send game control input
     */
    public function sendControl(Request $request)
    {
        $validated = $request->validate([
            'room_code' => 'required|string|size:4',
            'direction' => 'required|in:up,down,left,right',
            'action' => 'required|in:press,release'
        ]);

        // Verify room exists
        if (!Cache::has('game_room_' . $validated['room_code'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid room code'
            ], 404);
        }

        // Store control in cache with 1 second expiry (polling-based)
        $controlKey = 'game_control_' . $validated['room_code'];

        // Get existing controls or create new array
        $controls = Cache::get($controlKey, []);

        // Add new control with timestamp
        $controls[] = [
            'direction' => $validated['direction'],
            'action' => $validated['action'],
            'timestamp' => microtime(true)
        ];

        // Keep only last 10 controls
        if (count($controls) > 10) {
            $controls = array_slice($controls, -10);
        }

        // Store for 2 seconds
        Cache::put($controlKey, $controls, 2);

        return response()->json([
            'success' => true,
            'message' => 'Control sent'
        ]);
    }

    /**
     * Get pending controls for a room (polling endpoint)
     */
    public function getControls(Request $request)
    {
        $roomCode = $request->input('room_code');
        $lastTimestamp = $request->input('last_timestamp', 0);

        if (!$roomCode || !Cache::has('game_room_' . $roomCode)) {
            return response()->json([
                'success' => false,
                'controls' => []
            ]);
        }

        // Get controls from cache
        $controlKey = 'game_control_' . $roomCode;
        $controls = Cache::get($controlKey, []);

        // Filter controls newer than last timestamp
        $newControls = array_filter($controls, function($control) use ($lastTimestamp) {
            return $control['timestamp'] > $lastTimestamp;
        });

        return response()->json([
            'success' => true,
            'controls' => array_values($newControls)
        ]);
    }

    /**
     * Show the mobile remote control page
     */
    public function showRemoteControl()
    {
        return view('pages.remote_control');
    }

    // ─────────────────────────────────────────────
    // Watermelon Global Leaderboard
    // ─────────────────────────────────────────────

    /**
     * Submit a score to the global leaderboard.
     * Works for logged-in users and guests.
     */
    public function submitScore(Request $request)
    {
        $request->validate([
            'score'       => 'required|integer|min:0|max:99999',
            'player_name' => 'nullable|string|max:30',
            'guest_token' => 'nullable|string|max:64',
        ]);

        $score       = (int) $request->input('score');
        $guestToken  = $request->input('guest_token');
        $user        = Auth::user();

        if ($user) {
            $playerName = $user->name ?? 'Player';
            // Only update if this is a personal best
            $existing = WatermelonScore::where('user_id', $user->id)->first();
            if ($existing) {
                if ($score > $existing->score) {
                    $existing->update(['score' => $score, 'player_name' => $playerName]);
                }
                $record = $existing->fresh();
            } else {
                $record = WatermelonScore::create([
                    'user_id'     => $user->id,
                    'player_name' => $playerName,
                    'score'       => $score,
                ]);
            }
        } else {
            // Guest — use their token to de-duplicate
            if ($guestToken) {
                $existing = WatermelonScore::where('guest_token', $guestToken)->first();
                if ($existing) {
                    $rawName = trim($request->input('player_name', ''));
                    $updateData = [];
                    if ($score > $existing->score) {
                        $updateData['score'] = $score;
                    }
                    if ($rawName !== '' && $rawName !== 'Guest') {
                        $updateData['player_name'] = $rawName;
                    }
                    if (!empty($updateData)) {
                        $existing->update($updateData);
                    }
                    $record = $existing->fresh();
                } else {
                    $rawName  = trim($request->input('player_name', ''));
                    $playerName = $rawName !== '' ? $rawName : 'Guest';
                    $record = WatermelonScore::create([
                        'user_id'     => null,
                        'player_name' => $playerName,
                        'score'       => $score,
                        'guest_token' => $guestToken,
                    ]);
                }
            } else {
                $rawName  = trim($request->input('player_name', ''));
                $playerName = $rawName !== '' ? $rawName : 'Guest';
                $record = WatermelonScore::create([
                    'user_id'     => null,
                    'player_name' => $playerName,
                    'score'       => $score,
                    'guest_token' => null,
                ]);
            }
        }

        // Calculate player's global rank
        $rank = WatermelonScore::where('score', '>', $record->score)->count() + 1;

        return response()->json([
            'success' => true,
            'rank'    => $rank,
            'score'   => $record->score,
        ]);
    }

    /**
     * Return top-50 global leaderboard entries.
     */
    public function getLeaderboard(Request $request)
    {
        $guestToken = $request->input('guest_token');
        $user       = Auth::user();

        $top = WatermelonScore::orderByDesc('score')
            ->limit(50)
            ->get(['id', 'user_id', 'player_name', 'score', 'guest_token', 'updated_at'])
            ->map(function ($row, $index) {
                return [
                    'rank'        => $index + 1,
                    'player_name' => $row->player_name,
                    'score'       => $row->score,
                    'user_id'     => $row->user_id,
                    'guest_token' => $row->guest_token,
                    'updated_at'  => $row->updated_at?->diffForHumans(),
                ];
            });

        // Find the current player's own entry
        $myEntry = null;
        if ($user) {
            $myRecord = WatermelonScore::where('user_id', $user->id)->first();
            if ($myRecord) {
                $myRank = WatermelonScore::where('score', '>', $myRecord->score)->count() + 1;
                $myEntry = ['rank' => $myRank, 'score' => $myRecord->score, 'player_name' => $myRecord->player_name];
            }
        } elseif ($guestToken) {
            $myRecord = WatermelonScore::where('guest_token', $guestToken)->first();
            if ($myRecord) {
                $myRank = WatermelonScore::where('score', '>', $myRecord->score)->count() + 1;
                $myEntry = ['rank' => $myRank, 'score' => $myRecord->score, 'player_name' => $myRecord->player_name];
            }
        }

        return response()->json([
            'success'  => true,
            'top'      => $top,
            'my_entry' => $myEntry,
        ]);
    }
}

