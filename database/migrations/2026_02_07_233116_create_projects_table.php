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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('location');

            $table->enum('sub_sector', [
                'basic_education',
                'higher_education',
                'madaris_education',
                'technical_education',
                'others'
            ]);

            $table->string('source_of_fund');
            $table->year('funding_year');
            $table->decimal('amount', 15, 2);

            $table->date('start_date');
            $table->date('due_date');

            // Archiving
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
