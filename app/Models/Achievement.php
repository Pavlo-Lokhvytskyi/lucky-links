<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Achievements\AchievementStatus;
use App\Domain\Achievements\AchievementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'level',
        'progress',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AchievementType::class,
            'level' => 'integer',
            'progress' => 'integer',
            'status' => AchievementStatus::class,
            'completed_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
