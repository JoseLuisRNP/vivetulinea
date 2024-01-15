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
        Schema::table('invitations', function (Blueprint $table) {
            $table->float('sugars')->default(13.5);
            $table->float('proteins')->default(10.5);
            $table->float('fats')->default(6);
            $table->float('daily_points')->default(30);
            $table->float('weekly_points')->default(35);
            $table->string('name')->default('Sin nombre');
        });
    }
};
