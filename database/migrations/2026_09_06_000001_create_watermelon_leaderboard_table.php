<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatermelonLeaderboardTable extends Migration
{
    public function up()
    {
        Schema::create('watermelon_leaderboard', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // null = guest
            $table->string('player_name', 60)->default('Guest');
            $table->unsignedInteger('score');
            $table->string('guest_token', 64)->nullable(); // unique token for guest session
            $table->timestamps();

            $table->index('score');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('watermelon_leaderboard');
    }
}
