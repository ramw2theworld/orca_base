<?php

namespace Modules\Role\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Permission\Models\Permission;
use Modules\Role\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        // Assuming you want to attach all permissions to a specific role
        $permissions = Permission::all();
        
        Role::all()->each(function ($role) use ($permissions) {
            // Attach all permissions to each role
            // You can customize this logic based on your needs
            $role->permissions()->attach($permissions);
        });
    }
}
