<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappCampaignTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('whatsapp_contact_lists')) {
            Schema::create('whatsapp_contact_lists', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('whatsapp_contacts')) {
            Schema::create('whatsapp_contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contact_list_id');
                $table->string('name')->nullable();
                $table->string('phone', 32);
                $table->string('company')->nullable();
                $table->string('tags')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamp('last_sent_at')->nullable();
                $table->timestamp('opt_out_at')->nullable();
                $table->timestamps();
                $table->unique(['contact_list_id', 'phone']);
                $table->index(['status', 'opt_out_at']);
            });
        }

        if (!Schema::hasTable('whatsapp_campaigns')) {
            Schema::create('whatsapp_campaigns', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedBigInteger('contact_list_id');
                $table->string('name');
                $table->longText('message');
                $table->string('status')->default('draft')->index();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->unsignedInteger('total_contacts')->default(0);
                $table->unsignedInteger('processed_contacts')->default(0);
                $table->unsignedInteger('success_count')->default(0);
                $table->unsignedInteger('failed_count')->default(0);
                $table->unsignedInteger('skipped_count')->default(0);
                $table->unsignedSmallInteger('batch_size')->default(10);
                $table->unsignedSmallInteger('min_delay_seconds')->default(25);
                $table->unsignedSmallInteger('max_delay_seconds')->default(75);
                $table->unsignedSmallInteger('pause_after_messages')->default(20);
                $table->unsignedInteger('pause_duration_seconds')->default(900);
                $table->unsignedInteger('daily_limit')->default(250);
                $table->time('quiet_hours_start')->nullable();
                $table->time('quiet_hours_end')->nullable();
                $table->text('last_error')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('whatsapp_campaign_sends')) {
            Schema::create('whatsapp_campaign_sends', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id');
                $table->unsignedBigInteger('contact_id')->nullable();
                $table->string('phone', 32);
                $table->longText('message')->nullable();
                $table->string('status')->default('pending')->index();
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->json('provider_response')->nullable();
                $table->timestamps();
                $table->index(['campaign_id', 'status']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_campaign_sends');
        Schema::dropIfExists('whatsapp_campaigns');
        Schema::dropIfExists('whatsapp_contacts');
        Schema::dropIfExists('whatsapp_contact_lists');
    }
}
