<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuAppController extends Controller
{
    public function __invoke()
    {
        $isSameDayOfRegister = now()->dayOfWeek === auth()->user()->created_at->addDay()->dayOfWeek;
        $lastWeight = auth()->user()->weights()->orderBy('date', 'desc')->first('date');

        $shouldShowToast = $isSameDayOfRegister && !$lastWeight->date->isSameDay(now());

    
        return Inertia::render('ViewSelector', [
            'shouldShowToast' => $shouldShowToast
        ]);
    }
}
