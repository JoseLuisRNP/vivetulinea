<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Food>
 */
class FoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'points' => fake()->randomFloat(2, 0, 10),
            'quantity' => fake()->randomFloat(2, 1, 100),
            'unit' => fake()->randomElement(['g', 'ml', 'unidad']),
            'color' => fake()->randomElement(['blue', 'green', 'yellow', 'red', 'black']),
            'no_count' => false,
            'special_no_count' => false,
            'oil_no_count' => false,
        ];
    }
}
