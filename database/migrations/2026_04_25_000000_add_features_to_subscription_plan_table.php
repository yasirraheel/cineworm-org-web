<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plan', function (Blueprint $table) {
            $table->unsignedBigInteger('included_plan_id')->nullable()->after('plan_device_limit');
            $table->json('features')->nullable()->after('included_plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plan', function (Blueprint $table) {
            $table->dropColumn(['included_plan_id', 'features']);
        });
    }
};
