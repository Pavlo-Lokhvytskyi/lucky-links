<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'username',
        'phone_number',
    ];

    /** @return HasMany<AccessLink, $this> */
    public function accessLinks(): HasMany
    {
        return $this->hasMany(AccessLink::class);
    }

    /** @return HasMany<Draw, $this> */
    public function draws(): HasMany
    {
        return $this->hasMany(Draw::class);
    }
}
