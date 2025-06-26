<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_auths', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false);
            $table->string('code', 50)->unique();
            // $table->timestamp('created_at', 6);
            $table->timestamp('modified_at', 6)->nullable();
            $table->string('password', 100)->nullable();
            $table->text('token')->nullable();
            $table->string('username', 100)->nullable();
            $table->foreignId('merchant_id')->nullable()->constrained('tb_merchants');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_auths');
    }
};
