<?php

namespace Modules\Role\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Role\Models\Role;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->jobTitle;
        return [
            'name' => $title,
            'slug' => Str::slug($title),
        ];
    }
}
