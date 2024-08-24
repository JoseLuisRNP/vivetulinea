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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->float('quantity');
            $table->string('unit');
            $table->float('points');
            $table->float('proteins');
            $table->float('sugars');
            $table->float('fats');


            $table->timestamps();
        });
    }
};
