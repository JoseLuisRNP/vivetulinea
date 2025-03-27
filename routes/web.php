<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuAppController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ShowWeightsController;
use App\Http\Controllers\WeightController;
use App\Http\Middleware\HandleActiveUsersMiddleware;
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

Route::get('/admin/login', fn () =>redirect()->route('login'))->name('filament.admin.auth.login');

Route::get('/app', function () {
    if(!auth()->check()) {
        return redirect()->route('register');
    }

    return redirect()->route('menu');
});

Route::view('/aviso-legal', 'aviso-legal')->name('about');
Route::view('/proteccion-de-datos', 'proteccion-datos')->name('proteccion-datos');
Route::view('/cookies', 'cookies')->name('cookies');
Route::view('/', 'index')->name('index');
Route::view('/mapa', 'mapa')->name('mapa');
Route::view('/politica-de-privacidad', 'politica-privacidad')->name('politica-privacidad');
Route::get('food/no-count', [PointsController::class, 'noCountFood'])->name('food.no-count');


//Route::get('fake-login', function() {
//    $user = \App\Models\User::find(request('id'));
//    auth()->login($user);
//    return redirect()->route('dashboard');
//});

Route::get('campaign', [RegisteredUserController::class, 'create'])
                ->name('campaign');

Route::prefix('app')->middleware(['auth', HandleActiveUsersMiddleware::class])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('calculator', [CalculatorController::class, 'show'])->name('calculator');
    Route::get('weight-info', [ShowWeightsController::class, 'show'])->name('weights.show');
    Route::get('weight', [WeightController::class, 'show'])->name('weight.show');
    Route::post('weight', [WeightController::class, 'store'])->name('weight.store');
    Route::delete('weight/{weight}', [WeightController::class, 'destroy'])->name('weight.destroy');
    Route::patch('weight/{weight}', [WeightController::class, 'update'])->name('weight.update');
    Route::get('recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::delete('recipes/{recipe}', [RecipeController::class, 'delete'])->name('recipes.destroy');
    Route::get('recipes/create', [RecipeController::class, 'new'])->name('recipes.new');
    Route::post('recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::get('menu', MenuAppController::class)->name('menu');
    Route::get('points/{food?}', [PointsController::class, 'show'])->name('points.show');
    Route::post('points/store', [PointsController::class, 'store'])->name('points.store');
    Route::delete('points/{meal}', [PointsController::class, 'destroy'])->name('points.destroy');
    Route::post('guideline/update/{guideline}', [PointsController::class, 'storeGuideline'])->name('guidelines.store');
    Route::post('no-count', [PointsController::class, 'noCountDay'])->name('points.no-count');
    Route::post('cancel-no-count', [PointsController::class, 'cancelNoCountDay'])->name('points.cancel-no-count');
    Route::get('no-active', function() {
        $isCampaignUser = auth()->user()->campaign_id !== null && auth()->user()->dietician_id === 1;
        return Inertia::render('NoActiveAccount', ['isCampaignUser' => $isCampaignUser]);
    })->name('no-active');
});

require __DIR__.'/auth.php';
