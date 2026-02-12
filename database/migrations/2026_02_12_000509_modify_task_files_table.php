<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_files', function (Blueprint $table) {

            // 1️⃣ Drop foreign key first
            $table->dropForeign(['task_id']);

            // 2️⃣ Then drop the column
            $table->dropColumn('task_id');

            // 3️⃣ Add new column
            $table->unsignedBigInteger('task_remark_id')->after('id');

            // 4️⃣ Add new foreign key
            $table->foreign('task_remark_id')
                ->references('id')
                ->on('task_remarks')
                ->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::table('task_files', function (Blueprint $table) {

            // Drop new foreign key first
            $table->dropForeign(['task_remark_id']);
            $table->dropColumn('task_remark_id');

            // Restore old column
            $table->unsignedBigInteger('task_id')->after('id');

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');
        });
    }
};
