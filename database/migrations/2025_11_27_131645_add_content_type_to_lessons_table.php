<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Create migration: php artisan make:migration add_content_type_to_lessons_table
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->enum('content_type', ['text', 'video', 'document', 'mixed'])->default('text')->after('title');
        });
    }

    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });
    }
};
