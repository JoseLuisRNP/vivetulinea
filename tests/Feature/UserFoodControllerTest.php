<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserFood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFoodControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_foods(): void
    {
        $user = User::factory()->create();
        $userFood = UserFood::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('my-foods.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('MyFoods')
            ->has('userFoods', 1)
            ->where('userFoods.0.id', $userFood->id)
        );
    }

    public function test_user_can_create_food(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('my-foods.store'), [
            'name' => 'Mi alimento',
            'color' => 'blue',
            'points' => 5,
            'quantity' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_foods', [
            'user_id' => $user->id,
            'name' => 'Mi alimento',
            'color' => 'blue',
            'points' => 5,
            'quantity' => 100,
        ]);
    }

    public function test_user_can_update_their_food(): void
    {
        $user = User::factory()->create();
        $userFood = UserFood::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('my-foods.update', $userFood), [
            'name' => 'Alimento actualizado',
            'color' => 'red',
            'points' => 10,
            'quantity' => 200,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_foods', [
            'id' => $userFood->id,
            'name' => 'Alimento actualizado',
            'color' => 'red',
            'points' => 10,
            'quantity' => 200,
        ]);
    }

    public function test_user_cannot_update_other_user_food(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUserFood = UserFood::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put(route('my-foods.update', $otherUserFood), [
            'name' => 'Alimento actualizado',
            'color' => 'red',
            'points' => 10,
            'quantity' => 200,
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_their_food(): void
    {
        $user = User::factory()->create();
        $userFood = UserFood::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('my-foods.destroy', $userFood));

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_foods', [
            'id' => $userFood->id,
        ]);
    }

    public function test_user_cannot_delete_other_user_food(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUserFood = UserFood::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('my-foods.destroy', $otherUserFood));

        $response->assertForbidden();
    }

    public function test_user_can_add_food_as_meal(): void
    {
        $user = User::factory()->create();
        $userFood = UserFood::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mi alimento',
            'color' => 'blue',
            'points' => 5,
            'quantity' => 100,
        ]);

        $response = $this->actingAs($user)->post(route('my-foods.add-meal', $userFood), [
            'time' => 'Desayuno',
            'dayActive' => now()->toISOString(),
            'noCountDay' => false,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('meals', [
            'user_id' => $user->id,
            'name' => 'Mi alimento',
            'color' => 'blue',
            'points' => 5,
            'quantity' => 100,
            'time_of_day' => 'Desayuno',
        ]);
    }

    public function test_user_cannot_add_food_as_meal_on_no_count_day(): void
    {
        $user = User::factory()->create();
        $userFood = UserFood::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('my-foods.add-meal', $userFood), [
            'time' => 'Desayuno',
            'dayActive' => now()->toISOString(),
            'noCountDay' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No se pueden añadir alimentos propios el día de no contar');
        $this->assertDatabaseMissing('meals', [
            'user_id' => $user->id,
            'name' => $userFood->name,
        ]);
    }

    public function test_validation_requires_name_color_points_and_quantity(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('my-foods.store'), []);

        $response->assertSessionHasErrors(['name', 'color', 'points', 'quantity']);
    }

    public function test_validation_requires_valid_color(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('my-foods.store'), [
            'name' => 'Test',
            'color' => 'invalid',
            'points' => 5,
            'quantity' => 100,
        ]);

        $response->assertSessionHasErrors(['color']);
    }
}
