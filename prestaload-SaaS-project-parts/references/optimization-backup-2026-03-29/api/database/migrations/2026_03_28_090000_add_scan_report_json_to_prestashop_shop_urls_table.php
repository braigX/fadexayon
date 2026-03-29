<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_urls', function (Blueprint $table): void {
            $table->json('scan_report_json')->nullable()->after('desktop_score');
        });

        $profiles = DB::table('prestashop_shop_page_type_profiles')
            ->whereNotNull('scan_source_prestashop_shop_url_id')
            ->whereNotNull('scan_report_json')
            ->get();

        foreach ($profiles as $profile) {
            DB::table('prestashop_shop_urls')
                ->where('id', $profile->scan_source_prestashop_shop_url_id)
                ->update([
                    'scan_report_json' => $profile->scan_report_json,
                    'mobile_score' => $profile->mobile_score,
                    'desktop_score' => $profile->desktop_score,
                    'last_scanned_at' => $profile->last_scanned_at,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_urls', function (Blueprint $table): void {
            $table->dropColumn('scan_report_json');
        });
    }
};
