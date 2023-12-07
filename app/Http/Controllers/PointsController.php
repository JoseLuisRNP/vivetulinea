<?php

namespace App\Http\Controllers;

use App\Models\Food;
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
        ]);

        return redirect()->back()->with('success', 'Puntos registrados correctamente');
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
}
