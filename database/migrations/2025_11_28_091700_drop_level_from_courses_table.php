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
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'level')) {
                $table->dropColumn('level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Re-add the column if migration is rolled back
            $table->enum('level', [
                'lower_primary',
                'upper_primary',
                'junior_secondary',
                'senior_secondary'
            ])->nullable();
        });
    }
};
