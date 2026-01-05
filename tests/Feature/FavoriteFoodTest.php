<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteFoodTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_food_as_favorite(): void
    {
        $user = User::factory()->create();
        $food = Food::factory()->create();

        // Toggle favorite on
        $response = $this
            ->actingAs($user)
            ->post(route('favorites.toggle', ['food' => $food->id]));

        $response->assertOk();
        $response->assertJson(['is_favorite' => true]);
        $this->assertTrue($user->favoriteFoods()->where('food_id', $food->id)->exists());

        // Toggle favorite off
        $response = $this
            ->actingAs($user)
            ->post(route('favorites.toggle', ['food' => $food->id]));

        $response->assertOk();
        $response->assertJson(['is_favorite' => false]);
        $this->assertFalse($user->favoriteFoods()->where('food_id', $food->id)->exists());
    }

    public function test_favorite_foods_appear_first_in_food_list(): void
    {
        $user = User::factory()->create();
        $favoriteFood = Food::factory()->create(['name' => 'Zebra Food']);
        $normalFood = Food::factory()->create(['name' => 'Apple Food']);
        $anotherNormalFood = Food::factory()->create(['name' => 'Banana Food']);

        // Mark one as favorite
        $user->favoriteFoods()->attach($favoriteFood->id);

        $response = $this
            ->actingAs($user)
            ->get(route('foods.index'));

        $response->assertOk();
        $foods = $response->json('props.foods.data');
        
        // First food should be the favorite
        $this->assertEquals($favoriteFood->id, $foods[0]['id']);
        $this->assertTrue($foods[0]['is_favorite']);
        
        // Other foods should not be favorites
        $this->assertFalse($foods[1]['is_favorite']);
        $this->assertFalse($foods[2]['is_favorite']);
    }

    public function test_favorite_foods_appear_first_in_dashboard_search(): void
    {
        $user = User::factory()->create();
        $favoriteFood = Food::factory()->create(['name' => 'Favorite Banana']);
        $normalFood = Food::factory()->create(['name' => 'Normal Banana']);

        // Mark one as favorite
        $user->favoriteFoods()->attach($favoriteFood->id);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard', ['q' => 'Banana']));

        $response->assertOk();
        $resultSearch = $response->json('props.resultSearch');
        
        // Check that results exist
        $this->assertNotEmpty($resultSearch);
        
        // Find favorite and normal foods in results
        $favoriteResult = collect($resultSearch)->firstWhere('id', $favoriteFood->id);
        $normalResult = collect($resultSearch)->firstWhere('id', $normalFood->id);
        
        // Favorite food should be marked as favorite
        $this->assertNotNull($favoriteResult);
        $this->assertTrue($favoriteResult['is_favorite']);
        
        // Normal food should not be favorite
        $this->assertNotNull($normalResult);
        $this->assertFalse($normalResult['is_favorite']);
        
        // Favorite should appear before normal food in the list
        $favoriteIndex = collect($resultSearch)->search(fn($item) => $item['id'] === $favoriteFood->id);
        $normalIndex = collect($resultSearch)->search(fn($item) => $item['id'] === $normalFood->id);
        $this->assertLessThan($normalIndex, $favoriteIndex);
    }
}
