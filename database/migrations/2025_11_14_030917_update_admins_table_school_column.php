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
        Schema::table('admins', function (Blueprint $table) {
            // Drop old school_name if it exists
            if (Schema::hasColumn('admins', 'school_name')) {
                $table->dropColumn('school_name');
            }

            // Add school_id after dropping old column
            $table->unsignedBigInteger('school_id')->nullable()->after('admin_level');

            // Optional: add FK if schools table exists
            if (Schema::hasTable('schools')) {
                $table->foreign('school_id')
                      ->references('id')->on('schools')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Rollback: drop school_id
            if (Schema::hasColumn('admins', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }

            // Restore school_name column
            $table->string('school_name')->nullable();
        });
    }
};
