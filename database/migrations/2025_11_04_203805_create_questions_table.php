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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // ✅ Each question should belong to an assessment (not course directly)
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');

            // The teacher (user_id) who created it — optional
            $table->foreignId('set_by')->nullable()->constrained('teachers', 'user_id')->onDelete('set null');

            $table->text('question_text');
            $table->integer('marks')->default(1);

            // ✅ Consistent MCQ structure
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();

            // Use CHAR(1) for correctness and restrict via enum
            $table->enum('correct_option', ['A', 'B', 'C', 'D']);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
