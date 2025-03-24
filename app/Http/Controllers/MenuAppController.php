<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuAppController extends Controller
{
    public function __invoke()
    {
        $weights = auth()->user()->weights()->orderBy('date')->get();

        $weights = $weights->map(function ($weight, $index) {
            return [
                'index' => $index + 1,
                'value' => (float)$weight->value,
                'date' => $weight->date->format('d/m/Y'),
            ];
        });



        return Inertia::render('ViewSelector', [
            'weights' => collect() //$weights,
        ]);
    }
}
