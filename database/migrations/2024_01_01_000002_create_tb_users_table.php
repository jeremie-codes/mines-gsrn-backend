<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_users', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('code', 50)->unique();
            // $table->timestamp('created_at', 6);
            $table->string('email', 50)->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamp('modified_at', 6)->nullable();
            $table->string('password', 100);
            $table->string('phone_number', 20)->nullable();
            $table->string('username', 50)->unique();
            $table->enum('role', ['admin', 'manager'])->nullable()->default('manager');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_users');
        Schema::dropIfExists('password_reset_tokens');
    }
};
