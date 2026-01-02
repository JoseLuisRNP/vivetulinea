<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = $request->input('search');

        $foods = Food::whereRaw('LOWER(name) COLLATE utf8_general_ci LIKE LOWER(?)', ["%$search%"])
        ->get();

        return response()->json($foods);
    }
}
