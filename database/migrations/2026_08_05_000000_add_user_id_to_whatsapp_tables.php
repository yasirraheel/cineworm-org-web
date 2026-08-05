<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToWhatsappTables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('whatsapp_contact_lists') && !Schema::hasColumn('whatsapp_contact_lists', 'user_id')) {
            Schema::table('whatsapp_contact_lists', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('whatsapp_contacts') && !Schema::hasColumn('whatsapp_contacts', 'user_id')) {
            Schema::table('whatsapp_contacts', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('whatsapp_campaigns') && !Schema::hasColumn('whatsapp_campaigns', 'user_id')) {
            Schema::table('whatsapp_campaigns', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->nullable()->after('id')->index();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('whatsapp_contact_lists') && Schema::hasColumn('whatsapp_contact_lists', 'user_id')) {
            Schema::table('whatsapp_contact_lists', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('whatsapp_contacts') && Schema::hasColumn('whatsapp_contacts', 'user_id')) {
            Schema::table('whatsapp_contacts', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('whatsapp_campaigns') && Schema::hasColumn('whatsapp_campaigns', 'user_id')) {
            Schema::table('whatsapp_campaigns', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
}
