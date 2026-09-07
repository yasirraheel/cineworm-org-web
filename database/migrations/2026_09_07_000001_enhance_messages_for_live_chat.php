<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ensure user_id can be NULL for guests
        try {
            DB::statement("ALTER TABLE `messages` MODIFY `user_id` BIGINT UNSIGNED NULL;");
        } catch (\Exception $e) {}

        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'guest_token')) {
                $table->string('guest_token', 64)->nullable()->index()->after('user_id');
            }

            if (!Schema::hasColumn('messages', 'guest_name')) {
                $table->string('guest_name', 50)->nullable()->after('guest_token');
            }

            if (!Schema::hasColumn('messages', 'sender_type')) {
                $table->string('sender_type', 20)->default('user')->after('message');
            }

            if (!Schema::hasColumn('messages', 'sender')) {
                $table->string('sender', 20)->default('user')->after('message');
            }

            if (!Schema::hasColumn('messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('sender');
            }

            if (!Schema::hasColumn('messages', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('is_read');
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $cols = [];
            foreach (['guest_token', 'guest_name', 'sender_type', 'ip_address'] as $col) {
                if (Schema::hasColumn('messages', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
