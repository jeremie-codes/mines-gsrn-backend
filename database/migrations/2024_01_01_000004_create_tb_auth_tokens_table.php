<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_auth_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('active')->default(true);
            $table->string('code', 200);
            // $table->timestamp('created_at', 6);
            $table->timestamp('expires_at', 6)->nullable();
            $table->timestamp('modified_at', 6)->nullable();
            $table->text('token')->nullable();
            $table->foreignId('auth_id')->nullable()->constrained('tb_auths');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_auth_tokens');

  }
};
