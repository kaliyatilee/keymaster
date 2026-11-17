<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $roleCounts = Role::query()->withCount('users')->pluck('users_count', 'name');

        return Inertia::render('admin/dashboard', [
            'counts' => [
                'users' => User::query()->count(),
                'students' => (int) ($roleCounts[RoleName::Student->value] ?? 0),
                'teachers' => (int) ($roleCounts[RoleName::Teacher->value] ?? 0),
                'admins' => (int) ($roleCounts[RoleName::Admin->value] ?? 0),
                'pending_consent' => User::query()->where('status', AccountStatus::PendingConsent->value)->count(),
                'teacher_requests' => User::query()
                    ->whereNotNull('teacher_requested_at')
                    ->whereDoesntHave('roles', fn ($q) => $q->where('name', RoleName::Teacher->value))
                    ->count(),
            ],
            'recentActivity' => AuditLog::query()
                ->with('actor:id,username')
                ->latest('id')
                ->limit(8)
                ->get()
                ->map(fn (AuditLog $log) => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'actor' => $log->actor?->username,
                    'subject' => $log->auditable_type !== null ? class_basename($log->auditable_type).' #'.$log->auditable_id : null,
                    'at' => $log->created_at->toISOString(),
                ]),
        ]);
    }
}
