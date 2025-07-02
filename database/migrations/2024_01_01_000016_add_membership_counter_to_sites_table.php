<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->integer('membership_counter')->default(0)->after('code');
        });
    }

    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('membership_counter');
        });
    }
};
