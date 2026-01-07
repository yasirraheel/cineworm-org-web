<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Events\GameControlEvent;

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
}
