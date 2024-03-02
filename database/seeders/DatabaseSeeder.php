<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Permission;
use Modules\Role\Database\Seeders\PermissionRoleTableSeeder;
use Modules\Role\Models\Role;
use Modules\User\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Role::factory()->count(5)->create();
        // User::factory()->count(12)->create();
        // Permission::factory()->count(50)->create();
        // $this->call(PermissionRoleTableSeeder::class);
    }
}
