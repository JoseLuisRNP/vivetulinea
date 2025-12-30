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

        return Inertia::render('FoodList', [
            'foods' => Food::query()
                ->when($search, fn ($q) => $q->whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["%$search%"]))
                ->when($color, fn ($q) => $q->where('color', $color))
                ->orderBy('name')
                ->paginate(100)
                ->withQueryString(),
        ]);
    }
}
