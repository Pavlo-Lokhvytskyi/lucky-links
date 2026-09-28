<?php

declare(strict_types=1);

namespace App\Domain\Achievements;

enum AchievementType: string
{
    case Spins = 'spins';
    case Turnover = 'turnover';
}
