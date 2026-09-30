<?php

declare(strict_types=1);

namespace App\Domain\Achievements;

use App\Models\Draw;
use App\Models\User;

final readonly class AchievementService
{
    public function recordSpin(Draw $draw): void
    {
        $user = $draw->user;

        $this->updateProgress($user, AchievementType::Spins, $user->draws()->count());

        $turnover = (int) $user->draws()->sum('number');
        $this->updateProgress($user, AchievementType::Turnover, $turnover);
    }

    private function updateProgress(User $user, AchievementType $type, int $current): void
    {
        foreach (AchievementLevels::for($type) as $level) {
            $achievement = $user->achievements()->firstOrCreate(
                ['type' => $type, 'level' => $level],
                ['progress' => 0, 'status' => AchievementStatus::InProgress],
            );

            if ($achievement->status === AchievementStatus::Completed) {
                continue;
            }

            $achievement->progress = $current;

            if ($current === $level) {
                $achievement->status = AchievementStatus::Completed;
                $achievement->completed_at = now();
            }

            $achievement->save();
        }
    }
}
