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
        Schema::create('live_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title');
            $table->string('zoom_meeting_id')->nullable();
            $table->text('zoom_join_url')->nullable();
            $table->text('zoom_start_url')->nullable();
            $table->string('zoom_meeting_password')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('status')->default(0); // 0 = pending, 1 = approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_broadcasts');
    }
};
