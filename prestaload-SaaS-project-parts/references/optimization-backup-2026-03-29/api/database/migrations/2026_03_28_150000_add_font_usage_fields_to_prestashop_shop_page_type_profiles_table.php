<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_page_type_profiles', function (Blueprint $table): void {
            $table->timestamp('last_font_scanned_at')->nullable()->after('last_scanned_at');
            $table->json('font_usage_json')->nullable()->after('scan_report_json');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_page_type_profiles', function (Blueprint $table): void {
            $table->dropColumn([
                'last_font_scanned_at',
                'font_usage_json',
            ]);
        });
    }
};
