<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weight extends Model
{
    protected $fillable = [
        'user_id',
        'value',
        'notes',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
