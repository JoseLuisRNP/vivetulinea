<?php

namespace Database\Factories;

use App\Models\NoCountDay;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoCountDayFactory extends Factory
{
    protected $model = NoCountDay::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => now()->toDateString(),
        ];
    }
}
