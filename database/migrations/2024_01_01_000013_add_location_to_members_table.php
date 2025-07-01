<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null')->after('site_id');
            $table->foreignId('township_id')->nullable()->constrained()->onDelete('set null')->after('city_id');
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['township_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['township_id', 'city_id']);
        });
    }
};