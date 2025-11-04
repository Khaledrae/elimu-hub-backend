<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');

            $table->string('title');
            $table->text('instructions')->nullable();
            $table->enum('type', ['quiz', 'assignment', 'exam'])->default('quiz');
            $table->integer('total_marks')->default(100);
            $table->integer('duration_minutes')->nullable(); // For timed tests
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
