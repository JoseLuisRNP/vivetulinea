<?php

namespace Database\Factories;

use App\Models\Meal;
use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    protected $model = Meal::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipe_id' => null,
            'name' => fake()->word(),
            'points' => fake()->randomFloat(1, 0, 10),
            'quantity' => fake()->randomFloat(1, 1, 100),
            'consumed_at' => now(),
            'time_of_day' => fake()->randomElement(['desayuno', 'comida', 'cena', 'picoteo']),
            'color' => 'blue',
        ];
    }
}
