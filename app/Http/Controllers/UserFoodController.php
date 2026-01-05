<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserFoodRequest;
use App\Models\UserFood;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class UserFoodController extends Controller
{
    public function index()
    {
        $search = \request('q');
        $user = auth()->user();

        $userFoods = $user->userFoods()
            ->when($search, fn ($q) => $q->whereRaw('LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)', ["%$search%"]))
            ->orderBy('name')
            ->paginate(100)
            ->withQueryString();

        return Inertia::render('MyFoods', [
            'userFoods' => $userFoods,
            'time' => \request('time'),
            'dayActive' => \request('dayActive'),
            'noCountDay' => \request('noCountDay'),
        ]);
    }

    public function store(StoreUserFoodRequest $request)
    {
        auth()->user()->userFoods()->create($request->validated());

        return redirect()->back()->with('success', 'Alimento creado correctamente');
    }

    public function update(StoreUserFoodRequest $request, UserFood $userFood)
    {
        $this->authorize('update', $userFood);

        $userFood->update($request->validated());

        return redirect()->back()->with('success', 'Alimento actualizado correctamente');
    }

    public function destroy(UserFood $userFood)
    {
        $this->authorize('delete', $userFood);

        $userFood->delete();

        return redirect()->back()->with('success', 'Alimento eliminado correctamente');
    }

    public function addAsMeal(UserFood $userFood)
    {
        $this->authorize('view', $userFood);

        $noCountDay = \request('noCountDay') === 'true' || \request('noCountDay') === true;

        if ($noCountDay) {
            return redirect()->back()->with('error', 'No se pueden añadir alimentos propios el día de no contar');
        }

        $timeOfDay = \request('time') ?: $this->getCurrentTimeOfDay();
        $dayActive = \request('dayActive') ? Carbon::parse(\request('dayActive')) : now();

        auth()->user()->meals()->create([
            'points' => $userFood->points,
            'quantity' => $userFood->quantity,
            'name' => $userFood->name,
            'color' => $userFood->color,
            'time_of_day' => $timeOfDay,
            'consumed_at' => $dayActive,
        ]);

        return redirect()->route('dashboard', [
            'dayActive' => $dayActive->toISOString(),
        ])->with('success', 'Alimento añadido correctamente');
    }

    private function getCurrentTimeOfDay(): string
    {
        $hour = (int) now()->format('H');

        if ($hour < 10) {
            return 'Desayuno';
        }
        if ($hour < 12) {
            return 'Media mañana';
        }
        if ($hour < 16) {
            return 'Almuerzo';
        }
        if ($hour < 19) {
            return 'Merienda';
        }

        return 'Cena';
    }
}
