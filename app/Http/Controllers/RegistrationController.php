<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Access\AccessLinkService;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('register');
    }

    public function store(RegisterRequest $request, AccessLinkService $links): RedirectResponse
    {
        $link = DB::transaction(
            fn () => $links->issueFor(User::create($request->validated()))
        );

        return redirect($link->url());
    }
}
