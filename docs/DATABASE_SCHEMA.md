# Database Schema

MySQL 8.0.16+, InnoDB, `utf8mb4`, times stored in UTC. All changes go through migrations in `database/migrations`.
Append-only tables (runtime account has `SELECT, INSERT` only): `xp_transactions`, `typing_attempts`, `typing_attempt_logs`, `attempt_moderations`, `audit_logs`.

## Implemented in Phase 2

### Identity
| Table | Purpose | Key columns | Constraints / indexes |
| --- | --- | --- | --- |
| `users` | Login identity | `name`, `username` (unique), `email` (nullable, unique), `password`, `date_of_birth` (encrypted text), `account_type` enum(self, school_managed), `status` enum(active, pending_consent, suspended), `timezone`, `timezone_changed_at`, `teacher_requested_at`, `last_login_at`, 2FA columns, soft deletes | idx (`status`,`created_at`), idx `teacher_requested_at` |
| `roles` | Role definitions | `name` (unique), `label` | |
| `permissions` | Permission definitions | `name` (unique), `label` | |
| `permission_role` | Role → permissions | PK (`permission_id`,`role_id`) | cascade on delete |
| `role_user` | User → roles | PK (`role_id`,`user_id`), `granted_by`, `created_at` | idx `user_id` |
| `profiles` | Public-facing profile | PK `user_id`, `display_name`, `avatar_path`, `bio`, `country`, `is_public` (default false), `show_on_leaderboards`, `keyboard_layout`, `daily_goal_minutes` | |
| `parental_consents` | Guardian approval | `user_id`, `guardian_email`, `token_hash` (unique, SHA-256), `status`, `expires_at`, `responded_at`, `responder_ip` | idx (`user_id`,`status`); CHECK `expires_at > created_at` |
| `schools` | Institutions (used from Phase 5) | `name`, `country`, `contact_email`, `consent_agreement_at`, `status` | soft deletes |

### Content (used from Phase 3)
| Table | Key columns | Constraints |
| --- | --- | --- |
| `courses` | `slug` (unique), `title`, `level`, `sort_order`, `status`, `published_at` | idx (`status`,`sort_order`) |
| `lessons` | `course_id`, `slug`, `practice_mode` (11 modes), `difficulty`, `passing_accuracy`, `target_wpm`, `xp_reward`, `backspace_mode`, `time_limit_seconds`, `status` | unique (`course_id`,`slug`); CHECK difficulty 1–5, accuracy 0–100 |
| `lesson_content` | `lesson_id`, `version`, `body`, `char_count`, `checksum`, `is_current` | unique (`lesson_id`,`version`) |
| `text_passages` | `source`, `owner_id`, `body`, `char_count`, `checksum`, `status` | idx (`source`,`status`) |
| `lesson_progress` | `user_id`, `lesson_id`, `status`, `best_net_wpm`, `best_accuracy`, `attempts_count` | unique (`user_id`,`lesson_id`); CHECKs on ranges |

### Typing, XP and audit
| Table | Key columns | Constraints |
| --- | --- | --- |
| `levels` | `level_number` (unique), `name`, `xp_required` (unique) | seeded with 15 levels |
| `typing_sessions` | ULID `id`, `user_id`, `context_type`, `content_type/id`, `content_checksum`, `rules` JSON, `issued_at`, `expires_at`, `consumed_at` | CHECK `expires_at > issued_at` |
| `typing_attempts` | `session_id` (unique), timings (ms precision), `chars_typed/correct/incorrect`, `corrections`, `gross_wpm`, `net_wpm`, `accuracy`, `completed`, `validation_status`, `validation_flags` | append-only; CHECKs: elapsed > 0, ended ≥ started, correct + incorrect = typed, accuracy 0–100, 0 ≤ net ≤ gross |
| `typing_attempt_logs` | PK `attempt_id`, `log_gz` (compressed keystrokes) | append-only; pruned after 90 days (Phase 3 job) |
| `attempt_moderations` | `attempt_id`, `decision`, `reason`, `moderator_id` | append-only |
| `xp_transactions` | `user_id`, `amount`, `rule_key`, `source_type/id`, `idempotency_key` (unique), `created_by` | append-only; CHECK amount ≠ 0 |
| `user_statistics` | PK `user_id`, `xp_total`, `level_id`, `best_net_wpm`, `attempts_count`, sums, `total_practice_ms`, `lessons_completed` | written only by domain code |
| `audit_logs` | `actor_id`, `action`, `auditable_type/id`, `old_values`, `new_values`, `ip_address`, `user_agent` | append-only |
| `settings` | `key` PK, `value` JSON | |

Framework tables: `sessions`, `password_reset_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `passkeys`.

## Planned
Phase 4: `xp_rules`, `achievements`, `user_achievements`, `user_streaks`, `practice_days`, `challenges`, `challenge_attempts`, `leaderboards`, `leaderboard_entries`.
Phase 5: `classes`, `class_members`, `assignments`, `assignment_submissions`, `competitions`, `competition_participants`, `announcements`.
Phase 5–7: `notification_preferences` (+ Laravel `notifications`), `plans`, `subscriptions`, `ad_placements`.
Full column lists are in the Phase 1 proposal.
