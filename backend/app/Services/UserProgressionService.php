<?php

namespace App\Services;

use App\Models\Ranking;
use App\Models\User;

class UserProgressionService
{
    public function addXpToUser(User $user, float $xp): array
    {
        $currentXp = $user->xp + $xp;
        $oldRank = $this->calculateRank($user->xp);
        $newRank = $this->calculateRank($currentXp);

        $user->xp = $currentXp;
        $user->rank = $newRank;
        $user->save();

        return [
            'xp_earned' => $xp,
            'current_xp' => $currentXp,
            'new_rank' => $newRank,
            'next_rank_xp' => $this->getNextRankXp($currentXp),
            'rank_changed' => $oldRank !== $newRank
        ];
    }

    private function calculateRank(float $xp): string
    {
        $rank = 'E';
        foreach (Ranking::RANKS as $rankName => $requiredXp) {
            if ($xp >= $requiredXp) {
                $rank = $rankName;
            } else {
                break;
            }
        }
        return $rank;
    }

    public function getNextRankXp(float $currentXp): ?int
    {
        foreach (Ranking::RANKS as $requiredXp) {
            if ($currentXp < $requiredXp) {
                return $requiredXp;
            }
        }
        return null; // Si le joueur a atteint le rang maximum
    }
}
