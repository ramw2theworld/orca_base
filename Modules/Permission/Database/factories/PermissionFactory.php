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
        $title = $this->faker->word;
        return [
            'name' => $title,
            'slug' => Str::slug($title),
        ];
    }
}
