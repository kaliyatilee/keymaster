# Routes

KeyMaster uses Inertia (server routes returning pages), not a public JSON API. A versioned JSON API with Sanctum can be added for a mobile app later.

| Method | URI | Name | Protection |
| --- | --- | --- | --- |
| GET | `/` | home | public |
| GET | `/about`, `/courses`, `/typing-test`, `/leaderboard`, `/privacy`, `/terms` | pages.* | public |
| GET/POST | `/register`, `/login`, `/logout`, `/forgot-password`, `/reset-password`, `/email/verify`, 2FA, passkeys | Fortify | Fortify |
| GET | `/consent/{token}` | consent.show | public, token |
| POST | `/consent/{token}` | consent.respond | public, token, throttle 10/min |
| GET | `/account/pending` | consent.pending | auth |
| POST | `/account/pending/resend` | consent.resend | auth, throttle 3/hour |
| GET | `/dashboard` | dashboard | auth, verified; redirects teachers/admins to their dashboards |
| GET | `/practice`, `/challenges`, `/classes` | practice, challenges, classes | auth, verified, `can:practice.use` (placeholders) |
| GET | `/teacher` and `/teacher/{classes,students,assignments,competitions,reports}` | teacher.* | auth, verified, `can:teacher.access` |
| GET | `/admin` | admin.dashboard | auth, verified, `can:admin.access` |
| GET | `/admin/users` | admin.users.index | + `can:users.manage` |
| GET | `/admin/teacher-requests` | admin.teacher-requests.index | + `can:teachers.approve` |
| POST | `/admin/teacher-requests/{user}/approve` / `reject` | admin.teacher-requests.* | + `can:teachers.approve` |
| GET | `/admin/{courses,lessons,classes,challenges,achievements,analytics,settings}` | admin.* | `can:admin.access` (placeholders) |
| GET/PATCH/DELETE | `/settings/profile` | profile.* | auth (delete also verified + password) |
| GET/PUT | `/settings/security`, `/settings/password` | security.edit, user-password.update | auth, verified |

## Console commands
| Command | Purpose |
| --- | --- |
| `keymaster:db-grants [--print]` | Create/refresh the least-privilege runtime DB account |
| `keymaster:create-admin {email} {username} [--name=]` | Create an administrator |
