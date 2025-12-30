<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    const DEFAULTS_PROTEINS = 1.2;
    const DEFAULTS_FATS = 0.8;
    const DEFAULTS_SUGARS = 2.5;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'target_weight' => 'float',
    ];

    public function isSuperAdmin() {
        return $this->isAdmin() && $this->id === 1;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDietician()
    {
        return $this->role === 'dietician';
    }

    public function isMember()
    {
        return $this->role === 'member';
    }

    public function isActive()
    {
        return $this->is_actived;
    }

    public function dietician()
    {
        return $this->belongsTo(User::class, 'dietician_id');
    }

    public function members(){
        return $this->hasMany(User::class, 'dietician_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isDietician();
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function noCountDays()
    {
        return $this->hasMany(NoCountDay::class);
    }

    public function guidelines()
    {
        return $this->hasMany(Guideline::class);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function weights()
    {
        return $this->hasMany(Weight::class);
    }

    public function favoriteFoods()
    {
        return $this->belongsToMany(Food::class, 'food_user')->withTimestamps();
    }

}
