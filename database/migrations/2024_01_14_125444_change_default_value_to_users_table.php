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
        Schema::table('users', function (Blueprint $table) {
            $table->float('proteins')->default(10.5)->change();
            $table->float('sugars')->default(13.5)->change();
            $table->float('fats')->default(6)->change();
            $table->float('daily_points')->default(30)->change();
            $table->float('weekly_points')->default(35)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
