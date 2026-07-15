<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Draw extends Model
{
    protected $fillable = [
        'user_id',
        'number',
        'is_win',
        'payout_cents',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'is_win' => 'boolean',
            'payout_cents' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payout(): string
    {
        return number_format($this->payout_cents / 100, 2, '.', '');
    }
}
