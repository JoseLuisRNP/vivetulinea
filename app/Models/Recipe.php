<?php

namespace App\Models;

use App\Traits\ClearsDashboardCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory, ClearsDashboardCache;
    protected $guarded = [];

    public function foods()
    {
        return $this->hasMany(RecipeFood::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
