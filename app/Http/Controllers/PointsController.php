<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Guideline;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PointsController extends Controller
{
    public function show($food = null)
    {
        if ($food) {
            $food = Food::find($food);
        }

        return Inertia::render('Points', [
            'food' => $food,
        ]);
    }

    public function store()
    {
        $this->validate(\request(), [
            'points' => 'required|numeric',
            'quantity' => 'required|numeric',
            'name' => 'string',
            'color' =>  Rule::in(['yellow', 'blue', 'green', 'red']),
            'time_of_day' => Rule::in(['Desayuno', 'Media mañana', 'Almuerzo', 'Merienda', 'Cena']),
            'consumed_at' => 'required|date',
        ]);

        auth()->user()->meals()->create([
            'points' => \request('points'),
            'quantity' => \request('quantity'),
            'name' => \request('name'),
            'color' => \request('color'),
            'time_of_day' => \request('time_of_day'),
            'consumed_at' => Carbon::parse(\request('consumed_at')),
            'special_no_count' => \request('special_no_count') ?? false,
            'oil_no_count' => \request('oil_no_count') ?? false,
        ]);

        return redirect()->back()->with('success', 'Puntos registrados correctamente');
    }

    public function destroy($meal)
    {
        auth()->user()->meals()->find($meal)?->delete();

        return redirect()->back()->with('success', 'Alimento correctamente');
    }

    public function noCountDay()
    {
        $this->validate(\request(), [
            'date' => 'required|date',
        ]);

        auth()->user()->noCountDays()->create([
            'date' => Carbon::parse(\request('date')),
        ]);

        return redirect()->back()->with('message', 'Día de no contar puntos iniciado correctamente');
    }

    public function storeGuideline(Guideline $guideline) {
        $this->validate(\request(), [
            'water' => 'required|numeric',
            'fruit' => 'required|numeric',
            'vegetable' => 'required|numeric',
            'sport' => 'required|numeric',
        ]);


        $guideline->update([
            'water' => \request('water'),
            'fruit' => \request('fruit'),
            'vegetable' => \request('vegetable'),
            'sport' => \request('sport'),
        ]);

        return redirect()->back();
    }

    public function noCountFood()
    {
        $search = \request('q');

        return Inertia::render('NoCountFoodList', [
            'foods' => Food::query()
                ->when($search, fn($q) => $q->whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["%$search%"]))
                ->where('no_count', true)
                ->paginate(10)
                ->withQueryString(),
        ]);
    }
}
