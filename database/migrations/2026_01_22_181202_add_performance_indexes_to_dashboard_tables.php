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
        Schema::table('meals', function (Blueprint $table) {
            $table->index('consumed_at');
        });

        Schema::table('no_count_days', function (Blueprint $table) {
            $table->index('date');
        });

        Schema::table('guidelines', function (Blueprint $table) {
            $table->index('consumed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropIndex(['consumed_at']);
        });

        Schema::table('no_count_days', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        Schema::table('guidelines', function (Blueprint $table) {
            $table->dropIndex(['consumed_at']);
        });
    }
};
