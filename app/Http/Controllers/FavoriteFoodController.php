<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FavoriteFoodController extends Controller
{
    public function toggle(Food $food)
    {
        $user = auth()->user();

        if ($user->favoriteFoods()->where('food_id', $food->id)->exists()) {
            $user->favoriteFoods()->detach($food->id);
        } else {
            $user->favoriteFoods()->attach($food->id);
        }

        \Illuminate\Support\Facades\Cache::increment("user_{$user->id}_dashboard_version");

        return redirect()->back();
    }
}
