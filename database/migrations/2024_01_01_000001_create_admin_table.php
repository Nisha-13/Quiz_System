<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Idempotent: the table may already exist when the database was
     * provisioned from database/quiz_system.sql.
     */
    public function up(): void
    {
        if (Schema::hasTable('admin')) {
            return;
        }

        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
