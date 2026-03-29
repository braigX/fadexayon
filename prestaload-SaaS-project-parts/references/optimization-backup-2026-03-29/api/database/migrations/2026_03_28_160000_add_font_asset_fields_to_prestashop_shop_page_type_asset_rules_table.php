<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->string('font_asset_status', 30)->nullable()->after('minified_css_status');
            $table->string('font_css_public_path')->nullable()->after('minified_css_sha256');
            $table->string('font_css_public_url')->nullable()->after('font_css_public_path');
            $table->unsignedBigInteger('font_css_bytes')->nullable()->after('font_css_public_url');
            $table->string('font_css_sha256', 64)->nullable()->after('font_css_bytes');
            $table->json('font_meta_json')->nullable()->after('font_css_sha256');
            $table->timestamp('last_font_built_at')->nullable()->after('last_minified_at');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->dropColumn([
                'font_asset_status',
                'font_css_public_path',
                'font_css_public_url',
                'font_css_bytes',
                'font_css_sha256',
                'font_meta_json',
                'last_font_built_at',
            ]);
        });
    }
};
