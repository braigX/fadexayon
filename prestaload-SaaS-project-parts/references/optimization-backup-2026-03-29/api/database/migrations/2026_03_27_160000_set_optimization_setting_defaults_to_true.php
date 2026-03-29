<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_store_optimization_settings', function (Blueprint $table): void {
            $table->boolean('css_optimization_enabled')->default(true)->change();
            $table->boolean('generate_critical_css')->default(true)->change();
            $table->boolean('defer_safe_stylesheets')->default(true)->change();
            $table->boolean('minify_css')->default(true)->change();
            $table->boolean('optimize_web_fonts')->default(true)->change();
            $table->boolean('optimize_javascript')->default(true)->change();
            $table->boolean('delay_ads_analytics_scripts')->default(true)->change();
            $table->boolean('prioritize_speed_over_slider_loading')->default(true)->change();
            $table->boolean('compress_inline_js')->default(true)->change();
            $table->boolean('lazy_load_iframes_youtube')->default(true)->change();
            $table->boolean('lazy_load_vimeo_videos')->default(true)->change();
            $table->boolean('compress_final_html')->default(true)->change();
        });

        DB::table('prestashop_store_optimization_settings')->update([
            'css_optimization_enabled' => true,
            'generate_critical_css' => true,
            'defer_safe_stylesheets' => true,
            'minify_css' => true,
            'optimize_web_fonts' => true,
            'optimize_javascript' => true,
            'delay_ads_analytics_scripts' => true,
            'prioritize_speed_over_slider_loading' => true,
            'compress_inline_js' => true,
            'lazy_load_iframes_youtube' => true,
            'lazy_load_vimeo_videos' => true,
            'compress_final_html' => true,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('prestashop_store_optimization_settings', function (Blueprint $table): void {
            $table->boolean('css_optimization_enabled')->default(true)->change();
            $table->boolean('generate_critical_css')->default(true)->change();
            $table->boolean('defer_safe_stylesheets')->default(true)->change();
            $table->boolean('minify_css')->default(false)->change();
            $table->boolean('optimize_web_fonts')->default(false)->change();
            $table->boolean('optimize_javascript')->default(false)->change();
            $table->boolean('delay_ads_analytics_scripts')->default(false)->change();
            $table->boolean('prioritize_speed_over_slider_loading')->default(false)->change();
            $table->boolean('compress_inline_js')->default(false)->change();
            $table->boolean('lazy_load_iframes_youtube')->default(false)->change();
            $table->boolean('lazy_load_vimeo_videos')->default(false)->change();
            $table->boolean('compress_final_html')->default(false)->change();
        });
    }
};
