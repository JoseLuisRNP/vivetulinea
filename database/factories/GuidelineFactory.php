<?php

namespace Database\Factories;

use App\Models\Guideline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuidelineFactory extends Factory
{
    protected $model = Guideline::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'water' => 0,
            'fruit' => 0,
            'vegetable' => 0,
            'sport' => 0,
            'consumed_at' => now(),
        ];
    }
}
