<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Achievements\AchievementLevels;
use App\Domain\Achievements\AchievementStatus;
use App\Domain\Achievements\AchievementType;
use App\Models\AccessLink;
use App\Models\Achievement;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(AccessLink $link): View
    {
        $existing = $link->user->achievements()->get()
            ->keyBy(fn (Achievement $achievement): string => $achievement->type->value.':'.$achievement->level);

        $build = function (AchievementType $type) use ($existing, $link): Collection {
            return collect(AchievementLevels::for($type))->map(
                fn (int $level): Achievement => $existing->get($type->value.':'.$level) ?? new Achievement([
                    'user_id' => $link->user_id,
                    'type' => $type,
                    'level' => $level,
                    'progress' => 0,
                    'status' => AchievementStatus::InProgress,
                ])
            );
        };

        return view('achievements', [
            'link' => $link,
            'spins' => $build(AchievementType::Spins),
            'turnover' => $build(AchievementType::Turnover),
        ]);
    }
}
