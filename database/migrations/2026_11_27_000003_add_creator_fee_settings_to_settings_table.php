<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'creator_sale_fee_percent')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->decimal('creator_sale_fee_percent', 5, 2)->default(0)->after('currency_code');
                $table->decimal('creator_sale_fee_fixed', 10, 2)->default(0)->after('creator_sale_fee_percent');
                $table->boolean('creator_sale_fee_included')->default(true)->after('creator_sale_fee_fixed');
            });
        }
    }

    public function down(): void
    {
        foreach (['creator_sale_fee_percent', 'creator_sale_fee_fixed', 'creator_sale_fee_included'] as $column) {
            if (Schema::hasColumn('settings', $column)) {
                Schema::table('settings', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
