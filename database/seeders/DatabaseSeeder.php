<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Permission\Models\Permission;
use Modules\Role\Database\Seeders\PermissionRoleTableSeeder;
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
            'slug' => 'admin',
        ]);
        Role::create([
            'name' => 'User',
            'slug' => 'user',
        ]);
        Role::factory()->count(3)->create();

        // admin
        User::create([
            'first_name' => "Admin",
            'last_name' => "Admin",
            'email' => "admin@admin.com",
            'password' => bcrypt('password01'),
            'username' => strtolower("Admin") . strtolower("Admin") . rand(1000, 9999),
            'status' => 1,
            'role_id' => Role::where(DB::raw('LOWER(name)'), 'admin')
                            ->inRandomOrder()
                            ->first()
                            ->id ?? null,
        ]);

        User::factory()->count(25)->create();

        $permissionNames = include base_path('Modules/Role/Database/data/permissions.php');
        foreach ($permissionNames as $permissionName) {
            $permissionCreated = Permission::create([
                'name' => $permissionName,
                'slug' => Str::slug($permissionName),
            ]);

            $adminRole->givePermissionTo($permissionCreated);
        }
        Log::info("seeding done!");

    }
}
