<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('site_default_movie_thumb')->nullable()->after('site_logo');
            $table->string('site_default_movie_poster')->nullable()->after('site_default_movie_thumb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['site_default_movie_thumb', 'site_default_movie_poster']);
        });
    }
};
