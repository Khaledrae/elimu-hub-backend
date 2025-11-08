<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Add the course_id column (nullable since we already have data)
            $table->foreignId('course_id')
                  ->nullable()
                  ->after('lesson_id')
                  ->constrained('courses')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Drop the foreign key and column when rolling back
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};

