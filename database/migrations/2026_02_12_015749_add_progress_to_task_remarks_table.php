<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_remarks', function (Blueprint $table) {
            $table->integer('progress')->nullable()->after('remark');
        });
    }

    public function down()
    {
        Schema::table('task_remarks', function (Blueprint $table) {
            $table->dropColumn('progress');
        });
    }
};
