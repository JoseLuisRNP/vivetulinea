<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_food', function (Blueprint $table) {
            $table->id();
            $table->float('quantity');
            $table->foreignIdFor(\App\Models\Food::class)->constrained()->cascadeOnDelete();
            $table->string('unit');
            $table->foreignIdFor(\App\Models\Recipe::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
