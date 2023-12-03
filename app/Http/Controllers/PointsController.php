<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PointsController extends Controller
{
    public function store()
    {


        $this->validate(\request(), [
            'points' => 'required|numeric',
            'quantity' => 'required|numeric',
            'consumed_at' => 'required|date',
        ]);

        auth()->user()->meals()->create([
            'points' => \request('points'),
            'quantity' => \request('quantity'),
            'consumed_at' => Carbon::parse(\request('consumed_at')),
        ]);


        return redirect()->route('calculator')->with('success', 'Puntos registrados correctamente');
    }
}
