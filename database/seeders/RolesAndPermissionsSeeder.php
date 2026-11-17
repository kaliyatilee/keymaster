<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionName;
use App\Enums\RoleName;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Reference data required in every environment. Safe to run repeatedly:
 * it upserts roles and permissions and re-syncs their links.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionName::cases() as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission->value],
                ['label' => $permission->label()],
            );
        }

        $ids = Permission::query()->pluck('id', 'name');

        foreach (RoleName::cases() as $roleName) {
            $role = Role::query()->updateOrCreate(
                ['name' => $roleName->value],
                ['label' => $roleName->label()],
            );

            $role->permissions()->sync(array_map(
                fn (PermissionName $p) => $ids[$p->value],
                PermissionName::forRole($roleName),
            ));
        }
    }
}
