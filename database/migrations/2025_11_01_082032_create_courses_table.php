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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            //$table->foreignId('curriculum_id')->nullable()->constrained('curriculums')->onDelete('set null');
            $table->string('thumbnail')->nullable();
            $table->enum('level', ['lower_primary', 'upper_primary', 'junior_secondary', 'senior_secondary'])->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
