<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\HandleActiveUsersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(!auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route( auth()->user()->isAdmin() ? 'filament.admin.pages.dashboard' : 'dashboard');
});



Route::middleware(['auth', HandleActiveUsersMiddleware::class])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['verified'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('calculator', [CalculatorController::class, 'show'])->name('calculator');
});

require __DIR__.'/auth.php';
