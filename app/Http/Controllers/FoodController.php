<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Inertia\Inertia;

class FoodController extends Controller
{
    public function index()
    {
        $search = \request('q');
        $color = \request('color');
        $user = auth()->user();

        $foods = Food::query()
            ->when($search, fn ($q) => $q->whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["%$search%"]))
            ->when($color, fn ($q) => $q->where('color', $color))
            ->withExists(['favoritedByUsers as is_favorite' => function ($query) use ($user) {
                $query->where('users.id', $user->id);
            }])
            ->orderByDesc('is_favorite')
            ->orderBy('name')
            ->paginate(100)
            ->withQueryString();

        return Inertia::render('FoodList', [
            'foods' => $foods,
        ]);
    }
}
