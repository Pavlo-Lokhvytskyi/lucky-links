<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Access\AccessLinkService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            AccessLinkService::class,
            fn (): AccessLinkService => new AccessLinkService((int) config('access_links.ttl_days')),
        );
    }

    public function boot(): void
    {
        //
    }
}
