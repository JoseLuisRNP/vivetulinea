<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWeightRequest;
use App\Models\Weight;
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

    public function store(StoreWeightRequest $request)
    {
        $validated = $request->validated();

        Weight::create([
            'user_id' => auth()->id(),
            'value' => $validated['weight'],
            'date' => $validated['date'],
        ]);

        return redirect()->route('weights.show');
    }

    public function destroy(Weight $weight)
    {
        $weight->delete();

        return redirect()->back();
    }

    public function update(StoreWeightRequest $request, Weight $weight)
    {
        $validated = $request->validated();

        $weight->update([
            'value' => $validated['weight'],
            'date' => $validated['date'],
        ]);

        return redirect()->route('weights.show');
    }
}
