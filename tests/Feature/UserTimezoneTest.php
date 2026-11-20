<?php

namespace Tests\Feature;

use App\Models\Meal;
use App\Models\User;
use App\Models\UserFood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A user in Florida (UTC-5) at 20:00 on 20/11 is already on 21/11 in UTC.
 * Their meals and their "today" must still be 20/11.
 */
class UserTimezoneTest extends TestCase
{
    use RefreshDatabase;

    private const FLORIDA = 'America/New_York';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 20/11/2026 20:00 in Florida.
        $this->travelTo(Carbon::parse('2026-11-21 01:00:00', 'UTC'));

        $this->user = User::factory()->create([
            'daily_points' => 30,
            'weekly_points' => 35,
        ]);
    }

    private function fromFlorida(): static
    {
        return $this->actingAs($this->user)->withUnencryptedCookie('tz', self::FLORIDA);
    }

    public function test_dashboard_defaults_to_the_users_local_day(): void
    {
        $this->fromFlorida()
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->where('day', '2026-11-20'));
    }

    public function test_dashboard_defaults_to_madrid_without_timezone_cookie(): void
    {
        // 23:30 UTC on 20/11 is already 00:30 on 21/11 in Madrid.
        $this->travelTo(Carbon::parse('2026-11-20 23:30:00', 'UTC'));

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->where('day', '2026-11-21'));
    }

    public function test_invalid_timezone_cookie_falls_back_to_madrid(): void
    {
        $this->travelTo(Carbon::parse('2026-11-20 23:30:00', 'UTC'));

        $this->actingAs($this->user)
            ->withUnencryptedCookie('tz', 'Not/AZone')
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->where('day', '2026-11-21'));
    }

    public function test_meal_logged_in_the_evening_stays_on_the_local_day(): void
    {
        $this->fromFlorida()->post(route('points.store'), [
            'points' => 4,
            'quantity' => 100,
            'name' => 'Cena',
            'color' => 'blue',
            'time_of_day' => 'Cena',
            'consumed_at' => '2026-11-20',
        ])->assertRedirect();

        $meal = Meal::where('user_id', $this->user->id)->sole();
        $this->assertSame('2026-11-20', $meal->consumed_at->toDateString());

        $this->fromFlorida()
            ->get(route('dashboard', ['dayActive' => '2026-11-20']))
            ->assertInertia(fn (Assert $page) => $page
                ->where('day', '2026-11-20')
                ->has('meals.Cena', 1)
                ->where('remainingPoints', 26));
    }

    public function test_iso_instant_from_an_old_client_is_read_in_the_users_timezone(): void
    {
        $this->fromFlorida()->post(route('points.store'), [
            'points' => 4,
            'quantity' => 100,
            'name' => 'Cena',
            'color' => 'blue',
            'time_of_day' => 'Cena',
            'consumed_at' => '2026-11-21T01:00:00.000Z',
        ])->assertRedirect();

        $meal = Meal::where('user_id', $this->user->id)->sole();
        $this->assertSame('2026-11-20', $meal->consumed_at->toDateString());
    }

    public function test_dashboard_accepts_iso_instant_from_an_old_client(): void
    {
        $this->fromFlorida()
            ->get(route('dashboard', ['dayActive' => '2026-11-21T01:00:00.000Z']))
            ->assertInertia(fn (Assert $page) => $page->where('day', '2026-11-20'));
    }

    public function test_no_count_day_uses_the_local_day(): void
    {
        $this->fromFlorida()
            ->post(route('points.no-count'), ['date' => '2026-11-20'])
            ->assertRedirect();

        $this->fromFlorida()
            ->get(route('dashboard', ['dayActive' => '2026-11-20']))
            ->assertInertia(fn (Assert $page) => $page->where('noCountDay', true));
    }

    public function test_own_food_added_as_meal_uses_the_local_day_and_hour(): void
    {
        $food = UserFood::create([
            'user_id' => $this->user->id,
            'name' => 'Mi alimento',
            'color' => 'blue',
            'points' => 3,
            'quantity' => 100,
        ]);

        $this->fromFlorida()
            ->post(route('my-foods.add-meal', $food), ['dayActive' => '2026-11-20'])
            ->assertRedirect(route('dashboard', ['dayActive' => '2026-11-20']));

        $meal = Meal::where('user_id', $this->user->id)->sole();
        $this->assertSame('2026-11-20', $meal->consumed_at->toDateString());
        // 20:00 in Florida is dinner time, not 01:00 breakfast.
        $this->assertSame('Cena', $meal->time_of_day);
    }
}
