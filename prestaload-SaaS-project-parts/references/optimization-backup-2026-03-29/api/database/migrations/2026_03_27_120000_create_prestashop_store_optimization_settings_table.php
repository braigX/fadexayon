<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestashop_store_optimization_settings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('prestashop_store_id')->unique();
            $table->boolean('generate_critical_css')->default(true);
            $table->boolean('minify_css')->default(true);
            $table->boolean('optimize_web_fonts')->default(true);
            $table->boolean('optimize_javascript')->default(true);
            $table->boolean('delay_ads_analytics_scripts')->default(true);
            $table->boolean('prioritize_speed_over_slider_loading')->default(true);
            $table->boolean('compress_inline_js')->default(true);
            $table->boolean('lazy_load_iframes_youtube')->default(true);
            $table->boolean('lazy_load_vimeo_videos')->default(true);
            $table->boolean('compress_final_html')->default(true);
            $table->string('cache_ttl')->default('origin');
            $table->json('skip_lazy_load_css_patterns')->nullable();
            $table->json('skip_lazy_load_js_patterns')->nullable();
            $table->timestamps();

            $table->foreign('prestashop_store_id')
                ->references('id')
                ->on('prestashop_stores')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestashop_store_optimization_settings');
    }
};
