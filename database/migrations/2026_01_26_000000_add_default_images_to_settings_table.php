<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultImagesToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'site_movie_thumb_default')) {
                $table->string('site_movie_thumb_default')->nullable()->after('site_copyright');
            }
            if (!Schema::hasColumn('settings', 'site_movie_poster_default')) {
                $table->string('site_movie_poster_default')->nullable()->after('site_movie_thumb_default');
            }
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
            $table->dropColumn(['site_movie_thumb_default', 'site_movie_poster_default']);
        });
    }
}
