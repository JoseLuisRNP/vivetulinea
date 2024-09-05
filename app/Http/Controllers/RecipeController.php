<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Inertia\Inertia;

class RecipeController extends Controller
{
    public function index()
    {
        $search = \request('q');

        $foods = auth()->user()->recipes()
            ->with(['foods.food:name,id,color,points,quantity'])
            ->when($search, fn($q) => $q->whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["%$search%"])
                ->orderByRaw("CASE WHEN LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?) THEN 1 ELSE 0 END DESC", ["$search%"]))
            ->when(!$search, fn($q) => $q->orderBy('name'))
            ->paginate(10);

        return Inertia::render('Recipes', ['foods' => $foods]);
    }

    public function new()
    {
        $resultSearch = collect();
        $search = \request('q');
        if($search) {
            $resultSearch = Food::whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["%$search%"])
                ->orderByRaw("CASE WHEN LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?) THEN 1 ELSE 0 END DESC", ["$search%"])
                ->get();
        }
        return Inertia::render('CreateRecipe', ['resultSearch' => $resultSearch]);
    }

    public function create()
    {

        $validated = $this->validate(\request(), [
            'name' => 'required|string',
            'ration' => 'required|numeric',
            'proteins' => 'required|numeric',
            'sugars' => 'required|numeric',
            'fats' => 'required|numeric',
            'empty_points' => 'required|numeric',
            'points' => 'required|numeric',
            'foods' => 'required|array',
        ]);

        $recipe = auth()->user()->recipes()->create([
            'name' => $validated['name'],
            'quantity' => $validated['ration'],
            'proteins' => $validated['proteins'],
            'sugars' => $validated['sugars'],
            'fats' => $validated['fats'],
            'empty_points' => $validated['empty_points'],
            'points' => $validated['points'],
            'unit' => 'ración' // solo raciones o incluir gramos también?
        ]);

        $recipe->foods()->createMany($validated['foods']);

        return redirect()->route('recipes.index')->with('success', 'Receta creada correctamente');
    }
}
