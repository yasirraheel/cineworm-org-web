<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [];

        if (!Schema::hasColumn('movie_videos', 'writer')) {
            $columns[] = 'writer';
        }

        if (!Schema::hasColumn('movie_videos', 'production_company')) {
            $columns[] = 'production_company';
        }

        if (!empty($columns)) {
            Schema::table('movie_videos', function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->text($column)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('movie_videos', function (Blueprint $table) {
            if (Schema::hasColumn('movie_videos', 'writer')) {
                $table->dropColumn('writer');
            }
            if (Schema::hasColumn('movie_videos', 'production_company')) {
                $table->dropColumn('production_company');
            }
        });
    }
};
