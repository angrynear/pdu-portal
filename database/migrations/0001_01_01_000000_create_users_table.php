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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();

            // Authentication
            $table->string('password');

            // Roles & account control
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->enum('account_status', ['active', 'deactivated'])->default('active');

            // Profile (optional at creation)
            $table->string('photo')->nullable();
            $table->string('profession')->nullable();
            $table->string('designation')->nullable();
            $table->string('employment_status')->nullable();
            $table->date('employment_started')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
