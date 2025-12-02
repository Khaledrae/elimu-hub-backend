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

        //
        Schema::table('courses', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['teacher_id']);

            // Recreate the foreign key to reference teachers.user_id
            $table->foreign('teacher_id')
                ->references('user_id')
                ->on('teachers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //

        Schema::table('courses', function (Blueprint $table) {
            // Drop the custom foreign key
            $table->dropForeign(['teacher_id']);

            // Restore the original foreign key that references id
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('set null');
               
        });
    }
};
