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
            $table->boolean('is_actived')->default(true);
            $table->integer('proteins')->default(8);
            $table->integer('sugars')->default(8);
            $table->integer('fats')->default(8);
        });
    }
};
