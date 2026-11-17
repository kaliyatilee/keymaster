# Testing Strategy

All PHP tests run on **MySQL** (`keymaster_test`) because constraints and grants are part of the design.

| Area | Tests | Type |
| --- | --- | --- |
| Registration | `Auth/RegistrationTest` — complete account creation, DOB encrypted, under-age guardian flow, teacher request, username rules, terms, DOB validation | Feature |
| Consent | `Identity/ParentalConsentTest` — waiting page, approve, decline, expiry, single use, resend | Feature |
| Login | `Identity/LoginTest`, `Auth/AuthenticationTest` — username/email login, school-managed login, suspension, rate limit, 2FA | Feature |
| Permissions | `Identity/RoleAccessTest` — guest/student/teacher/admin access, role home redirect, navigation per role, every nav link reachable, no sensitive shared props | Feature |
| Admin | `Admin/TeacherApprovalTest` — approve/reject audited, unverified blocked, domain auto-approval, user search | Feature |
| Profile | `Settings/ProfileUpdateTest` — updates, privacy for minors, timezone cooldown, anonymised deletion | Feature |
| Database | `Database/AppAccountGrantsTest` (runtime account cannot UPDATE/DELETE ledgers or DROP), `Database/CheckConstraintsTest` | Feature |
| Domain units | `Unit/Identity/DatabaseGrantPlannerTest`, `Identity/AgeGateTest` | Unit / light feature |
| Kit tests | Email verification, password reset/confirmation, 2FA, security settings | Feature |

From Phase 3: Vitest + React Testing Library for the typing engine, shared JSON scoring fixtures run by both Vitest and PHPUnit, Playwright end-to-end tests.

CI also runs Pint, PHPStan (level 7), the frontend linter/formatter, `tsc`, and a migrate → rollback → migrate cycle.
