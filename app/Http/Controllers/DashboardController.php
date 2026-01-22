<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Meal;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $search = request('q');
        $day = request('dayActive') ? Carbon::parse(request('dayActive')) : Carbon::now();
        $user = auth()->user();

        // Calculate the week range
        $from = $day->clone()->startOfWeek($user->created_at->addDay()->dayOfWeek)->startOfDay();
        $to = $from->clone()->addDays(6)->endOfDay();

        // Fetch everything in unified queries
        $weeklyMeals = $user->meals()
            ->with('recipe')
            ->whereBetween('consumed_at', [$from, $to])
            ->get();

        $noCountDays = $user->noCountDays()
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->keyBy(fn($d) => Carbon::parse($d->date)->format('Y-m-d'));

        // Current day data
        $dayString = $day->toDateString();
        $meals = $weeklyMeals->filter(fn($meal) => $meal->consumed_at->toDateString() === $dayString);
        $noCountDay = $noCountDays->has($dayString);

        // Calculate daily points by color
        $pointsByColor = collect(['blue' => 0, 'green' => 0, 'red' => 0, 'yellow' => 0]);

        foreach ($meals as $meal) {
            if ($meal->recipe_id) {
                if ($recipe = $meal->recipe) {
                    $multiplier = $meal->quantity / $recipe->quantity;
                    $pointsByColor['blue'] += $recipe->proteins * $multiplier;
                    $pointsByColor['green'] += $recipe->sugars * $multiplier;
                    $pointsByColor['red'] += $recipe->fats * $multiplier;
                    $pointsByColor['yellow'] += $recipe->empty_points * $multiplier;
                }
            } else {
                $pointsByColor[$meal->color] = ($pointsByColor[$meal->color] ?? 0) + $meal->points;
            }
        }

        // Calculate remaining points
        $dailyPointsLimit = $user->daily_points;
        $mealsOnNoCountDays = $noCountDays; // We'll use the keyed collection for lookup

        $totalPointsToday = $meals->sum('points');
        $remainingPoints = $dailyPointsLimit - ($noCountDay ? 0 : $totalPointsToday);

        // Weekly points calculation (points exceeding daily limit on non-no-count days)
        $weeklyPointsExceeded = $weeklyMeals->groupBy(fn($m) => $m->consumed_at->toDateString())
            ->map(function ($dayMeals, $date) use ($noCountDays, $dailyPointsLimit) {
                if ($noCountDays->has($date)) {
                    return $dayMeals->sum('points');
                }
                $dayTotal = $dayMeals->sum('points');
                return max(0, $dayTotal - $dailyPointsLimit);
            })->sum();

        $weekRemainingPoints = $user->weekly_points - $weeklyPointsExceeded;

        // Search logic
        $resultSearch = collect();
        if ($search) {
            $resultSearch = Food::where('name', 'like', "%$search%")
                ->withExists([
                    'favoritedByUsers as is_favorite' => function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    }
                ])
                ->orderByDesc('is_favorite')
                ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 0 END DESC", ["$search%"])
                ->get();
        }

        // Guideline logic
        $guideLine = $user->guidelines()->firstOrCreate(
            ['consumed_at' => $dayString],
            ['water' => 0, 'fruit' => 0, 'vegetable' => 0, 'sport' => 0]
        );

        return Inertia::render('Dashboard', [
            'meals' => $meals->groupBy('time_of_day'),
            'remainingPoints' => $this->format_number($remainingPoints, false),
            'weekPointsConsumedThisDay' => $remainingPoints < 0 ? abs($remainingPoints) : null,
            'weekRemainingPoints' => $this->format_number($weekRemainingPoints, true),
            'pointsByColor' => $pointsByColor,
            'resultSearch' => $resultSearch,
            'noCountDay' => $noCountDay,
            'guideline' => $guideLine,
        ]);
    }

    private function format_number($num, $allowNegative = false)
    {
        if ($num < 0 && !$allowNegative) {
            return 0;
        }
        if ($num == intval($num)) {
            return intval($num);
        } else {
            return number_format($num, 1);
        }
    }
}
