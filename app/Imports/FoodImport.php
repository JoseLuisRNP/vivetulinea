<?php

namespace App\Imports;

use App\Models\Food;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class FoodImport implements ToModel, WithProgressBar, WithHeadingRow
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!$row["nombre"]) {
            return null;
        }
        return Food::updateOrCreate([
            'name' => $row["nombre"],
            'points' => $row["puntos"],
            'quantity' => $row["cantidaden_gramos"] ?? 100,
            'unit' => $row["observaciones"] ?: 'gr',
            'color' => $row["color"] ? $this->parseColor($row["color"]) : 'yellow',
            'no_count' => (bool) $row['hoy_no_cuento'],
        ]);
    }

    private function parseColor(string $color)
    {
        switch ($color) {
            case 'naranja':
            case 'morado':
            case 'amarillo':
                return 'yellow';
            case 'azul':
                return 'blue';
            case 'verde':
                return 'green';
            case 'rojo':
                return 'red';
            case 'negro':
                return 'black';
            default:
                return null;
        }
    }
}
