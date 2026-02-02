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

    public function userFood()
    {
        return $this->belongsTo(UserFood::class);
    }

    protected function getPointsAttribute()
    {
        if ($this->user_food_id) {
            return $this->userFood->points;
        }

        $result = ($this->quantity * $this->food->points) / $this->food->quantity;
        return max(round($result * 2) / 2, 0);
    }
}
