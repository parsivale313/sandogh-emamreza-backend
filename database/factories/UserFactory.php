<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone' => $this->faker->unique()->numerify('09#########'),
            'password' => bcrypt('password123'),
            'is_admin' => false,
            'active' => true,
        ];
    }

    public function admin()
    {
        return $this->state(fn() => ['is_admin' => true]);
    }

    public function inactive()
    {
        return $this->state(fn() => ['active' => false]);
    }
}
