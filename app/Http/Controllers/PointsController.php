<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PointsController extends Controller
{
    public function show()
    {
        return Inertia::render('Points');
    }

    public function store()
    {
        $this->validate(\request(), [
            'points' => 'required|numeric',
            'quantity' => 'required|numeric',
            'name' => 'string',
            'color' =>  Rule::in(['yellow', 'blue', 'green', 'red']),
            'time_of_day' => Rule::in(['Desayuno', 'Media mañana', 'Almuerzo', 'Merienda', 'Cena', 'Snack']),
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
}
