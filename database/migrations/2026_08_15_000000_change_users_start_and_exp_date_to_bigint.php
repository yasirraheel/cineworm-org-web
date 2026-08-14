<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeUsersStartAndExpDateToBigint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY start_date BIGINT NULL");
        DB::statement("ALTER TABLE users MODIFY exp_date BIGINT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY start_date INT NULL");
        DB::statement("ALTER TABLE users MODIFY exp_date INT NULL");
    }
}
