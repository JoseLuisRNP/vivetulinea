<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ActiveUsersCurrentMonth extends ChartWidget
{
    protected static ?string $heading = 'Usuarios activos mes actual';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $data = User::query()
            ->when(auth()->user()->isDietician(), fn ($q) => $q->where('dietician_id', auth()->id()))
            ->with('dietician')
            ->whereRole('member')
            ->whereMonth('actived_at', Carbon::now()->month)
            ->groupBy('dietician_id')
            ->get([
                'dietician_id',
                \DB::raw('COUNT(*) as total_users')
            ])->map(fn ($d) => [
                'dietician' => $d->dietician->name,
                'total_users' => $d->total_users
            ]);

        return [
            'datasets' => [
                [
                    'label' => 'Usuarios activos',
                    'backgroundColor' => $data->pluck('dietician')->map(fn ($d) => $this->generateColorFromName($d))->toArray(),
                    'data' => $data->pluck('total_users')->toArray(),
                ],
            ],
            'labels' => $data->pluck('dietician')->toArray(),

        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function generateColorFromName($name) {
        // Use a hash function to convert the string into a numeric value
        $hash = crc32($name);

        // Convert the numeric value to a hex color
        $color = '#' . substr(dechex($hash), 0, 6);

        return $color;
    }
}
