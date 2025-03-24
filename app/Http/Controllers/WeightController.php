<?php

namespace App\Http\Controllers;

use App\Models\Weight;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WeightController extends Controller
{
    public function show()
    {
        return Inertia::render('Weight');
    }

    public function store(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric',
            'date' => 'required|date',
        ]);

        Weight::create([
            'user_id' => auth()->id(),
            'value' => $request->weight,
            'date' => $request->date,
        ]);

        return redirect()->route('dashboard');
    }
}
