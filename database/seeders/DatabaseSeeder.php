<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Permission\Models\Permission;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::info("seeding tables...");

        // Creating role admin and user
        $adminRole = Role::create([
            'name' => 'Admin',
            'guard_name' => 'api',
            'slug' => Str::slug('Admin', '-'),
        ]);
        Role::create([
            'name' => 'User',
            'guard_name' => 'api',
            'slug' => Str::slug('User', '-')
        ]);

        // Creating admin user
        $adminUser = User::create([
            'first_name' => "Admin",
            'last_name' => "Admin",
            'email' => "admin@admin.com",
            'password' => bcrypt('password01'),
            'username' => strtolower("AdminAdmin") . rand(1000, 9999),
            'status' => 1,
        ]);

        $adminUser = User::where('email', 'admin@admin.com')->first();
        // Assigning Admin role to admin user
        $adminUser->assignRole($adminRole);

        // Creating permissions and assigning them to Admin role
        $permissionNames = include base_path('Modules/Role/Database/data/permissions.php');
        foreach ($permissionNames as $permissionName) {
            $permission = Permission::create([
                'name' => $permissionName,
                'guard_name' => 'api',
                'slug' => Str::slug($permissionName, '-')
            ]);

            // Give permission to Admin role
            $adminRole->givePermissionTo($permission);
        }

        Log::info("seeding done!");

    }
}
