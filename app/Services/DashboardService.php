<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get dashboard data for a user on a specific day.
     */
    public function getDashboardData(User $user, Carbon $day): array
    {
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

        $pointsByColor = $this->calculatePointsByColor($meals);

        // Calculate remaining points
        $dailyPointsLimit = $user->daily_points;
        $totalPointsToday = $meals->sum('points');
        $remainingPoints = $dailyPointsLimit - ($noCountDay ? 0 : $totalPointsToday);

        // Weekly points calculation
        $weeklyPointsExceeded = $this->calculateWeeklyPointsExceeded($weeklyMeals, $noCountDays, $dailyPointsLimit);

        $weekRemainingPoints = $user->weekly_points - $weeklyPointsExceeded;

        // Guideline logic (back to firstOrCreate as requested)
        $guideLine = $user->guidelines()->firstOrCreate(
            ['consumed_at' => $dayString],
            ['water' => 0, 'fruit' => 0, 'vegetable' => 0, 'sport' => 0]
        );

        return [
            'meals' => $meals->groupBy('time_of_day'),
            'remainingPoints' => $this->format_number($remainingPoints, false),
            'weekPointsConsumedThisDay' => $remainingPoints < 0 ? abs($remainingPoints) : null,
            'weekRemainingPoints' => $this->format_number($weekRemainingPoints, true),
            'pointsByColor' => $pointsByColor,
            'noCountDay' => $noCountDay,
            'guideline' => $guideLine,
        ];
    }

    /**
     * Calculate points grouped by color.
     */
    private function calculatePointsByColor(Collection $meals): Collection
    {
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

        return $pointsByColor;
    }

    /**
     * Calculate exceeded weekly points.
     */
    private function calculateWeeklyPointsExceeded(Collection $weeklyMeals, Collection $noCountDays, $dailyPointsLimit): float
    {
        return $weeklyMeals->groupBy(fn($m) => $m->consumed_at->toDateString())
            ->map(function ($dayMeals, $date) use ($noCountDays, $dailyPointsLimit) {
                if ($noCountDays->has($date)) {
                    return $dayMeals->sum('points');
                }
                $dayTotal = $dayMeals->sum('points');
                return max(0, $dayTotal - $dailyPointsLimit);
            })->sum();
    }

    /**
     * Format numbers for display.
     */
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
