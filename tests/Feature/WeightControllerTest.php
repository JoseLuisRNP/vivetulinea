<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Weight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeightControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_a_valid_weight(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 72.5,
            'date' => '2026-05-15',
        ]);

        $response->assertRedirect(route('weights.show'));
        $weight = Weight::where('user_id', $user->id)->first();
        $this->assertNotNull($weight);
        $this->assertSame('72.50', (string) $weight->value);
        $this->assertSame('2026-05-15', $weight->date->toDateString());
    }

    public function test_store_rejects_missing_weight_and_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), []);

        $response->assertSessionHasErrors(['weight', 'date']);
        $this->assertDatabaseCount('weights', 0);
    }

    public function test_store_rejects_non_numeric_weight(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 'not-a-number',
            'date' => '2026-05-15',
        ]);

        $response->assertSessionHasErrors(['weight']);
        $this->assertDatabaseCount('weights', 0);
    }

    public function test_store_rejects_weight_below_minimum(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 19,
            'date' => '2026-05-15',
        ]);

        $response->assertSessionHasErrors(['weight']);
        $this->assertDatabaseCount('weights', 0);
    }

    public function test_store_rejects_weight_above_maximum(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 301,
            'date' => '2026-05-15',
        ]);

        $response->assertSessionHasErrors(['weight']);
        $this->assertDatabaseCount('weights', 0);
    }

    public function test_store_rejects_production_bug_value_84600(): void
    {
        // Regression test: SQLSTATE 22003 reported 2026-05-29.
        // 84600 must be rejected by validation before hitting the DB,
        // returning a 422-style redirect with errors instead of a 500.
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 84600,
            'date' => '2026-05-15',
        ]);

        $response->assertSessionHasErrors(['weight']);
        $this->assertDatabaseCount('weights', 0);
    }

    public function test_store_rejects_more_than_two_decimal_places(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 72.555,
            'date' => '2026-05-15',
        ]);

        $response->assertSessionHasErrors(['weight']);
        $this->assertDatabaseCount('weights', 0);
    }

    public function test_store_accepts_boundary_min_value(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 20,
            'date' => '2026-05-15',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('weights', ['user_id' => $user->id, 'value' => 20]);
    }

    public function test_store_accepts_boundary_max_value(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('weight.store'), [
            'weight' => 300,
            'date' => '2026-05-15',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('weights', ['user_id' => $user->id, 'value' => 300]);
    }

    public function test_update_applies_same_validation_rules(): void
    {
        $user = User::factory()->create();
        $weight = Weight::create([
            'user_id' => $user->id,
            'value' => 72.5,
            'date' => '2026-05-10',
        ]);

        $response = $this->actingAs($user)->patch(
            route('weight.update', ['weight' => $weight->id]),
            ['weight' => 84600, 'date' => '2026-05-15']
        );

        $response->assertSessionHasErrors(['weight']);
        $this->assertDatabaseHas('weights', [
            'id' => $weight->id,
            'value' => 72.5,
        ]);
    }

    public function test_update_accepts_valid_weight(): void
    {
        $user = User::factory()->create();
        $weight = Weight::create([
            'user_id' => $user->id,
            'value' => 72.5,
            'date' => '2026-05-10',
        ]);

        $response = $this->actingAs($user)->patch(
            route('weight.update', ['weight' => $weight->id]),
            ['weight' => 73.2, 'date' => '2026-05-15']
        );

        $response->assertRedirect(route('weights.show'));
        $weight->refresh();
        $this->assertSame('73.20', (string) $weight->value);
        $this->assertSame('2026-05-15', $weight->date->toDateString());
    }
}
