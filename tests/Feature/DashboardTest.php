<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\Meal;
use App\Models\NoCountDay;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'daily_points' => 30,
            'weekly_points' => 35,
            'created_at' => Carbon::now()->startOfWeek()->subDays(1), // Ensure alignment
        ]);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_access_dashboard(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Dashboard')
                    ->has('meals')
                    ->has('remainingPoints')
            );
    }

    public function test_dashboard_shows_meals_for_specific_day(): void
    {
        $today = Carbon::now()->toDateString();
        $yesterday = Carbon::now()->subDay()->toDateString();

        Meal::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => $today,
            'points' => 5,
            'time_of_day' => 'desayuno'
        ]);

        Meal::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => $yesterday,
            'points' => 10,
            'time_of_day' => 'comida'
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['dayActive' => $today]))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->has('meals.desayuno', 1)
                    ->missing('meals.comida')
                    ->where('remainingPoints', 25)
            );
    }

    public function test_calculates_remaining_points_correctly(): void
    {
        $today = Carbon::now()->toDateString();

        Meal::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => $today,
            'points' => 12.5,
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['dayActive' => $today]))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->where('remainingPoints', "17.5")
            );
    }

    public function test_handles_no_count_days(): void
    {
        $today = Carbon::now()->toDateString();

        NoCountDay::factory()->create([
            'user_id' => $this->user->id,
            'date' => $today,
        ]);

        Meal::factory()->create([
            'user_id' => $this->user->id,
            'consumed_at' => $today,
            'points' => 10,
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['dayActive' => $today]))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->where('noCountDay', true)
                    ->where('remainingPoints', 30) // Points don't subtract on no count days
            );
    }

    public function test_calculates_weekly_remaining_points_correctly(): void
    {
        $monday = Carbon::now()->startOfWeek();

        // Day 1: 35 points (5 over limit of 30)
        Meal::factory()->create(['user_id' => $this->user->id, 'consumed_at' => $monday->toDateString(), 'points' => 35]);

        // Day 2: 32 points (2 over limit of 30)
        Meal::factory()->create(['user_id' => $this->user->id, 'consumed_at' => $monday->copy()->addDay()->toDateString(), 'points' => 32]);

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->where('weekRemainingPoints', 28)
                    ->etc()
            );
    }

    public function test_calculates_points_for_recipes_with_multipliers(): void
    {
        $today = Carbon::now()->toDateString();

        $recipe = Recipe::factory()->create([
            'user_id' => $this->user->id,
            'quantity' => 2, // 2 servings
            'proteins' => 10,
            'sugars' => 4,
            'fats' => 6,
            'empty_points' => 2,
        ]);

        // Consuming 1 serving (0.5 multiplier)
        Meal::factory()->create([
            'user_id' => $this->user->id,
            'recipe_id' => $recipe->id,
            'consumed_at' => $today,
            'quantity' => 1,
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['dayActive' => $today]))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->where('pointsByColor.blue', 5)
                    ->where('pointsByColor.green', 2)
                    ->where('pointsByColor.red', 3)
                    ->where('pointsByColor.yellow', 1)
            );
    }

    public function test_search_food_returns_results_ordered_by_favorites_and_prefix(): void
    {
        $food1 = Food::factory()->create(['name' => 'Manzana Roja']);
        $food2 = Food::factory()->create(['name' => 'Manzana Verde']);
        $food3 = Food::factory()->create(['name' => 'Anana']);

        $this->user->favoriteFoods()->attach($food2->id);

        $this->actingAs($this->user)
            ->get(route('dashboard', ['q' => 'Manzana']))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->has('resultSearch', 2)
                    ->where('resultSearch.0.id', $food2->id) // Favorite first
                    ->where('resultSearch.1.id', $food1->id)
            );
    }

    public function test_guidelines_are_automatically_created_for_the_day(): void
    {
        $today = Carbon::now()->toDateString();

        $this->actingAs($this->user)
            ->get(route('dashboard', ['dayActive' => $today]))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->has('guideline')
                    ->where('guideline.consumed_at', fn($val) => str_starts_with($val, $today))
            );

        $this->assertDatabaseHas('guidelines', [
            'user_id' => $this->user->id,
        ]);

        $guideline = \App\Models\Guideline::where('user_id', $this->user->id)->first();
        $this->assertTrue(str_starts_with($guideline->consumed_at, $today));
    }
}
