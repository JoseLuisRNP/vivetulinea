<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $search = \request('q');
        $day = \request('dayActive') ? Carbon::parse(\request('dayActive')) : Carbon::now();
        $meals = auth()->user()->meals()->whereDate('consumed_at', $day->toDateString())->get();

        $from =$day->clone()
            ->startOfWeek(auth()->user()->created_at->dayOfWeek);

        $noCountDayWeek = auth()->user()->noCountDays()->whereBetween('date', [$from->toDateString(), $from->clone()->addDays(6)])->get();

        $noCountDay = $noCountDayWeek->contains(fn($d) => $d->date->isSameDay($day));

        $pointsByColor = $meals->groupBy('color')->map(function ($item, $key) {
            return $item->sum('points');
        });


        $remainingPoints = auth()->user()->daily_points - $meals->filter(fn ($meal) => !$noCountDayWeek->contains(fn($d) => $d->date->isSameDay($meal->consumed_at)) )->sum('points');


        $weeklyMeals = auth()->user()->meals()->whereBetween('consumed_at', [$from->toDateString(), $from->clone()->addDays(6)->toDateString()])->get();

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
            $resultSearch = Food::whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["$search%"])->take(5)->get();
        }

        return Inertia::render('Dashboard', [
            'meals' => $meals->groupBy('time_of_day'),
            'remainingPoints' => $remainingPoints,
            'weekRemainingPoints' => $weekRemainingPoints,
            'pointsByColor' => $pointsByColor,
            'resultSearch' => $resultSearch,
            'noCountDay' => !!$noCountDay,
        ]);
    }
}
