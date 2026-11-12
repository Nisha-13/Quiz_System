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
        if (Schema::hasTable('records')) {
            return;
        }

        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->tinyInteger('status')->default(1)->comment('1 = in progress, 2 = completed');
            $table->timestamps();

            $table->index(['user_id', 'quiz_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
