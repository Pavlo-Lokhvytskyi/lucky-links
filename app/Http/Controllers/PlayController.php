<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Access\AccessLinkService;
use App\Domain\Game\LuckyDraw;
use App\Models\AccessLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlayController extends Controller
{
    public function show(AccessLink $link): View
    {
        return view('play', ['link' => $link]);
    }

    public function lucky(AccessLink $link, LuckyDraw $game): RedirectResponse
    {
        $draw = $game->play($link->user);

        return redirect($link->url())->with('draw', [
            'number' => $draw->number,
            'result' => $draw->is_win ? 'Win' : 'Lose',
            'payout' => $draw->payout(),
        ]);
    }

    public function history(AccessLink $link, LuckyDraw $game): View
    {
        return view('history', [
            'link' => $link,
            'draws' => $game->history($link->user),
        ]);
    }

    public function regenerate(AccessLink $link, AccessLinkService $links): RedirectResponse
    {
        return redirect($links->regenerate($link)->url());
    }

    public function deactivate(AccessLink $link, AccessLinkService $links): RedirectResponse
    {
        $links->deactivate($link);

        return redirect()->route('register')->with('status', 'The link has been deactivated.');
    }
}
