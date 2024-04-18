<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Permission\Models\Permission;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\PaymentProvider\Database\Seeders\CurrencySeeder;
use Modules\PaymentProvider\Database\Seeders\LanguageSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::info("Starting database seeding...");

        // $adminRole = Role::firstOrCreate(
        //     ['name' => 'Admin', 'guard_name' => 'api'],
        //     ['slug' => Str::slug('Admin', '-')]
        // );

        // $userRole = Role::firstOrCreate(
        //     ['name' => 'User', 'guard_name' => 'api'],
        //     ['slug' => Str::slug('User', '-')]
        // );

        // $adminUser = User::firstOrCreate(
        //     ['email' => "admin@admin.com"],
        //     [
        //         'first_name' => "Admin",
        //         'last_name' => "Admin",
        //         'password' => bcrypt('password01'),
        //         'username' => "adminadmin".rand(1000, 9000),
        //         'status' => 1,
        //     ]
        // );

        // $adminUser->assignRole($adminRole);

        // $permissionNames = include base_path('Modules/Role/Database/data/permissions.php');
        // foreach ($permissionNames as $permissionName) {
        //     $permission = Permission::firstOrCreate(
        //         ['name' => $permissionName, 'guard_name' => 'api'],
        //         ['slug' => Str::slug($permissionName, '-')]
        //     );

        //     if (!$adminRole->hasPermissionTo($permission)) {
        //         $adminRole->givePermissionTo($permission);
        //     }
        // }

        //currency table seeder
        $this->call([
            // CurrencySeeder::class,
            LanguageSeeder::class,
            // LanguageSeeder::class,
            // UsersSeeder::class,
            // PaymentProvidersTableSeeder::class,
            // PlansTableSeeder::class,
            // PermissionsTableSeeder::class,
        ]);

        Log::info("Database seeding completed successfully.");
    }
}
