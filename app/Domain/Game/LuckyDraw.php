<?php

declare(strict_types=1);

namespace App\Domain\Game;

use App\Domain\Achievements\AchievementService;
use App\Models\Draw;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class LuckyDraw
{
    public const HISTORY_SIZE = 3;

    public function __construct(private AchievementService $achievements) {}

    public function play(User $user): Draw
    {
        $outcome = DrawOutcome::forNumber(
            random_int(DrawOutcome::MIN_NUMBER, DrawOutcome::MAX_NUMBER)
        );

        $draw = $user->draws()->create([
            'number' => $outcome->number,
            'is_win' => $outcome->isWin,
            'payout_cents' => $outcome->payoutCents,
        ]);

        $this->achievements->recordSpin($draw);

        return $draw;
    }

    /** @return Collection<int, Draw> */
    public function history(User $user): Collection
    {
        return $user->draws()
            ->latest('id')
            ->limit(self::HISTORY_SIZE)
            ->get();
    }
}
