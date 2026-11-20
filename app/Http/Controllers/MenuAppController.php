<?php

namespace App\Http\Controllers;

use App\Support\UserTime;
use Inertia\Inertia;

class MenuAppController extends Controller
{
    public function __invoke()
    {
        $today = UserTime::today();
        $isSameDayOfRegister = $today->dayOfWeek === auth()->user()->created_at->addDay()->dayOfWeek;
        $lastWeight = auth()->user()->weights()->orderBy('date', 'desc')->first('date');

        $shouldShowToast = false;

        if(auth()->user()->role === 'admin') { 
            $shouldShowToast = $isSameDayOfRegister && (!$lastWeight || $lastWeight->date->toDateString() !== $today->toDateString());
        }
    
        return Inertia::render('ViewSelector', [
            'shouldShowToast' => $shouldShowToast
        ]);
    }
}
