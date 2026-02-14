<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_activity_log_id')
                ->constrained('task_activity_logs')
                ->cascadeOnDelete();

            $table->string('file_path');
            $table->string('original_name');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_files');
    }
};
