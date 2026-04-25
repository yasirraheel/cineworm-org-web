<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionalMailTables extends Migration
{
    public function up()
    {
        Schema::create('promotional_smtp_servers', function (Blueprint $table) {
            $table->id();
            $table->string('server_name');
            $table->string('gateway_type')->default('smtp');
            $table->string('from_name')->nullable();
            $table->string('sender_email');
            $table->string('reply_to_email')->nullable();
            $table->string('host');
            $table->unsignedInteger('port')->default(587);
            $table->string('encryption')->nullable();
            $table->string('username');
            $table->longText('smtp_password')->nullable();
            $table->string('ehlo_domain')->nullable();
            $table->unsignedInteger('min_delay_per_message')->default(0);
            $table->unsignedInteger('max_delay_per_message')->default(0);
            $table->unsignedInteger('pause_after_messages')->default(0);
            $table->unsignedInteger('pause_duration')->default(0);
            $table->unsignedInteger('reset_counter_after_messages')->default(0);
            $table->unsignedInteger('max_messages_per_day')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('is_default')->default(0);
            $table->longText('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('promotional_sending_domains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smtp_server_id')->nullable();
            $table->string('domain');
            $table->string('selector')->default('default');
            $table->string('dkim_type')->default('TXT');
            $table->longText('dkim_value')->nullable();
            $table->string('return_path_subdomain')->nullable();
            $table->longText('spf_value')->nullable();
            $table->string('dmarc_policy')->default('quarantine');
            $table->string('dmarc_report_email')->nullable();
            $table->string('dmarc_alignment')->default('relaxed');
            $table->tinyInteger('dkim_status')->default(0);
            $table->tinyInteger('spf_status')->default(0);
            $table->tinyInteger('dmarc_status')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamp('verified_at')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            $table->foreign('smtp_server_id')
                ->references('id')
                ->on('promotional_smtp_servers')
                ->onDelete('set null');
        });

        Schema::create('promotional_tracking_domains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smtp_server_id')->nullable();
            $table->string('domain');
            $table->string('cname_target');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('verified_at')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            $table->foreign('smtp_server_id')
                ->references('id')
                ->on('promotional_smtp_servers')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotional_tracking_domains');
        Schema::dropIfExists('promotional_sending_domains');
        Schema::dropIfExists('promotional_smtp_servers');
    }
}
