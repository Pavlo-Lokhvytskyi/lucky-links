# Lucky Links

Registration by name and phone number. Each user gets a unique link to a private page (page A) that
is valid for 7 days. There you can play "Imfeelinglucky", view the last 3 results, regenerate the
link, or deactivate it.

Laravel 12, PHP 8.2, MySQL 8.

## Running

Only Docker is required.

```bash
cp .env.example .env
docker compose up -d --build
```

On startup the container installs dependencies, generates the application key, and runs the
migrations. As soon as `Server running on [http://0.0.0.0:8000]` shows up in the logs, open
<http://localhost:8000>.

## Tests

```bash
docker compose exec app php artisan test
```

The tests run on in-memory SQLite, so they don't need a running MySQL.

## How it works

* `POST /register` creates the user, issues a link, and immediately redirects to it.
* The link contains a random 40-character token and an `expires_at` 7 days out (configurable via
  `ACCESS_LINK_TTL_DAYS` in `.env`). The `EnsureLinkIsActive` middleware returns `410 Gone` for an
  expired or deactivated link and guards **every** action on page A, not just the page itself.
* Regeneration first kills the current link and only then issues a new one, so a leaked token does
  not outlive its replacement. Draw history belongs to the user, so regeneration does not wipe it.
* `App\Domain\Game\DrawOutcome` holds the game rules and nothing else: an even number is a Win, and
  the payout is 70/50/30/10% of the number depending on its range. The number is generated on the
  server and the result is derived from it, so the client cannot influence the draw.
* Payouts are stored as integers in cents: a payout of `number * percent%` units is exactly
  `number * percent` cents, so rounding error can never reach the money.
