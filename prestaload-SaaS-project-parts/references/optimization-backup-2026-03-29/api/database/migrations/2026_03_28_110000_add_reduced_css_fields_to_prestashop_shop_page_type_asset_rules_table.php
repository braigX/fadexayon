<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->string('reduced_css_status')->nullable()->after('effective_action');
            $table->string('reduced_css_public_path')->nullable()->after('notes');
            $table->string('reduced_css_public_url')->nullable()->after('reduced_css_public_path');
            $table->unsignedInteger('reduced_css_bytes')->nullable()->after('reduced_css_public_url');
            $table->string('reduced_css_sha256', 64)->nullable()->after('reduced_css_bytes');
            $table->timestamp('last_reduced_at')->nullable()->after('last_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->dropColumn([
                'reduced_css_status',
                'reduced_css_public_path',
                'reduced_css_public_url',
                'reduced_css_bytes',
                'reduced_css_sha256',
                'last_reduced_at',
            ]);
        });
    }
};
