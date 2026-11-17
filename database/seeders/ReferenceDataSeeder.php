<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Data every environment (including tests and production) needs.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            LevelsSeeder::class,
        ]);
    }
}
