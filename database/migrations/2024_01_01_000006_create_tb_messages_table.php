<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('closed')->default(false);
            $table->text('content')->nullable();
            // $table->timestamp('created_at', 6);
            $table->boolean('delivered')->default(false);
            $table->timestamp('modified_at', 6)->nullable();
            $table->integer('nb_trial_check')->default(0);
            $table->text('notification')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('reference', 50)->nullable();
            $table->text('response')->nullable();
            $table->boolean('sent')->default(false);
            $table->string('sms_from', 50)->nullable();
            $table->string('sms_login', 50)->nullable();
            $table->foreignId('auth_id')->nullable()->constrained('tb_auths');
            $table->foreignId('merchant_id')->nullable()->constrained('tb_merchants');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_messages');

  }
};
