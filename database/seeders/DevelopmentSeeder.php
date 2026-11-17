<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo accounts for local development only. Never runs in production.
 * All passwords are "password".
 */
class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        User::factory()->withRole(RoleName::Admin)->create([
            'name' => 'Ada Admin', 'username' => 'ada_admin', 'email' => 'admin@keymaster.test',
        ]);

        User::factory()->withRole(RoleName::Teacher)->create([
            'name' => 'Tendai Teacher', 'username' => 'mr_tendai', 'email' => 'teacher@keymaster.test',
        ]);

        User::factory()->create([
            'name' => 'Sam Student', 'username' => 'sam_types', 'email' => 'student@keymaster.test',
        ]);

        User::factory()->teacherRequested()->create([
            'name' => 'Rudo Applicant', 'username' => 'rudo_m', 'email' => 'applicant@keymaster.test',
        ]);

        User::factory()->pendingConsent('parent@keymaster.test')->create([
            'name' => 'Kid Learner', 'username' => 'kid_learner', 'email' => 'kid@keymaster.test',
        ]);
    }
}
