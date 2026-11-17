# Deployment Guide

Target: a VPS managed with Laravel Forge or Ploi, or Laravel Cloud. Shared hosting is not supported (queues and, from Phase 5, WebSockets need long-running processes).

## Server
- PHP 8.4 with `pdo_mysql`, `intl`, `mbstring`, `redis`; Nginx; Node 22 for builds.
- MySQL 8.4 LTS; Redis for cache, sessions and queues.
- Queue worker (`php artisan queue:work`) and scheduler (`* * * * * php artisan schedule:run`).

## Database accounts
1. `keymaster_migrator` — owns the schema; used only by the deploy script. Needs `CREATE USER` and `GRANT OPTION` to run `keymaster:db-grants`.
2. `keymaster_app` — created by `keymaster:db-grants`; used by web and queue processes.
3. Optional `keymaster_readonly` for reporting and backups.

## Environment
Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, mail settings, `DB_*` (migrator for the deploy step), `DB_APP_PASSWORD`, `KEYMASTER_CONSENT_AGE`, `KEYMASTER_TEACHER_EMAIL_DOMAINS`, `ADS_ENABLED=false` until Phase 7. For runtime processes, point `DB_USERNAME`/`DB_PASSWORD` at the app account.

## Deploy script (zero-downtime release)
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force                 # as migrator
php artisan db:seed --class=ReferenceDataSeeder --force
php artisan keymaster:db-grants             # refresh per-table grants for new tables
php artisan config:cache route:cache view:cache event:cache
php artisan queue:restart
```

## First run
`php artisan keymaster:create-admin you@school.org admin_username --name="Your Name"`

## Before public launch
Legal review of Privacy Policy and Terms for launch countries; encrypted backups with a restore drill; HTTPS with HSTS; error monitoring.
