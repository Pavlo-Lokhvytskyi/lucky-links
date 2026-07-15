<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AccessLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use RuntimeException;
use Tests\TestCase;

class AccessLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_issues_a_link_valid_for_seven_days(): void
    {
        Carbon::setTestNow('2026-07-14 12:00:00');

        $response = $this->post(route('register.store'), [
            'username' => 'jane',
            'phone_number' => '+380671234567',
        ]);

        $this->assertDatabaseHas('users', ['username' => 'jane', 'phone_number' => '+380671234567']);

        $link = AccessLink::sole();
        $this->assertSame('2026-07-21 12:00:00', $link->expires_at->toDateTimeString());

        $response->assertRedirect($link->url());
        $this->get($link->url())->assertOk();
    }

    public function test_registration_requires_a_username_and_phone_number(): void
    {
        $this->post(route('register.store'), ['username' => '', 'phone_number' => 'not a phone'])
            ->assertSessionHasErrors(['username', 'phone_number']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_rejects_a_phone_number_without_enough_digits(): void
    {
        $this->post(route('register.store'), ['username' => 'jane', 'phone_number' => '(() - ()) 123'])
            ->assertSessionHasErrors(['phone_number']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_a_failed_link_issue_rolls_back_the_registration(): void
    {
        AccessLink::creating(fn () => throw new RuntimeException('boom'));

        try {
            $this->withoutExceptionHandling()->post(route('register.store'), [
                'username' => 'jane',
                'phone_number' => '+380671234567',
            ]);
            $this->fail('Registration should have failed.');
        } catch (RuntimeException) {
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_an_expired_link_is_no_longer_valid(): void
    {
        $link = $this->link(expiresAt: Carbon::now()->subMinute());

        $this->get($link->url())->assertGone();
    }

    public function test_a_deactivated_link_is_no_longer_valid(): void
    {
        $link = $this->link();

        $this->post(route('play.deactivate', $link->token))->assertRedirect(route('register'));

        $this->get($link->url())->assertGone();
    }

    public function test_regenerating_a_link_replaces_the_old_one(): void
    {
        $link = $this->link();

        $response = $this->post(route('play.regenerate', $link->token));

        $fresh = AccessLink::whereKeyNot($link->id)->sole();
        $response->assertRedirect($fresh->url());

        $this->get($fresh->url())->assertOk();
        $this->get($link->url())->assertGone();
    }

    public function test_a_failed_regeneration_keeps_the_old_link_working(): void
    {
        $link = $this->link();

        AccessLink::creating(fn () => throw new RuntimeException('boom'));

        try {
            $this->withoutExceptionHandling()->post(route('play.regenerate', $link->token));
            $this->fail('Regeneration should have failed.');
        } catch (RuntimeException) {
        }

        $this->get($link->url())->assertOk();
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
