<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AccessLink;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLinkIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $link = $request->route('link');

        abort_if(! $link instanceof AccessLink || ! $link->isActive(), 410, 'This link is no longer valid.');

        return $next($request);
    }
}
