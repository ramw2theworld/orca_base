<?php

namespace Modules\Role\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Role\Models\Role;

class RolesSeeder extends Seeder
{
        /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 5 roles
        Role::factory()->count(5)->create();
    }
}
