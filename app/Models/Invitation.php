<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dietician()
    {
        return $this->belongsTo(User::class, 'dietician_id');
    }
}
