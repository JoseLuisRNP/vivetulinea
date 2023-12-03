<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuAppController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('ViewSelector');
    }
}
