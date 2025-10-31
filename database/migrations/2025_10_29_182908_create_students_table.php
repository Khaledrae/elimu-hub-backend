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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Core student details
            $table->string('admission_number')->nullable();
            $table->string('grade_level')->nullable();  // e.g., Grade 6, Grade 7
            $table->string('school_name')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // Parent/guardian reference (future portal)
            $table->foreignId('guardian_id')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->string('status')->default('active'); // active, graduated, suspended
            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
