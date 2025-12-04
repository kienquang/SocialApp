# Copilot instructions for this Laravel application

This repository is a Laravel 12 application (skeleton). The guidance below is focused, actionable, and specific to this codebase so an AI coding agent can be productive immediately.

Key facts
- PHP >= 8.2, Laravel framework ^12.0 (see `composer.json`).
- Frontend uses Vite + Tailwind; `package.json` exposes `npm run dev` and `npm run build`.
- Tests use PHPUnit configured in `phpunit.xml` with an in-memory sqlite DB for fast runs.

Primary developer workflows (commands)
- Setup: run `composer install`, copy `.env.example` to `.env`, `php artisan key:generate`, create `database/database.sqlite` if needed, then `php artisan migrate` or run `composer setup` which performs many steps (see `composer.json` scripts).
- Local dev (backend + queue + vite): run `composer run-script dev` or use `npx concurrently` via composer `dev` script. For frontend only: `npm run dev`.
- Build assets: `npm run build` (Vite build).
- Tests: `composer test` or `php artisan test` (see `composer.json` scripts and `phpunit.xml`). PHPUnit uses an in-memory sqlite DB by default in this repo.

Project structure & important files
- `app/` — application code. Controllers live in `app/Http/Controllers`, models in `app/Models` (see `User.php`).
- `routes/web.php` — web routes (returns `welcome` view by default).
- `database/` — migrations, seeders, and an included `database.sqlite` file.
- `artisan` — CLI entrypoint for Laravel commands.
- `phpunit.xml` — test environment overrides (sqlite in-memory, queue=sync, etc.).

Patterns and conventions (discoverable from code)
- Eloquent models use `HasFactory` and typed property casts (see `app/Models/User.php`). Prefer model factories in `database/factories` for tests.
- Configuration and environment variables follow default Laravel conventions; tests rely on `phpunit.xml` overrides rather than modifying `.env`.
- Service providers are in `app/Providers` and may register bindings; check `AppServiceProvider.php` for app-level bootstrapping.

When editing code
- Preserve PSR-4 structure (namespace `App\*` maps to `app/`).
- Add or update migration files under `database/migrations` and run `php artisan migrate` (or `composer setup` during CI) when schema changes are needed.
- For new controllers, place them under `app/Http/Controllers` and register routes in `routes/web.php` (or `routes/api.php` if you add one).

Testing guidance
- Use factories in `database/factories` and the in-memory sqlite configuration provided by `phpunit.xml` for fast tests.
- Prefer `php artisan test` (wraps PHPUnit) or `composer test` which clears cached config before running.

Integration & external services
- No explicit external service integrations are present in the skeleton. If you add services, register credentials in `.env` and update `config/*.php` accordingly.

Examples from this repo
- Create a route + controller: add `app/Http/Controllers/MyController.php` with namespace `App\Http\Controllers`, then in `routes/web.php` add `Route::get('/my', [MyController::class, 'index']);`.
- Run tests locally: `composer test` (uses sqlite in-memory defined in `phpunit.xml`).

Rules for the AI agent
- Only make changes consistent with Laravel conventions and PSR-4 autoloading. Do not modify vendor files.
- When adding migrations, include a matching rollback (down) and add a brief comment describing the change.
- Run `composer test` after substantive PHP changes; run `npm run build` after modifying frontend assets.

If anything is ambiguous
- Prefer minimal, backward-compatible changes and create a short PR description that explains the reasoning and references the modified files.

If you want more
- Ask for specific areas to expand (testing patterns, seeding, CI config, API routes, or auth). I can add examples and small automations (tests or composer scripts).
