<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('film_purchases')) {
            return;
        }

        Schema::create('film_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id')->index();
            $table->unsignedBigInteger('buyer_id')->index();
            $table->unsignedBigInteger('seller_id')->nullable()->index();
            $table->string('gateway', 40)->nullable();
            $table->string('provider_order_id', 191)->nullable()->index();
            $table->string('provider_payment_id', 191)->nullable()->index();
            $table->string('currency', 3);
            $table->decimal('film_amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // These are intentionally indexed rather than constrained. The legacy
            // movie_videos/users tables use installation-specific integer types.
            // Application-level checks keep purchases safe without making this
            // migration fail on older Cineworm databases.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('film_purchases');
    }
};
