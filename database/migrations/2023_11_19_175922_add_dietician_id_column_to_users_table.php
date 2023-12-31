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
            $table->string('role', 30)->default('member');
            $table->unsignedBigInteger('dietician_id')->nullable();
            $table->foreign('dietician_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
