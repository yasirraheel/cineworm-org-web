<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('movie_videos', 'creator_sale_enabled')) {
            Schema::table('movie_videos', function (Blueprint $table) {
                $table->boolean('creator_sale_enabled')->default(false)->after('is_owner');
                $table->decimal('creator_price', 10, 2)->default(0)->after('creator_sale_enabled');
                $table->string('creator_currency', 3)->nullable()->after('creator_price');
                $table->text('creator_trailer_url')->nullable()->after('creator_currency');
                $table->string('creator_sale_status', 20)->default('pending')->after('creator_trailer_url');
                $table->timestamp('creator_approved_at')->nullable()->after('creator_sale_status');
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'creator_sale_enabled',
            'creator_price',
            'creator_currency',
            'creator_trailer_url',
            'creator_sale_status',
            'creator_approved_at',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('movie_videos', $column)) {
                Schema::table('movie_videos', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
