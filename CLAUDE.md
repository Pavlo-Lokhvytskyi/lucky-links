# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Lucky Links: registration by name and phone number. Each user gets a unique link to a private page
("page A") valid for 7 days (configurable via `ACCESS_LINK_TTL_DAYS`). On that page the user can play
"I'm Feeling Lucky" (a server-generated 1–1000 draw), view the last 3 results, regenerate the link, or
deactivate it. Laravel 12, PHP 8.2, MySQL 8, Docker-only dev setup.

## Commands

```bash
# Setup / run (Docker required, no local PHP needed)
cp .env.example .env
docker compose up -d --build
# ready when logs show: Server running on [http://0.0.0.0:8000]

# Run all tests (in-memory SQLite, no MySQL needed)
docker compose exec app php artisan test

# Run a single test file / method
docker compose exec app php artisan test tests/Unit/DrawOutcomeTest.php
docker compose exec app php artisan test --filter=test_method_name

# Format code (Laravel Pint)
docker compose exec app vendor/bin/pint
```

`composer test` runs `config:clear` then `artisan test` — use it if `.env` config was recently changed.

## Architecture

Business logic lives in `app/Domain/`, kept deliberately separate from `app/Http/` and `app/Models/`.
Controllers are thin: they resolve domain services (bound in `AppServiceProvider`) and translate
between HTTP and domain calls. Follow this split for new features — don't put business rules directly
in controllers or Eloquent models.

- **`App\Domain\Access\AccessLinkService`** — issue/regenerate/deactivate access links.
  `regenerate()` deactivates the old link and issues a new one inside a single `DB::transaction`, so a
  leaked token can never outlive its replacement. Draw history belongs to the `User`, not the link, so
  regenerating a link does not wipe history. Bound as a singleton in `AppServiceProvider`, constructed
  with `ttl_days` from `config/access_links.php` (backed by `ACCESS_LINK_TTL_DAYS`).
- **`App\Domain\Game\DrawOutcome`** — pure game rules, no I/O: `forNumber()` validates the 1–1000
  range, decides win/lose (even = win), and computes the payout percentage by range (>900→70%,
  >600→50%, >300→30%, else 10%). This is the single source of truth for game rules; if a feature
  changes payout tiers or win condition, this is the only class that should change.
- **`App\Domain\Game\LuckyDraw`** — orchestration: generates the random number server-side via
  `random_int` (client can never influence the draw), delegates to `DrawOutcome`, persists a `Draw`,
  and fetches the last `HISTORY_SIZE` (3) draws for a user.
- **`App\Http\Middleware\EnsureLinkIsActive`** — guards **every** route under `play/{link:token}`
  (not just the page view) and returns `410 Gone` if the link is expired or deactivated. Any new route
  added under that route group is automatically protected; don't bypass this middleware for new
  page-A actions.
- **Money as integer cents**: `Draw.payout_cents` stores `number * percent`, which is always a whole
  number of cents by construction — never introduce floats for money in this codebase.
- **Route-model binding**: `play/{link:token}` binds `AccessLink` by its `token` column (not `id`).
  `AccessLink::url()` builds the shareable URL via `route('play.show', $token)`.

### Request flow

- `POST /register` → `RegistrationController::store` validates via `RegisterRequest`, creates the
  `User`, and calls `AccessLinkService::issueFor()` inside a `DB::transaction`, then redirects straight
  to the new link's page.
- `play/{link:token}/*` routes (`show`, `history`, `lucky`, `regenerate`, `deactivate`) all sit behind
  `EnsureLinkIsActive` and are handled by `PlayController`, which is a thin adapter over
  `LuckyDraw` and `AccessLinkService`.

### Data model

`User` 1—N `AccessLink` (token, expires_at, deactivated_at) and `User` 1—N `Draw` (number, is_win,
payout_cents). See `database/migrations/` for exact columns.
