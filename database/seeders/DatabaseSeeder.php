<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Permission\Models\Permission;
use Modules\Role\Database\Seeders\PermissionRoleTableSeeder;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::info("seeding tables...");
        Role::factory()->count(5)->create();
        User::factory()->count(25)->create();

        $permissionNames = include base_path('Modules/Role/Database/data/permissions.php');
        foreach ($permissionNames as $permissionName) {
            Permission::create([
                'name' => $permissionName,
                'slug' => Str::slug($permissionName),
            ]);
        }
        Log::info("seeding done!");

    }
}
