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
        if (Schema::hasTable('mcq_records')) {
            return;
        }

        Schema::create('mcq_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained('records')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('mcq_id')->constrained('mcqs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('select_answer', ['a', 'b', 'c', 'd'])->nullable();
            $table->tinyInteger('is_correct')->default(0);
            $table->timestamps();

            $table->unique(['record_id', 'mcq_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcq_records');
    }
};
