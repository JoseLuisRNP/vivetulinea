<?php

namespace App\Http\Controllers;

use App\Models\Weight;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WeightController extends Controller
{
    public function show()
    {
        $weightId = request('weight');
        $weight = null;
        if ($weightId) {
            $weight = Weight::where('user_id', auth()->id())->where('id', $weightId)->first();
        }

        return Inertia::render('Weight', [
            'weight' => $weight ? [
                'id' => $weight->id,
                'value' => $weight->value,
                'date' => $weight->date,
            ] : null,
        ]);
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

        return redirect()->route('weights.show');
    }

    public function destroy(Weight $weight)
    {
        $weight->delete();

        return redirect()->back();
    }

    public function update(Request $request, Weight $weight)
    {
        $request->validate([
            'weight' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $weight->update([
            'value' => $request->weight,
            'date' => $request->date,
        ]);

        return redirect()->route('weights.show');
    }
}
