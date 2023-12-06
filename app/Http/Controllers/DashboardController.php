<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $day = \request('dayActive') ? Carbon::parse(\request('dayActive')) : Carbon::now();
        $meals = auth()->user()->meals()->whereDate('consumed_at', $day->toDateString())->get();

        $pointsByColor = $meals->groupBy('color')->map(function ($item, $key) {
            return $item->sum('points');
        });

        $remainingPoints = auth()->user()->daily_points - $meals->sum('points');

        $weeklyPoints =  auth()->user()->meals()->selectRaw('DATE(consumed_at) as date, SUM(points) as totalPoints')->groupBy('consumed_at')->havingBetween('consumed_at', [$day->startOfWeek()->toDateString(), $day->endOfWeek()->toDateString()])->having('totalPoints', '<', auth()->user()->weekly_points)->get();

        $weekRemainingPoints = auth()->user()->weekly_points - $weeklyPoints->sum('totalPoints');

        return Inertia::render('Dashboard', [
            'meals' => $meals->groupBy('time_of_day'),
            'remainingPoints' => $remainingPoints,
            'weekRemainingPoints' => $weekRemainingPoints,
            'pointsByColor' => $pointsByColor,
        ]);
    }
}
