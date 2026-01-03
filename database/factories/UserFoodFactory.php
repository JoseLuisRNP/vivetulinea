<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserFood>
 */
class UserFoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->words(2, true),
            'color' => fake()->randomElement(['yellow', 'blue', 'green', 'red', 'black']),
            'points' => fake()->randomFloat(1, 1, 20),
            'quantity' => fake()->randomFloat(1, 10, 500),
        ];
    }
}
