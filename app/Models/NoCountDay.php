<?php

namespace App\Models;

use App\Traits\ClearsDashboardCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoCountDay extends Model
{
    use HasFactory, ClearsDashboardCache;

    protected $guarded = [];

    // Specify the casting for the 'date' attribute
    protected $casts = [
        'date' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
