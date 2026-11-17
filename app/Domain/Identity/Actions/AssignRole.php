<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Admin\AuditLogger;
use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;

class AssignRole
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function handle(User $user, RoleName $role, ?User $grantedBy = null): void
    {
        $model = Role::query()->where('name', $role->value)->firstOrFail();

        if ($user->roles()->whereKey($model->getKey())->exists()) {
            return;
        }

        $user->roles()->attach($model->getKey(), [
            'granted_by' => $grantedBy?->getKey(),
            'created_at' => now(),
        ]);
        $user->flushPermissionCache();

        if ($grantedBy !== null) {
            $this->audit->record('role.granted', $user, new: ['role' => $role->value], actor: $grantedBy);
        }
    }

    public function revoke(User $user, RoleName $role, User $revokedBy): void
    {
        $model = Role::query()->where('name', $role->value)->firstOrFail();

        if ($user->roles()->detach($model->getKey()) > 0) {
            $user->flushPermissionCache();
            $this->audit->record('role.revoked', $user, old: ['role' => $role->value], actor: $revokedBy);
        }
    }
}
