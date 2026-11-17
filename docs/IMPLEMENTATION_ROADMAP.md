# Implementation Roadmap and Checklist

| Phase | Scope | Status |
| --- | --- | --- |
| 1 | Discovery and architecture proposal | ✅ Approved 2026-11-17 |
| 2 | Foundation: setup, design system, routing, auth, profiles, roles, navigation, migrations, DB security | 🟡 Code complete; CI is the final check (see below) |
| 3 | Typing engine, scoring, tests, lessons, results, history, student dashboard | ⏳ Next |
| 4 | XP, levels, achievements, streaks, challenges, leaderboards | ⏳ |
| 5 | Classroom: classes, join codes, school-managed accounts, assignments, analytics, competitions, Reverb | ⏳ |
| 6 | Admin portal | ⏳ |
| 7 | Monetisation (AdSense, consent, plans) | ⏳ |
| 8 | QA, accessibility, performance, deployment | ⏳ |

## Phase 2 checklist
- [x] Laravel 13 React starter kit, chisel installer removed, all features kept (2FA, passkeys)
- [x] MySQL as the only supported database; tests run on MySQL
- [x] `config/keymaster.php` (consent age, teacher domains, DB accounts, ads off)
- [x] Migrations: users (extended), identity, content, typing/ledger/audit, CHECK constraints
- [x] `keymaster:db-grants` least-privilege account (verified manually on MySQL 8.0.46: UPDATE/DELETE/DROP denied on ledgers)
- [x] Roles/permissions tables, enums and seeder; levels seeder; dev-only demo seeder
- [x] Registration with username, DOB (encrypted), timezone, terms, guardian email, teacher request
- [x] Login by email or username; suspended users blocked
- [x] Parental consent flow (email, approve/decline, expiry, resend, waiting page)
- [x] Teacher approval (admin page + domain auto-approval), audited
- [x] Role-based route groups and server-built navigation
- [x] Profile settings: username, display name, timezone (weekly limit), daily goal, privacy; anonymising deletion
- [x] Design tokens (brand, XP, typing colours), logo, public layout, landing page, About/Privacy/Terms drafts
- [x] Student, teacher and admin dashboards; admin user list; placeholder pages for later phases
- [x] Feature tests for registration, consent, login, roles, navigation, teacher approval, profile, grants, constraints
- [x] CI with MySQL 8.4: Pint, PHPStan, lint, TypeScript, migrate/rollback, tests
- [ ] CI green on GitHub (could not install PHP packages in the build environment; CI is the first full run)
- [ ] Commit the CI-resolved `composer.lock`
- [ ] Legal review of Privacy Policy and Terms (before public launch)

## Known gaps carried forward
- Courses / Typing Test / Leaderboard public pages are placeholders until Phases 3–4.
- Dashboard streak is fixed at 0 until Phase 4.
- Inertia SSR build is not yet wired into deployment.
