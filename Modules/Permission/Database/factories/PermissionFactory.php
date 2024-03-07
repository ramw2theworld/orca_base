<?php

namespace Modules\Permission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Permission\Models\Permission;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $permission_names = include base_path('Modules/Role/Database/data/permissions.php');

        foreach($permission_names as $permission_name) {
            return [
                'name' => $permission_name,
                'slug' => Str::slug($permission_name),
            ];
        }
    }
}
