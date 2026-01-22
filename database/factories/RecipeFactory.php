<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'quantity' => 1,
            'unit' => 'ración',
            'points' => 0,
            'proteins' => fake()->randomFloat(1, 0, 10),
            'sugars' => fake()->randomFloat(1, 0, 10),
            'fats' => fake()->randomFloat(1, 0, 10),
            'empty_points' => fake()->randomFloat(1, 0, 10),
        ];
    }
}
