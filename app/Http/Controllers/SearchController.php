<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = $request->input('search');
        $user = auth()->user();

        $foods = Food::whereRaw('LOWER(name) COLLATE utf8_general_ci LIKE LOWER(?)', ["%$search%"])
            ->withExists(['favoritedByUsers as is_favorite' => function ($query) use ($user) {
                $query->where('users.id', $user->id);
            }])
            ->orderByDesc('is_favorite')
            ->orderByRaw("CASE WHEN LOWER(name) COLLATE utf8_general_ci LIKE LOWER(?) THEN 1 ELSE 0 END DESC", ["$search%"])
            ->get();

        return response()->json($foods);
    }
}
