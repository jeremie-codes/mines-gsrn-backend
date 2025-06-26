<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_merchants', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false);
            $table->string('code', 50)->unique();
            // $table->timestamp('created_at', 6);
            $table->timestamp('modified_at', 6)->nullable();
            $table->string('name', 50);
            $table->boolean('own_config')->default(false);
            $table->string('sms_from', 50)->nullable();
            $table->string('sms_login', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_merchants');
    }
};
