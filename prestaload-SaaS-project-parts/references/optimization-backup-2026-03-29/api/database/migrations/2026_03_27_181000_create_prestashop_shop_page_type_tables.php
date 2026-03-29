<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestashop_shop_page_type_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('prestashop_shop_id')->constrained('prestashop_shops')->cascadeOnDelete();
            $table->foreignUuid('page_type_id')->constrained('prestashop_page_types')->cascadeOnDelete();
            $table->string('status', 30)->default('draft');
            $table->timestamp('last_aggregated_at')->nullable();
            $table->timestamps();

            $table->unique(['prestashop_shop_id', 'page_type_id'], 'prestashop_shop_page_type_profile_unique');
        });

        Schema::create('prestashop_shop_page_type_profile_urls', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('profile_id')->constrained('prestashop_shop_page_type_profiles')->cascadeOnDelete();
            $table->foreignUuid('prestashop_shop_url_id')->constrained('prestashop_shop_urls')->cascadeOnDelete();
            $table->decimal('sample_weight', 8, 4)->default(1);
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamps();

            $table->unique(['profile_id', 'prestashop_shop_url_id'], 'prestashop_shop_page_type_profile_url_unique');
        });

        Schema::create('prestashop_shop_page_type_css_reports', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('profile_id')->constrained('prestashop_shop_page_type_profiles')->cascadeOnDelete();
            $table->foreignUuid('source_optimization_css_report_id')->nullable()->constrained('optimization_css_reports')->nullOnDelete();
            $table->string('device_class', 30)->default('desktop');
            $table->unsignedInteger('sample_count')->default(0);
            $table->unsignedInteger('stylesheet_count')->default(0);
            $table->unsignedBigInteger('total_css_bytes')->default(0);
            $table->unsignedBigInteger('total_used_css_bytes')->default(0);
            $table->decimal('used_ratio', 8, 4)->default(0);
            $table->decimal('unused_ratio', 8, 4)->default(0);
            $table->json('coverage_json')->nullable();
            $table->timestamp('last_compiled_at')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'device_class'], 'shop_page_type_css_reports_profile_device_index');
        });

        Schema::create('prestashop_shop_page_type_css_report_stylesheets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_page_type_css_report_id')->constrained('prestashop_shop_page_type_css_reports')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->string('style_sheet_key')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('origin', 50)->nullable();
            $table->boolean('is_inline')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->unsignedBigInteger('bytes')->default(0);
            $table->unsignedBigInteger('used_bytes')->default(0);
            $table->decimal('used_ratio', 8, 4)->default(0);
            $table->unsignedInteger('rule_count')->nullable();
            $table->unsignedBigInteger('minified_bytes')->nullable();
            $table->timestamps();

            $table->index(['shop_page_type_css_report_id', 'position'], 'shop_page_type_css_stylesheets_report_position_index');
            $table->index(['source_url'], 'shop_page_type_css_stylesheets_source_url_index');
        });

        Schema::create('prestashop_shop_page_type_css_artifacts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('profile_id')->constrained('prestashop_shop_page_type_profiles')->cascadeOnDelete();
            $table->string('device_class', 30)->default('desktop');
            $table->string('css_type', 30)->default('used_css');
            $table->string('status', 30)->default('draft');
            $table->string('storage_path')->nullable();
            $table->unsignedBigInteger('bytes')->default(0);
            $table->string('sha256', 64)->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'device_class', 'css_type'], 'shop_page_type_css_artifacts_profile_device_type_index');
        });

        Schema::create('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('profile_id')->constrained('prestashop_shop_page_type_profiles')->cascadeOnDelete();
            $table->string('asset_type', 20)->default('css');
            $table->string('asset_url', 2048)->nullable();
            $table->string('asset_pattern', 255)->nullable();
            $table->string('recommended_action', 30)->default('keep');
            $table->string('effective_action', 30)->default('keep');
            $table->string('action_source', 30)->default('auto');
            $table->decimal('confidence', 8, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'asset_type'], 'shop_page_type_asset_rules_profile_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestashop_shop_page_type_asset_rules');
        Schema::dropIfExists('prestashop_shop_page_type_css_artifacts');
        Schema::dropIfExists('prestashop_shop_page_type_css_report_stylesheets');
        Schema::dropIfExists('prestashop_shop_page_type_css_reports');
        Schema::dropIfExists('prestashop_shop_page_type_profile_urls');
        Schema::dropIfExists('prestashop_shop_page_type_profiles');
    }
};
