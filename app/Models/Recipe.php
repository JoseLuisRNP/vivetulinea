<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
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
