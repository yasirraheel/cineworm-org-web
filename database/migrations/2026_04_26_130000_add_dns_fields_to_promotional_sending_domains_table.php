<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDnsFieldsToPromotionalSendingDomainsTable extends Migration
{
    public function up()
    {
        Schema::table('promotional_sending_domains', function (Blueprint $table) {
            if (!Schema::hasColumn('promotional_sending_domains', 'dkim_private_key')) {
                $table->longText('dkim_private_key')->nullable()->after('dkim_value');
            }

            if (!Schema::hasColumn('promotional_sending_domains', 'dkim_public_key')) {
                $table->longText('dkim_public_key')->nullable()->after('dkim_private_key');
            }

            if (!Schema::hasColumn('promotional_sending_domains', 'dns_checked_at')) {
                $table->timestamp('dns_checked_at')->nullable()->after('verified_at');
            }
        });
    }

    public function down()
    {
        Schema::table('promotional_sending_domains', function (Blueprint $table) {
            if (Schema::hasColumn('promotional_sending_domains', 'dns_checked_at')) {
                $table->dropColumn('dns_checked_at');
            }

            if (Schema::hasColumn('promotional_sending_domains', 'dkim_public_key')) {
                $table->dropColumn('dkim_public_key');
            }

            if (Schema::hasColumn('promotional_sending_domains', 'dkim_private_key')) {
                $table->dropColumn('dkim_private_key');
            }
        });
    }
}
