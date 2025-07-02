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
            $table->string('qrcode_url')->nullable()->after('code');
            $table->timestamp('date_adhesion')->useCurrent()->after('qrcode_url');
        });
    }

    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('membership_counter');
            $table->dropColumn('qrcode_url');
            $table->dropColumn('date_adhesion');
        });
    }
};
