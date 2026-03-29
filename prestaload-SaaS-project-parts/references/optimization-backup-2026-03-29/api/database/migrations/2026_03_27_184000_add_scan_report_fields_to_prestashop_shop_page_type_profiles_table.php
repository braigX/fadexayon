<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_page_type_profiles', function (Blueprint $table): void {
            $table->string('scan_provider')->nullable()->after('status');
            $table->foreignUuid('scan_source_prestashop_shop_url_id')->nullable()->after('scan_provider')
                ->constrained('prestashop_shop_urls')
                ->nullOnDelete();
            $table->unsignedTinyInteger('mobile_score')->nullable()->after('scan_source_prestashop_shop_url_id');
            $table->unsignedTinyInteger('desktop_score')->nullable()->after('mobile_score');
            $table->timestamp('last_scanned_at')->nullable()->after('desktop_score');
            $table->json('scan_report_json')->nullable()->after('last_scanned_at');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_page_type_profiles', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('scan_source_prestashop_shop_url_id');
            $table->dropColumn([
                'scan_provider',
                'mobile_score',
                'desktop_score',
                'last_scanned_at',
                'scan_report_json',
            ]);
        });
    }
};
