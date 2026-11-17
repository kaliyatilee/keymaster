# Product Requirements

## Vision
KeyMaster is a web-based keyboard-learning platform that helps people improve typing speed, accuracy and keyboard proficiency, combining structured courses, daily practice, gamification and fair competition.

## Audiences
1. **Individual learners** of any age who want to type faster and more accurately.
2. **Schools and teachers** who want to teach keyboarding and measure student progress.

## Principles
- Easy for beginners; engaging enough for daily use.
- Measurable outcomes (WPM, accuracy, practice time, streaks).
- Fair: every score that earns XP or a leaderboard place is validated on the server.
- Safe for children: parental or school consent, private profiles for under-18s, no ads for under-18s.
- Works on desktop, tablet and mobile browsers.

## Business model
1. Google AdSense on non-typing pages for adult, consenting users (Phase 7).
2. Later: ad-free subscriptions, premium teacher features, school licences.
No payment processing in the MVP.

## Decisions taken in the Phase 1 proposal (approved 2026-11-17)
| Topic | Decision |
| --- | --- |
| Stack | Laravel 13 + MySQL 8.4 + React/TypeScript via Inertia (replaces the Supabase/PostgreSQL brief) |
| Hosting | VPS (Forge/Ploi) or Laravel Cloud; not shared hosting |
| Children | School-managed accounts (no email), parental consent under the consent age (default 13), no ads for under-18s |
| Teachers | Admin approval, or automatic for configured school email domains |
