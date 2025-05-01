<?php

namespace App\Http\Controllers\Extension;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\SessionFocus;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\UserProgressionService;

class SessionFocusController
{
    private $progressionService;

    public function __construct(UserProgressionService $progressionService)
    {
        $this->progressionService = $progressionService;
    }

    public function start(Request $request): JsonResponse
    {
        $params = $request->validate([
            'expected_duration' => 'required|integer|min:5|max:90', // 1 second to 1 hour
        ]);

        $existing = SessionFocus::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($existing) {
            $existing->update([
                'is_valid' => false,
                'status' => 'finished',
                'finished_at' => now(),
            ]);
        }

        $session = SessionFocus::create([
            'user_id' => Auth::id(),
            'started_at' => now(),
            'expected_duration' => $params['expected_duration'] * 60, // Convert to seconds
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
        $realDuration = $now->diffInSeconds(Carbon::parse($session->started_at));
        if($session->total_paused_time) {
            $realDuration -= $session->total_paused_time;
        }

        $difference = abs($realDuration ) - $session->expected_duration;

        $session->finished_at = $now;
        $session->status = 'finished';
        $session->is_valid = $difference <= 180; // 180 seconds tolerance

        if ($session->is_valid) {
            $session->xp_earned = abs(($realDuration / $session->expected_duration) * 10);
            $session->xp_earned = (int) $session->xp_earned;
        } else {
            $session->xp_earned = 0;
        }

        $session->save();

        // Ajouter l'XP au user et mettre à jour son rang
        $user = User::find(Auth::id());
        $progressionData = $this->progressionService->addXpToUser($user, $session->xp_earned);

        return response()->json([
            'message' => $session->is_valid ? 'Session successfully validated.' : 'Session invalidated due to irregularities.',
            'xp_earned' => $session->xp_earned,
            'current_xp' => $progressionData['current_xp'],
            'new_rank' => $progressionData['new_rank'],
            'next_rank_xp' => $progressionData['next_rank_xp'],
            'rank_changed' => $progressionData['rank_changed']
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
