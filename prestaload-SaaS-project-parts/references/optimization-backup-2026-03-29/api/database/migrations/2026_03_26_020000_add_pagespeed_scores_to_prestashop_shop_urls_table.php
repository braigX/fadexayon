<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_urls', function (Blueprint $table): void {
            $table->unsignedTinyInteger('mobile_score')->nullable()->after('status');
            $table->unsignedTinyInteger('desktop_score')->nullable()->after('mobile_score');
            $table->timestamp('last_scanned_at')->nullable()->after('desktop_score');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_urls', function (Blueprint $table): void {
            $table->dropColumn([
                'mobile_score',
                'desktop_score',
                'last_scanned_at',
            ]);
        });
    }
};
