<?php

namespace App\Http\Controllers\Extension;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\SessionFocus;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionFocusController
{
    public function start(Request $request): JsonResponse
    {
        $existing = SessionFocus::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'A focus session is already active.'], 409);
        }

        $session = SessionFocus::create([
            'user_id' => Auth::id(),
            'started_at' => now(),
            'expected_duration' => 25 * 60, // 25 minutes in seconds
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Focus session started.', 'session' => $session]);
    }

    public function pause(Request $request): JsonResponse
    {
        $session = $this->getActiveSession();

        if ($session->status !== 'active') {
            return response()->json(['message' => 'Session is not active.'], 400);
        }

        $session->update([
            'paused_at' => now(),
            'status' => 'paused',
        ]);

        return response()->json(['message' => 'Session paused.']);
    }

    public function resume(Request $request): JsonResponse
    {
        $session = $this->getActiveSession();

        if ($session->status !== 'paused') {
            return response()->json(['message' => 'Session is not paused.'], 400);
        }

        $pausedDuration = now()->diffInSeconds(Carbon::parse($session->paused_at));

        $session->update([
            'total_paused_time' => $session->total_paused_time + $pausedDuration,
            'paused_at' => null,
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Session resumed.']);
    }

    public function finish(Request $request): JsonResponse
    {
        $session = $this->getActiveSession();

        if (!in_array($session->status, ['active', 'paused'])) {
            return response()->json(['message' => 'Session already finished.'], 400);
        }

        $now = now();
        $realDuration = $now->diffInSeconds(Carbon::parse($session->started_at)) - $session->total_paused_time;

        $difference = abs($realDuration - $session->expected_duration);

        $session->finished_at = $now;
        $session->status = 'finished';
        $session->is_valid = $difference <= 10;

        if ($session->is_valid) {
            $session->xp_earned = ($realDuration / $session->expected_duration) * 100; // 100 XP for full session
        } else {
            $session->xp_earned = 0;
        }

        $session->save();

        return response()->json([
            'message' => $session->is_valid ? 'Session successfully validated.' : 'Session invalidated due to irregularities.',
            'xp_earned' => $session->xp_earned
        ]);
    }

    private function getActiveSession()
    {
        $session = SessionFocus::where('user_id', Auth::id())
            ->whereIn('status', ['active', 'paused'])
            ->latest()
            ->first();

        if (!$session) {
            abort(404, 'No active session found.');
        }

        return $session;
    }
}
