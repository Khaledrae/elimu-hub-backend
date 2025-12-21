<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('covered_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->enum('status', ['in-progress', 'completed', 'failed'])->default('in-progress'); // Added 'failed'
            $table->decimal('score', 5, 2)->nullable(); // Changed to decimal for percentages
            $table->integer('time_spent')->default(0); // seconds
            $table->integer('attempts')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Composite unique constraint
            $table->unique(['student_id', 'lesson_id']);

            // Indexes for better performance
            $table->index(['student_id', 'status']);
            $table->index(['student_id', 'course_id']);
            $table->index(['student_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('covered_lessons');
    }
};
