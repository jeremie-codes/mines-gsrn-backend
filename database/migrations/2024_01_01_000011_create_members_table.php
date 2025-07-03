<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('membershipNumber')->nullable();
            $table->string('gender')->nullable();
            $table->string('street')->nullable();
            $table->string('category')->nullable();
            $table->string('phone')->nullable();
            $table->string('qrcode_url')->nullable();
            $table->timestamp('date_adhesion')->useCurrent();
            $table->foreignId('chef_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('pool_id')->nullable()->constrained()->onDelete('set null');
            $table->string('libelle_pool')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('face_path')->nullable();
            $table->foreignId('fonction_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
};
