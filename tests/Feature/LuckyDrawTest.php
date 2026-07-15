<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AccessLink;
use App\Models\Draw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LuckyDrawTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_draw_is_recorded_and_shown_back_to_the_user(): void
    {
        $link = $this->link();

        $this->post(route('play.lucky', $link->token))->assertRedirect($link->url());

        $draw = Draw::sole();
        $this->assertSame($link->user_id, $draw->user_id);
        $this->assertGreaterThanOrEqual(1, $draw->number);
        $this->assertLessThanOrEqual(1000, $draw->number);

        $this->get($link->url())
            ->assertOk()
            ->assertSee((string) $draw->number)
            ->assertSee($draw->is_win ? 'Win' : 'Lose');
    }

    public function test_history_shows_the_last_three_draws_only(): void
    {
        $link = $this->link();

        foreach ([10, 20, 30, 40] as $number) {
            $link->user->draws()->create([
                'number' => $number,
                'is_win' => true,
                'payout_cents' => $number * 10,
            ]);
        }

        $this->get(route('play.history', $link->token))
            ->assertOk()
            ->assertSee('40')
            ->assertSee('30')
            ->assertSee('20')
            ->assertDontSee('<td>10</td>', escape: false);
    }

    public function test_playing_through_a_dead_link_is_rejected(): void
    {
        $link = $this->link(expiresAt: Carbon::now()->subMinute());

        $this->post(route('play.lucky', $link->token))->assertGone();

        $this->assertDatabaseCount('draws', 0);
    }

    private function link(?Carbon $expiresAt = null): AccessLink
    {
        $user = User::create(['username' => 'jane', 'phone_number' => '+380671234567']);

        return $user->accessLinks()->create([
            'token' => 'test-token',
            'expires_at' => $expiresAt ?? Carbon::now()->addDays(7),
        ]);
    }
}
