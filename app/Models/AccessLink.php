<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLink extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
            'deactivated_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->deactivated_at === null && $this->expires_at->isFuture();
    }

    public function url(): string
    {
        return route('play.show', $this->token);
    }
}
