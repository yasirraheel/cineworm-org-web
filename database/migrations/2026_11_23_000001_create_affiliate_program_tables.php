<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateProgramTables extends Migration
{
    public function up()
    {
        Schema::create('affiliate_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->string('landing_title')->default('Earn more with Cineworm referrals');
            $table->text('landing_description')->nullable();
            $table->decimal('recurring_commission_percent', 8, 2)->default(5);
            $table->unsignedInteger('max_referrals_per_cycle')->default(15);
            $table->decimal('minimum_payout_amount', 12, 2)->default(0);
            $table->boolean('reset_on_payout')->default(true);
            $table->text('payout_instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->unsignedInteger('min_referrals');
            $table->unsignedInteger('max_referrals')->nullable();
            $table->decimal('commission_percent', 8, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['active', 'min_referrals']);
        });

        Schema::create('affiliate_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('referral_code', 40)->unique();
            $table->unsignedInteger('cycle_number')->default(1);
            $table->unsignedInteger('referrals_count')->default(0);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_account_id')->index();
            $table->unsignedBigInteger('referred_user_id')->unique();
            $table->string('referral_code', 40)->index();
            $table->string('status', 30)->default('pending')->index();
            $table->unsignedInteger('cycle_number')->default(1);
            $table->unsignedBigInteger('first_transaction_id')->nullable()->index();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_account_id')->index();
            $table->unsignedBigInteger('referred_user_id')->index();
            $table->unsignedBigInteger('transaction_id')->nullable()->unique();
            $table->string('type', 30)->default('recurring');
            $table->unsignedInteger('cycle_number')->default(1);
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->decimal('rate_percent', 8, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('currency', 10)->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_account_id')->index();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->nullable();
            $table->string('method', 80)->nullable();
            $table->text('account_details')->nullable();
            $table->string('status', 30)->default('requested')->index();
            $table->unsignedBigInteger('processed_by')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        DB::table('affiliate_settings')->insert([
            'enabled' => 1,
            'landing_title' => 'Earn more with Cineworm referrals',
            'landing_description' => 'Share Cineworm with your audience and earn commission on confirmed subscriptions.',
            'recurring_commission_percent' => 5,
            'max_referrals_per_cycle' => 15,
            'minimum_payout_amount' => 0,
            'reset_on_payout' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('affiliate_tiers')->insert([
            ['name' => 'Starter', 'min_referrals' => 1, 'max_referrals' => 5, 'commission_percent' => 15, 'active' => 1, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Growth', 'min_referrals' => 6, 'max_referrals' => 10, 'commission_percent' => 20, 'active' => 1, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pro', 'min_referrals' => 11, 'max_referrals' => 14, 'commission_percent' => 25, 'active' => 1, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Top tier', 'min_referrals' => 15, 'max_referrals' => 15, 'commission_percent' => 30, 'active' => 1, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('affiliate_referrals');
        Schema::dropIfExists('affiliate_accounts');
        Schema::dropIfExists('affiliate_tiers');
        Schema::dropIfExists('affiliate_settings');
    }
}
