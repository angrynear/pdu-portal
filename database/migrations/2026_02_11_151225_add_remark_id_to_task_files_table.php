<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_files', function (Blueprint $table) {
            $table->foreignId('remark_id')
                  ->nullable()
                  ->after('task_id')
                  ->constrained('task_remarks')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('task_files', function (Blueprint $table) {
            $table->dropForeign(['remark_id']);
            $table->dropColumn('remark_id');
        });
    }
};
