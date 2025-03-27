<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ShowWeightsController extends Controller
{
    public function show(Request $request) 
    {
        $range = $request->input('range', '3months');
        $query = auth()->user()->weights();

        // Apply date range filter
        switch ($range) {
            case '1month':
                $query->where('date', '>=', Carbon::now()->subMonth());
                break;
            case '3months':
                $query->where('date', '>=', Carbon::now()->subMonths(3));
                break;
            case '6months':
                $query->where('date', '>=', Carbon::now()->subMonths(6));
                break;
            case '1year':
                $query->where('date', '>=', Carbon::now()->subYear());
                break;
        }

        $weights = $query->orderBy('date')->get();
        $weights = $weights->map(function ($weight, $index) {
            return [
                'id' => $weight->id,
                'index' => $index + 1,
                'value' => (float)$weight->value,
                'date' => $weight->date->format('d/m/Y'),
            ];
        });

        return Inertia::render('WeightInfo', [
            'weights' => $weights,
            'targetWeight' => auth()->user()->target_weight,
            'selectedRange' => $range,
        ]);
    }
}
