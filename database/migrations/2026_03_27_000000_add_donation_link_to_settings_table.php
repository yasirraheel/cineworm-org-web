<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'donation_link')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('donation_link')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'donation_link')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('donation_link');
            });
        }
    }
};

