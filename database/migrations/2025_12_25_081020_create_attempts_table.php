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
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            // Attempt count (1st attempt, 2nd attempt, etc)
            $table->unsignedInteger('attempt_number')->default(1);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->integer('total_marks_scored')->default(0);
            $table->integer('total_marks_possible')->default(0);
            $table->decimal('score_percentage', 5, 2)->default(0);

            $table->enum('status', ['in_progress', 'submitted', 'graded'])
                ->default('in_progress');

            $table->timestamps();

            // One row per attempt number
            $table->unique(['student_id', 'assessment_id', 'attempt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
