<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionalUserCampaignTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('promotional_contact_lists')) {
            Schema::create('promotional_contact_lists', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('promotional_contacts')) {
            Schema::create('promotional_contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedBigInteger('contact_list_id');
                $table->string('name')->nullable();
                $table->string('email');
                $table->string('company')->nullable();
                $table->string('tags')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
                $table->unique(['contact_list_id', 'email']);
            });
        }

        if (!Schema::hasTable('promotional_campaigns')) {
            Schema::create('promotional_campaigns', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedBigInteger('smtp_server_id')->nullable();
                $table->unsignedBigInteger('sending_domain_id')->nullable();
                $table->unsignedBigInteger('tracking_domain_id')->nullable();
                $table->unsignedBigInteger('contact_list_id');
                $table->string('name');
                $table->string('subject');
                $table->string('preview_text')->nullable();
                $table->string('from_name')->nullable();
                $table->string('from_email');
                $table->string('reply_to_email')->nullable();
                $table->longText('html_content');
                $table->longText('plain_text')->nullable();
                $table->string('status')->default('draft')->index();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->unsignedInteger('total_contacts')->default(0);
                $table->unsignedInteger('processed_contacts')->default(0);
                $table->unsignedInteger('success_count')->default(0);
                $table->unsignedInteger('failed_count')->default(0);
                $table->text('last_error')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('promotional_campaign_sends')) {
            Schema::create('promotional_campaign_sends', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id');
                $table->unsignedBigInteger('contact_id')->nullable();
                $table->string('email');
                $table->string('subject');
                $table->string('status')->default('pending')->index();
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('promotional_campaign_sends');
        Schema::dropIfExists('promotional_campaigns');
        Schema::dropIfExists('promotional_contacts');
        Schema::dropIfExists('promotional_contact_lists');
    }
}
