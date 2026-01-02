<x-filament-panels::page>
    <div class="fi-section-content-ctn">
        {{-- Header con selector de fecha --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header-ctn">
                <div class="fi-section-header mx-4 w-full">
                    <div class="flex items-center justify-between w-full">
                        
                        <div class="flex items-center gap-2 m-auto">
                            <button 
                                wire:click="previousDay" 
                                type="button"
                                class="fi-btn fi-btn-size-sm fi-color-gray fi-btn-outlined"
                            >
                                &lt;
                            </button>
                            <input 
                                type="date" 
                                wire:model.live="selectedDate"
                                class="fi-input"
                            />
                            <button 
                                wire:click="nextDay" 
                                type="button"
                                class="fi-btn fi-btn-size-sm fi-color-gray fi-btn-outlined"
                            >
                                &gt;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de momentos del día --}}
        @php
            $times = ['Desayuno', 'Media mañana', 'Almuerzo', 'Merienda', 'Cena'];
        @endphp

        @foreach($times as $time)
            @php
                $meals = $this->meals->get($time, collect());
                $totalPoints = $this->totalPointsByMeal[$time] ?? 0;
            @endphp
            
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-4">
                <div class="fi-section-header-ctn">
                    <div class="fi-section-header">
                        <details class="group" open>
                            <summary class="flex justify-between items-center cursor-pointer list-none">
                                <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white m-4">
                                    {{ $time }}
                                </h3>
                            </summary>
                            <div class="fi-section-content-ctn">
                                <div class="fi-section-content p-4">
                                    @if($meals->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4">No hay alimentos registrados</p>
                                    @else
                                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($meals as $meal)
                                                <div class="py-3">
                                                    <div class="flex items-start gap-3">
                                                        @php
                                                            $colorStyle = match(true) {
                                                                $meal->color === 'blue' => 'background-color: #3b82f6;',
                                                                $meal->color === 'green' => 'background-color: #22c55e;',
                                                                $meal->color === 'yellow' && !$meal->recipe_id => 'background-color: #eab308;',
                                                                $meal->color === 'red' => 'background-color: #ef4444;',
                                                                $meal->color === 'black' => 'background-color: #000000;',
                                                                $meal->color === 'yellow' && $meal->recipe_id => 'background: linear-gradient(to right, #3b82f6, #22c55e, #ef4444);',
                                                                default => 'background-color: #eab308;'
                                                            };
                                                        @endphp
                                                        <div class="w-3 h-3 rounded-full mt-1 shrink-0" style="{{ $colorStyle }}"></div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex justify-between items-start gap-4">
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="text-sm font-medium text-gray-950 dark:text-white">
                                                                        {{ $meal->name }}
                                                                    </div>
                                                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                        @if($meal->quantity)
                                                                            <span>
                                                                                {{ $meal->quantity }}
                                                                                @if($meal->recipe_id)
                                                                                    {{ $meal->quantity == 1 ? 'ración' : 'raciones' }}
                                                                                @endif
                                                                            </span>
                                                                        @endif
                                                                        @if($meal->recipe_id && $meal->recipe)
                                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                                <span class="w-2 h-2 rounded-full" style="background-color: #22c55e;"></span>
                                                                                <span>{{ $this->formatPoints(($meal->recipe->sugars * $meal->points) / $meal->recipe->points) }}</span>
                                                                                <span class="w-2 h-2 rounded-full ml-2" style="background-color: #3b82f6;"></span>
                                                                                <span>{{ $this->formatPoints(($meal->recipe->proteins * $meal->points) / $meal->recipe->points) }}</span>
                                                                                <span class="w-2 h-2 rounded-full ml-2" style="background-color: #ef4444;"></span>
                                                                                <span>{{ $this->formatPoints(($meal->recipe->fats * $meal->points) / $meal->recipe->points) }}</span>
                                                                                <span class="w-2 h-2 rounded-full ml-2" style="background-color: #eab308;"></span>
                                                                                <span>{{ $this->formatPoints(($meal->recipe->empty_points * $meal->points) / $meal->recipe->points) }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="text-sm font-semibold text-gray-950 dark:text-white shrink-0">
                                                                    {{ $this->formatPoints($meal->points) }} puntos
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 text-right">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Total: </span>
                                            <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $this->formatPoints($totalPoints) }} puntos</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Puntos por color --}}
        @php
            $pointsByColor = $this->pointsByColor;
        @endphp
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-4">
            <div class="fi-section-content-ctn">
                <div class="fi-section-content p-4">
                    <div class="flex flex-wrap justify-evenly gap-4 text-xs">
                        <div class="flex items-center px-3 py-1 rounded-full bg-gray-50 dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10">
                            <div class="w-2 h-2 rounded-full mr-2" style="background-color: #22c55e;"></div>
                            <span class="text-gray-950 dark:text-white">{{ $this->formatPoints($pointsByColor['green'] ?? 0) }} puntos</span>
                        </div>
                        <div class="flex items-center px-3 py-1 rounded-full bg-gray-50 dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10">
                            <div class="w-2 h-2 rounded-full mr-2" style="background-color: #3b82f6;"></div>
                            <span class="text-gray-950 dark:text-white">{{ $this->formatPoints($pointsByColor['blue'] ?? 0) }} puntos</span>
                        </div>
                        <div class="flex items-center px-3 py-1 rounded-full bg-gray-50 dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10">
                            <div class="w-2 h-2 rounded-full mr-2" style="background-color: #ef4444;"></div>
                            <span class="text-gray-950 dark:text-white">{{ $this->formatPoints($pointsByColor['red'] ?? 0) }} puntos</span>
                        </div>
                        <div class="flex items-center px-3 py-1 rounded-full bg-gray-50 dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10">
                            <div class="w-2 h-2 rounded-full mr-2" style="background-color: #eab308;"></div>
                            <span class="text-gray-950 dark:text-white">{{ $this->formatPoints($pointsByColor['yellow'] ?? 0) }} puntos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen del día --}}
        @php
            $displayedTotal = $this->displayedTotalPoints;
            $extraPoints = $this->extraPoints;
            $weeklyExtraPoints = $this->weeklyExtraPoints;
            $hasExceeded = $this->hasExceededDailyPoints();
        @endphp
        <div class="fi-section rounded-xl bg-primary-600 shadow-sm ring-1 ring-primary-600/10 dark:ring-primary-500/20 mt-4">
            <div class="fi-section-content-ctn">
                <div class="fi-section-content p-6">
                    <div class="space-y-4">
                        {{-- Puntos consumidos del día --}}
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white">{{ $this->formatPoints($displayedTotal) }} puntos </div>
                            <div class="text-sm mt-1 text-white/90">
                                de {{ $this->userDailyPoints() }} consumidos del día
                            </div>
                        </div>

                        {{-- Puntos extras del día --}}
                            <div class="pt-3 border-t border-white/20 text-center">
                                <div class="text-xl font-semibold text-white">{{ $this->formatPoints($extraPoints) }} puntos </div>
                                <div class="text-xs mt-1 text-white/80">de {{ $this->userWeeklyPoints() }} extras consumidos este día</div>
                            </div>

                        {{-- Puntos extras de la semana --}}
                            <div class="pt-3 border-t border-white/20 text-center">
                                <div class="text-xl font-semibold text-white">{{ $this->formatPoints($weeklyExtraPoints) }} puntos </div>
                                <div class="text-xs mt-1 text-white/80">de {{ $this->userWeeklyPoints() }} extras consumidos esta semana</div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
