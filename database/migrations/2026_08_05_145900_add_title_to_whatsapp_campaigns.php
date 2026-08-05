<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleToWhatsappCampaigns extends Migration
{
    public function up()
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_campaigns', 'title')) {
                $table->string('title')->after('user_id')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_campaigns', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
}
