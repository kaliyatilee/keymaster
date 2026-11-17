# System Architecture

A **modular monolith**: one Laravel 13 application, one MySQL database, React pages rendered through Inertia.js.

```
Browser (React + TypeScript, Inertia)
   │  HTTPS (Inertia)                    WebSocket (Phase 5, Reverb)
   ▼
Laravel 13
   ├─ HTTP layer: controllers, Form Requests, middleware, policies/gates
   ├─ Domain (app/Domain/*): actions, rules, events, notifications
   ├─ Eloquent models (app/Models)
   └─ Queue + scheduler
   ▼
MySQL 8.4 (least-privilege runtime account) · Redis (cache/queue, production) · Mail · File storage
```

## Layers
| Layer | Location | Rule |
| --- | --- | --- |
| Presentation | `resources/js` | Renders props; no permission or scoring decisions |
| HTTP | `app/Http` | Validate, authorise, call one domain action, respond |
| Domain | `app/Domain/<Area>` | Business rules: `Identity`, `Admin` today; `Typing`, `Gamification`, … later |
| Data | `app/Models` | Eloquent models and relationships |
| Integrations | `app/Integrations` (later) | Mail, ads, payments behind interfaces |

## Folder structure (current)
```
app/
  Actions/Fortify/          Fortify hooks (delegate to domain actions)
  Casts/EncryptedDate.php   Encrypted date of birth
  Console/Commands/         keymaster:db-grants, keymaster:create-admin
  Domain/
    Admin/                  AuditLogger, DatabaseGrantPlanner
    Identity/               AgeGate, NavigationBuilder, Actions/, Listeners/, Notifications/
  Enums/                    AccountStatus, AccountType, RoleName, Permission, ConsentStatus
  Http/Controllers/{Public,Auth,Admin,Teacher,Settings}
  Http/Middleware/          EnsureAccountIsActive, HandleInertiaRequests
  Models/
routes/  web.php (public, consent, dashboard) · student.php · teacher.php · admin.php · settings.php
resources/js/
  pages/{public,consent,admin,teacher,auth,settings}
  layouts/public-layout.tsx, app layouts from the starter kit
  components/{ui,public,dashboard}
  lib/ (nav-icons, age)
```

## Key decisions
| Decision | Reason |
| --- | --- |
| Laravel React starter kit as the base | Fortify auth, 2FA, passkeys, Inertia, Tailwind and shadcn/ui components already integrated and tested |
| Models in `app/Models`, domain logic in `app/Domain` | Keeps Laravel conventions (factories, discovery) while business rules stay out of controllers. Deviation from the proposal, which put models inside domains |
| In-house RBAC (4 tables) instead of spatie/laravel-permission | Small role set; avoids a dependency; permission checks easy to audit |
| PHPUnit instead of Pest | The starter kit ships PHPUnit; avoids a second test runner |
| Navigation built on the server | The client never decides what a role may see |
| MySQL grants as the RLS replacement | Database-level guarantee that ledgers cannot be rewritten |
| Placeholder pages for later phases | Navigation works end to end and says honestly which phase delivers each feature |
