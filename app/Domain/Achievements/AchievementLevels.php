<?php

declare(strict_types=1);

namespace App\Domain\Achievements;

final class AchievementLevels
{
    private const SPINS = [10, 100, 1000];

    private const TURNOVER = [1_000, 10_000, 100_000, 1_000_000];

    /** @return list<int> */
    public static function for(AchievementType $type): array
    {
        return match ($type) {
            AchievementType::Spins => self::SPINS,
            AchievementType::Turnover => self::TURNOVER,
        };
    }
}
