<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Support\Htmlable;

class ViewUserDiary extends Page
{
    protected static string $resource = UserResource::class;

    protected string $view = 'filament.resources.user-resource.pages.view-user-diary';

    public $record;
    public $selectedDate;

    public function mount($record): void
    {
        $this->record = static::getResource()::getModel()::findOrFail($record);
        $this->selectedDate = request('date', now()->toDateString());
    }

    public function getTitle(): string | Htmlable
    {
        return 'Diario de ' . $this->record->name;
    }

    public function getHeading(): string | Htmlable
    {
        return 'Diario de ' . $this->record->name;
    }

    public function updatedSelectedDate(): void
    {
        // Livewire will automatically re-render when selectedDate changes
    }

    public function getMealsProperty()
    {
        $date = Carbon::parse($this->selectedDate);
        
        return $this->record->meals()
            ->with('recipe')
            ->whereDate('consumed_at', $date->toDateString())
            ->get()
            ->groupBy('time_of_day');
    }

    public function getTotalPointsByMealProperty()
    {
        $totals = [];
        $times = ['Desayuno', 'Media mañana', 'Almuerzo', 'Merienda', 'Cena'];
        
        foreach ($times as $time) {
            $meals = $this->meals->get($time, collect());
            $totals[$time] = $meals->sum('points');
        }
        
        return $totals;
    }

    public function getPointsByColorProperty()
    {
        $allMeals = $this->meals->flatten();
        $mealsRecipe = $allMeals->filter(fn ($meal) => $meal->recipe_id);
        $mealsFood = $allMeals->filter(fn ($meal) => !$meal->recipe_id);

        $pointsByColor = $mealsFood->groupBy('color')->map(function ($item, $key) {
            return $item->sum('points');
        });

        $mealsRecipe->each(function ($meal) use ($pointsByColor) {
            $recipe = $meal->recipe;
            if ($recipe) {
                $quantityMultiplier = $meal->quantity / $recipe->quantity;
                $pointsByColor->put('blue', $pointsByColor->get('blue', 0) + $recipe->proteins * $quantityMultiplier);
                $pointsByColor->put('red', $pointsByColor->get('red', 0) + $recipe->fats * $quantityMultiplier);
                $pointsByColor->put('green', $pointsByColor->get('green', 0) + $recipe->sugars * $quantityMultiplier);
                $pointsByColor->put('yellow', $pointsByColor->get('yellow', 0) + $recipe->empty_points * $quantityMultiplier);
            }
        });

        return $pointsByColor;
    }

    public function formatPoints($points): string
    {
        $rounded = max(round($points * 2) / 2, 0);
        if ($rounded == intval($rounded)) {
            return (string) intval($rounded);
        }
        return number_format($rounded, 1);
    }

    public function getTotalDayPointsProperty()
    {
        return collect($this->totalPointsByMeal)->sum();
    }

    public function getDisplayedTotalPointsProperty()
    {
        $total = $this->totalDayPoints;
        $dailyPoints = $this->record->daily_points ?? 0;
        
        // Si se pasa del límite, mostrar el límite como total
        return $total > $dailyPoints ? $dailyPoints : $total;
    }

    public function getExtraPointsProperty()
    {
        $total = $this->totalDayPoints;
        $dailyPoints = $this->record->daily_points ?? 0;
        
        // Calcular puntos extras si se pasa del límite
        return $total > $dailyPoints ? $total - $dailyPoints : 0;
    }

    public function hasExceededDailyPoints(): bool
    {
        return $this->totalDayPoints > ($this->record->daily_points ?? 0);
    }

    public function getWeeklyExtraPointsProperty()
    {
        $selectedDate = Carbon::parse($this->selectedDate);
        
        // Calcular el inicio de la semana basado en created_at
        $weekStartDay = $this->record->created_at->clone()->addDay()->dayOfWeek;
        $from = $selectedDate->clone()
            ->startOfWeek($weekStartDay)
            ->startOfDay();

        // Obtener días sin contar de la semana
        $noCountDayWeek = $this->record->noCountDays()
            ->whereBetween('date', [$from->toDateString(), $from->clone()->addDays(6)->endOfDay()])
            ->get();

        // Obtener todas las comidas de la semana
        $weeklyMeals = $this->record->meals()
            ->whereBetween('consumed_at', [$from, $from->clone()->addDays(6)->endOfDay()])
            ->get();

        // Agrupar por día
        $groupByDay = $weeklyMeals->groupBy(fn($meal) => $meal->consumed_at->format('Y-m-d'));

        // Calcular puntos extras semanales
        $weeklyExtraPoints = $groupByDay->map(function($dayMeals, $key) use ($noCountDayWeek) {
            $day = Carbon::parse($key);
            
            // Si es un día sin contar, todos los puntos son extras
            if($noCountDayWeek->contains(fn ($noCountDay) => $noCountDay->date->isSameDay($day))) {
                return $dayMeals->sum('points');
            }

            // Si no es día sin contar, solo contar el exceso sobre el límite diario
            $total = $dayMeals->sum('points');
            $dailyPoints = $this->record->daily_points ?? 0;
            
            if($total > $dailyPoints) {
                return $total - $dailyPoints;
            }

            return 0;
        })->sum();

        return $weeklyExtraPoints;
    }

    public function previousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->toDateString();
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->toDateString();
    }

    public function userWeeklyPoints(): int
    {
        return $this->record->weekly_points ?? 0;
    }

    public function userDailyPoints(): int
    {
        return $this->record->daily_points ?? 0;
    }   

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}

