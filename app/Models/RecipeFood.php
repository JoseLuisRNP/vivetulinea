<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeFood extends Model
{
//    protected $appends = ['points'];
    protected $guarded = [];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    protected function getPointsAttribute()
    {
        $result = ($this->quantity * $this->food->points) / $this->food->quantity;
        return  max(round($result * 2) / 2, 0);
    }
}
