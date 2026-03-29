<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_page_type_css_reports', function (Blueprint $table): void {
            $table->unique(['profile_id', 'device_class'], 'shop_page_type_css_reports_profile_device_unique');
        });

        Schema::table('prestashop_shop_page_type_css_artifacts', function (Blueprint $table): void {
            $table->unique(['profile_id', 'device_class', 'css_type'], 'shop_page_type_css_artifacts_profile_device_type_unique');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_page_type_css_artifacts', function (Blueprint $table): void {
            $table->dropUnique('shop_page_type_css_artifacts_profile_device_type_unique');
        });

        Schema::table('prestashop_shop_page_type_css_reports', function (Blueprint $table): void {
            $table->dropUnique('shop_page_type_css_reports_profile_device_unique');
        });
    }
};
