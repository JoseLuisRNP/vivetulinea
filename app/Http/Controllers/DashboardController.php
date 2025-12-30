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
        $search = \request('q');
        $day = \request('dayActive') ? Carbon::parse(\request('dayActive')) : Carbon::now();
        $meals = auth()->user()->meals()->with('recipe')->whereDate('consumed_at', $day->toDateString())->get();

        $from =$day->clone()
            ->startOfWeek(auth()->user()->created_at->addDay()->dayOfWeek)->startOfDay();

        $noCountDayWeek = auth()->user()->noCountDays()->whereBetween('date', [$from->toDateString(), $from->clone()->addDays(6)->endOfDay()])->get();

        $noCountDay = $noCountDayWeek->contains(fn($d) => $d->date->isSameDay($day));

        $mealsRecipe = $meals->filter(fn ($meal) => $meal->recipe_id);
        $mealsFood = $meals->filter(fn ($meal) => !$meal->recipe_id);

        $pointsByColor = $mealsFood->groupBy('color')->map(function ($item, $key) {
            return $item->sum('points');
        });

        $mealsRecipe->each(function (Meal $meal) use ($pointsByColor){
            $recipe = $meal->recipe;
            $quantityMultiplier = $meal->quantity / $recipe->quantity;
            $pointsByColor->put('blue', $pointsByColor->get('blue', 0) + $recipe->proteins * $quantityMultiplier);
            $pointsByColor->put('red', $pointsByColor->get('red', 0) + $recipe->fats * $quantityMultiplier);
            $pointsByColor->put('green', $pointsByColor->get('green', 0) + $recipe->sugars * $quantityMultiplier);
            $pointsByColor->put('yellow', $pointsByColor->get('yellow', 0) + $recipe->empty_points * $quantityMultiplier);
        });

        $remainingPoints = auth()->user()->daily_points - $meals->filter(fn ($meal) => !$noCountDayWeek->contains(fn($d) => $d->date->isSameDay($meal->consumed_at)) )->sum('points');



        $weeklyMeals = auth()->user()->meals()->whereBetween('consumed_at', [$from, $from->clone()->addDays(6)->endOfDay()])->get();

        $groupByDay = $weeklyMeals->groupBy(fn($meal) => $meal->consumed_at->format('Y-m-d'));

        $weeklyPoints = $groupByDay->map(function($d,$key) use ($noCountDayWeek) {
            $day = Carbon::parse($key);
            if($noCountDayWeek->contains(fn ($d) => $d->date->isSameDay($day))) {
                return $d->sum('points');
            }

            $total = $d->sum('points');
            if($total > auth()->user()->daily_points) {
                return $total - auth()->user()->daily_points;
            }

            return 0;
        })->sum();

        $weekRemainingPoints = auth()->user()->weekly_points - $weeklyPoints;

        $resultSearch = collect();
        if($search) {
            $user = auth()->user();
            $resultSearch = Food::whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["%$search%"])
                ->withExists(['favoritedByUsers as is_favorite' => function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                }])
                ->orderByDesc('is_favorite')
                ->orderByRaw("CASE WHEN LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?) THEN 1 ELSE 0 END DESC", ["$search%"])
                ->get();
        }

        $guideLine = auth()->user()->guidelines()->where('consumed_at', $day->toDateString())->first();
        if(!$guideLine) {
            $guideLine = auth()->user()->guidelines()->create([
                'consumed_at' => $day->toDateString(),
                'water' => 0,
                'fruit' => 0,
                'vegetable' => 0,
                'sport' => 0,
            ]);
        }

        return Inertia::render('Dashboard', [
            'meals' => $meals->groupBy('time_of_day'),
            'remainingPoints' => $this->format_number($remainingPoints, false),
            'weekPointsConsumedThisDay' => $remainingPoints < 0 ? $remainingPoints * -1 : null,
            'weekRemainingPoints' => $this->format_number($weekRemainingPoints, true),
            'pointsByColor' => $pointsByColor,
            'resultSearch' => $resultSearch,
            'noCountDay' => !!$noCountDay,
            'guideline' => $guideLine,
        ]);
    }

    private function format_number($num, $allowNegative = false) {
        if($num < 0 && !$allowNegative) {
            return 0;
        }
        if ($num == intval($num)) {
            return intval($num);
        } else {
            return number_format($num, 1);
        }
    }
}
