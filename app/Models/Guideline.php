<?php

namespace App\Models;

use App\Traits\ClearsDashboardCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guideline extends Model
{
    use HasFactory, ClearsDashboardCache;

    protected $guarded = [];

    protected $casts = [
        'consumed_at' => 'datetime',
    ];
}
