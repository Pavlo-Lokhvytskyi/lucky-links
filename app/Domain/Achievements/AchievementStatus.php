<?php

declare(strict_types=1);

namespace App\Domain\Achievements;

enum AchievementStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
