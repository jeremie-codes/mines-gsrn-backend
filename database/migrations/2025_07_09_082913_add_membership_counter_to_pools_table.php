<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembershipCounterToPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pools', function (Blueprint $table) {
            $table->unsignedInteger('membership_counter')->default(0)->after('is_active')
                ->comment('Counter for membership numbers, incremented with each new member');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pools', function (Blueprint $table) {
            //
        });
    }
}
