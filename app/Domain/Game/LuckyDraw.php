<?php

declare(strict_types=1);

namespace App\Domain\Game;

use App\Models\Draw;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class LuckyDraw
{
    public const HISTORY_SIZE = 3;

    public function play(User $user): Draw
    {
        $outcome = DrawOutcome::forNumber(
            random_int(DrawOutcome::MIN_NUMBER, DrawOutcome::MAX_NUMBER)
        );

        return $user->draws()->create([
            'number' => $outcome->number,
            'is_win' => $outcome->isWin,
            'payout_cents' => $outcome->payoutCents,
        ]);
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
