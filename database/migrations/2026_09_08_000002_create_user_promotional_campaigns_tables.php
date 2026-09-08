<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('user_promotional_campaigns')) {
            Schema::create('user_promotional_campaigns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable()->index();
                $table->string('title');
                $table->string('subject');
                $table->longText('content');
                $table->string('audience', 50)->default('all');
                $table->integer('total_recipients')->default(0);
                $table->integer('sent_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->string('status', 30)->default('queued')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_promotional_queue')) {
            Schema::create('user_promotional_queue', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('email', 191)->index();
                $table->string('name', 191)->nullable();
                $table->tinyInteger('status')->default(0)->index()->comment('0: pending, 1: sent, 2: failed');
                $table->text('error_message')->nullable();
                $table->dateTime('sent_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_promotional_queue');
        Schema::dropIfExists('user_promotional_campaigns');
    }
};
