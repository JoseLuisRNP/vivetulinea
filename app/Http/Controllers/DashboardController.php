<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Guideline;
use App\Services\DashboardService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboardService)
    {
        $search = request('q');
        $day = request('dayActive') ? Carbon::parse(request('dayActive')) : Carbon::now();
        $user = auth()->user();
        $dayString = $day->toDateString();

        // 1. Service-level data (cached)
        $cacheVersion = Cache::get("user_{$user->id}_dashboard_version", 1);
        $cacheKey = "user_{$user->id}_dashboard_{$dayString}_v{$cacheVersion}";

        $data = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($dashboardService, $user, $day) {
            return $dashboardService->getDashboardData($user, $day);
        });

        // 2. Search logic (not cached, uses indexed 'name')
        $resultSearch = collect();
        if ($search) {
            $resultSearch = Food::where('name', 'like', "$search%") // Prefix search is very fast with index
                ->orWhere('name', 'like', "%$search%") // Fallback to middle search
                ->withExists([
                    'favoritedByUsers as is_favorite' => function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    }
                ])
                ->orderByDesc('is_favorite')
                ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 0 END DESC", ["$search%"])
                ->limit(50)
                ->get();
        }

        return Inertia::render('Dashboard', array_merge($data, [
            'resultSearch' => $resultSearch,
        ]));
    }
}
