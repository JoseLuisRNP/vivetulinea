<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Inertia\Inertia;

class CalculatorController extends Controller
{
    public function show()
    {

        $prevRoute = url()->previous();

        $backTo = Str::contains($prevRoute, 'dashboard') ? 'dashboard' : 'menu';

        $dayActive = \request('dayActive');

        return Inertia::render('Calculator' , [
            'backTo' => $backTo,
            'dayActive' => $dayActive,
        ]);
    }
}
