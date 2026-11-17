# Functional Requirements

Status key: ✅ built · 🟡 partial · ⏳ planned (phase)

## A. Public website
| Requirement | Status |
| --- | --- |
| Landing page: hero, value proposition, Start Practising CTA, How It Works, courses, features, statistics showcase ("What we measure"), leaderboard preview ("Fair leaderboards" rules), classroom features, FAQ, footer | ✅ |
| About, Privacy Policy, Terms pages | ✅ (legal pages marked as drafts pending legal review) |
| Courses, Typing Test, Leaderboard public pages | 🟡 placeholder pages; content in Phase 3–4 |
| Login, Registration | ✅ |
| SEO: server-rendered titles and meta descriptions | 🟡 meta tags done; Inertia SSR enabled at deployment |

## B. Authentication
| Requirement | Status |
| --- | --- |
| Registration with username, email, date of birth, timezone, terms acceptance | ✅ |
| Login with email **or** username | ✅ |
| Logout, email verification, password reset, session management | ✅ (Fortify) |
| Two-factor authentication and passkeys | ✅ (from the starter kit) |
| Profile created with the account (profile, statistics, student role) in one transaction | ✅ |
| Age gate: under the consent age → guardian email required, account pending until approved | ✅ |
| School-managed accounts (no email, username login, no email verification) | 🟡 model + login support; creation by teachers in Phase 5 |
| Teacher access request + admin approval + domain auto-approval | ✅ |
| Suspended accounts cannot sign in and are signed out mid-session | ✅ |
| Account deletion anonymises personal data | ✅ |

## C. Roles and navigation
| Requirement | Status |
| --- | --- |
| Roles Student, Teacher, Admin stored in the database with permissions | ✅ |
| Permissions enforced on the server (route middleware `can:` + Gate) | ✅ |
| Role-based navigation built on the server, exactly as in the brief | ✅ |
| Guests limited to public pages | ✅ |

## D–M. Typing engine, courses, gamification, challenges, leaderboards, profiles, classrooms, notifications, admin portal, monetisation
Planned for Phases 3–7 as described in the Phase 1 proposal. Database tables for Phase 3 (content, typing sessions/attempts, XP ledger, levels, statistics, audit log) already exist.
