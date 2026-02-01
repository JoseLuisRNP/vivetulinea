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
        Schema::table('recipes', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('user_foods', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('meals', function (Blueprint $table) {
            $table->index(['user_id', 'consumed_at']);
        });

        Schema::table('no_count_days', function (Blueprint $table) {
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('user_foods', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('meals', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'consumed_at']);
        });

        Schema::table('no_count_days', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'date']);
        });
    }
};
