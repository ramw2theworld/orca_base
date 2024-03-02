<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Models\User;
use Illuminate\Support\Str;
use Modules\Role\Models\Role;


class UserFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // It's better to hash passwords, even in testing
            'username' => strtolower($firstName) . strtolower($lastName) . rand(1000, 9999), // Combining first name, last name, and 4 random digits
            'status' => $this->faker->boolean, // Randomly generates true or false
            'role_id' => Role::query()->inRandomOrder()->first()->id ?? null,

        ];
    }
}
