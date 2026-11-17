# Authentication and Security

## Authentication (Laravel Fortify)
- Registration (`CreateNewUser` → `Domain\Identity\Actions\RegisterUser`): name, username, email, date of birth, password, timezone (from the browser), optional teacher request, terms acceptance. Creates user, profile, statistics row and the Student role in one transaction.
- Login with **email or username** (`FortifyServiceProvider::configureAuthentication`). Suspended users are refused.
- Email verification, password reset, two-factor authentication, passkeys, password confirmation: Fortify defaults from the starter kit.
- School-managed accounts have no email; `User::hasVerifiedEmail()` returns true for them.
- Rate limits: login 5/min per login+IP; consent responses 10/min; consent resend 3/hour.

## Age and consent
- `config/keymaster.php`: `consent_age` (default 13, env `KEYMASTER_CONSENT_AGE`), `minimum_age` 5.
- Under the consent age: `guardian_email` is required; the account is created as `pending_consent`; a single-use link (64 random characters, stored as SHA-256, 14-day expiry) is emailed to the guardian.
- `EnsureAccountIsActive` middleware keeps pending users on `/account/pending` (they can resend the email or log out) and signs out suspended users.
- Under-18 defaults: private profile (cannot be made public), hidden from public leaderboards, no ads (Phase 7).
- Date of birth is encrypted at rest (`App\Casts\EncryptedDate`) and never shared with the browser.

## Roles and permissions
- Tables `roles`, `permissions`, `permission_role`, `role_user`; seeded by `RolesAndPermissionsSeeder` from `App\Enums\Permission::forRole()`.
- `Gate::before` grants an ability when one of the user's roles has a permission of that name; route groups use `can:teacher.access`, `can:admin.access`, etc.
- Ownership rules (e.g. "is this my class?") will be Policies from Phase 5.
- Teacher access: requested at registration, approved by an admin (`/admin/teacher-requests`, audited) or automatically on email verification for domains listed in `KEYMASTER_TEACHER_EMAIL_DOMAINS`.
- Admins are created only from the CLI: `php artisan keymaster:create-admin`.

## Replacing row-level security
1. The browser never connects to MySQL.
2. Every route is behind `auth`/`verified` and a permission gate where needed (tested in `RoleAccessTest`).
3. Shared Inertia props whitelist user fields (tested: no password, DOB or 2FA secrets).
4. **Least-privilege runtime account**: `php artisan keymaster:db-grants` creates `DB_APP_USERNAME` with `SELECT, INSERT, UPDATE, DELETE` per table, but only `SELECT, INSERT` on append-only tables. No DDL rights. Verified in `AppAccountGrantsTest`.
5. CHECK constraints, unique keys and foreign keys reject impossible or duplicate data (`CheckConstraintsTest`).

## Audit log
`Domain\Admin\AuditLogger` records role grants/revocations, teacher approvals/rejections, consent decisions, account deletions and CLI admin creation, with actor, IP and user agent.

## Other defaults
- Production password rule: 12+ characters, mixed case, numbers, symbols, not in known breaches.
- `Model::shouldBeStrict()` outside production.
- Destructive DB commands are blocked in production.
- Account deletion anonymises the user (name, username, email, DOB, password, 2FA cleared; profile deleted) and soft-deletes it; append-only history stays but no longer identifies the person.
