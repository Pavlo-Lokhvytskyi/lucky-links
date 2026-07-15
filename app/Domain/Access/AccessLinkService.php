<?php

declare(strict_types=1);

namespace App\Domain\Access;

use App\Models\AccessLink;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class AccessLinkService
{
    public function __construct(private int $ttlDays) {}

    public function issueFor(User $user): AccessLink
    {
        return $user->accessLinks()->create([
            'token' => Str::random(40),
            'expires_at' => Carbon::now()->addDays($this->ttlDays),
        ]);
    }

    public function regenerate(AccessLink $link): AccessLink
    {
        return DB::transaction(function () use ($link): AccessLink {
            $this->deactivate($link);

            return $this->issueFor($link->user);
        });
    }

    public function deactivate(AccessLink $link): void
    {
        $link->update(['deactivated_at' => Carbon::now()]);
    }
}
