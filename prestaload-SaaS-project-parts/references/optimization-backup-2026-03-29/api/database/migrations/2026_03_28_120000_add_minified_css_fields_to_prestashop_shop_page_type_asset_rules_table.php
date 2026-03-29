<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->string('minified_css_status')->nullable()->after('reduced_css_status');
            $table->string('minified_css_public_path')->nullable()->after('reduced_css_public_url');
            $table->string('minified_css_public_url')->nullable()->after('minified_css_public_path');
            $table->unsignedInteger('minified_css_asset_bytes')->nullable()->after('minified_css_public_url');
            $table->string('minified_css_sha256', 64)->nullable()->after('minified_css_asset_bytes');
            $table->timestamp('last_minified_at')->nullable()->after('last_reduced_at');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->dropColumn([
                'minified_css_status',
                'minified_css_public_path',
                'minified_css_public_url',
                'minified_css_asset_bytes',
                'minified_css_sha256',
                'last_minified_at',
            ]);
        });
    }
};
