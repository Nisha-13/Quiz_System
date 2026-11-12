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
        if (Schema::hasTable('mcqs')) {
            return;
        }

        Schema::create('mcqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->enum('correct_ans', ['a', 'b', 'c', 'd']);
            $table->foreignId('admin_id')->default(1)->constrained('admin')->cascadeOnUpdate();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcqs');
    }
};
