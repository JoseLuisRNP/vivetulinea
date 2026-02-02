<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recipe_food', function (Blueprint $table) {
            $table->unsignedBigInteger('food_id')->nullable()->change();
            $table->foreignId('user_food_id')->nullable()->constrained('user_foods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_food', function (Blueprint $table) {
            $table->unsignedBigInteger('food_id')->nullable(false)->change();
            $table->dropForeign(['user_food_id']);
            $table->dropColumn('user_food_id');
        });
    }
};
