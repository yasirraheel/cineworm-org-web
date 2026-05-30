<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('buttons_banners', 'placement')) {
            Schema::table('buttons_banners', function (Blueprint $table) {
                $table->string('placement')->default('default')->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('buttons_banners', 'placement')) {
            Schema::table('buttons_banners', function (Blueprint $table) {
                $table->dropColumn('placement');
            });
        }
    }
};
