<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Recipe;
use Inertia\Inertia;

class RecipeController extends Controller
{
    public function index()
    {
        $search = \request('q');

        $foods = auth()->user()->recipes()
            ->with(['foods.food:name,id,color,points,quantity', 'foods.userFood'])
            ->when($search, fn($q) => $q->where('name', 'like', "$search%")
                ->orWhere('name', 'like', "%$search%"))
            ->when(!$search, fn($q) => $q->orderBy('name'))
            ->paginate(10);

        return Inertia::render('Recipes', ['foods' => $foods]);
    }

    public function new()
    {
        $recipeId = request('id');
        if ($recipeId) {
            $recipe = auth()->user()->recipes()->with(['foods.food', 'foods.userFood'])->find($recipeId);
        }
        $resultSearch = collect();
        $search = \request('q');
        if ($search) {
            $foods = Food::where('name', 'like', "$search%")
                ->orWhere('name', 'like', "%$search%")
                ->get()
                ->map(function ($food) {
                    $food->isUserFood = false;
                    return $food;
                });

            $userFoods = auth()->user()->userFoods()
                ->where('name', 'like', "$search%")
                ->orWhere('name', 'like', "%$search%")
                ->get()
                ->map(function ($food) {
                    $food->isUserFood = true;
                    return $food;
                });

            $resultSearch = $foods->merge($userFoods);
        }

        return Inertia::render('CreateRecipe', [
            'resultSearch' => $resultSearch,
            'recipe' => $recipe ?? null
        ]);
    }

    public function create()
    {

        $validated = $this->validate(\request(), [
            'id' => 'nullable|exists:recipes,id',
            'name' => 'required|string',
            'ration' => 'required|numeric',
            'proteins' => 'required|numeric',
            'sugars' => 'required|numeric',
            'fats' => 'required|numeric',
            'empty_points' => 'required|numeric',
            'points' => 'required|numeric',
            'foods' => 'required|array',
            'foods.*.food_id' => 'nullable|exists:food,id',
            'foods.*.user_food_id' => 'nullable|exists:user_foods,id',
            'foods.*.quantity' => 'required|numeric|min:0.01',
            'foods.*.unit' => 'required|string',
        ]);


        $recipe = auth()->user()->recipes()->updateOrCreate(
            ['id' => $validated['id'] ?? null],
            [
                'name' => $validated['name'],
                'quantity' => $validated['ration'],
                'proteins' => $validated['proteins'],
                'sugars' => $validated['sugars'],
                'fats' => $validated['fats'],
                'empty_points' => $validated['empty_points'],
                'points' => $validated['points'],
                'unit' => 'ración' // solo raciones o incluir gramos también?
            ]
        );

        $recipe->foods()->delete();

        $recipe->foods()->createMany($validated['foods']);

        return redirect()->route('recipes.index')->with('success', $validated['id'] ?? null ? 'Receta actualizada correctamente' : 'Receta creada correctamente');
    }

    public function delete(Recipe $recipe)
    {
        $recipe->meals()->delete();
        $recipe->delete();
        return redirect()->route('recipes.index')->with('success', 'Receta eliminada correctamente');
    }
}
