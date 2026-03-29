<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestashopStoreOptimizationSetting extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'prestashop_store_id',
        'css_optimization_enabled',
        'generate_critical_css',
        'defer_safe_stylesheets',
        'minify_css',
        'optimize_web_fonts',
        'optimize_javascript',
        'delay_ads_analytics_scripts',
        'prioritize_speed_over_slider_loading',
        'compress_inline_js',
        'lazy_load_iframes_youtube',
        'lazy_load_vimeo_videos',
        'compress_final_html',
        'cache_ttl',
        'skip_lazy_load_css_patterns',
        'skip_lazy_load_js_patterns',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'css_optimization_enabled' => 'boolean',
            'generate_critical_css' => 'boolean',
            'defer_safe_stylesheets' => 'boolean',
            'minify_css' => 'boolean',
            'optimize_web_fonts' => 'boolean',
            'optimize_javascript' => 'boolean',
            'delay_ads_analytics_scripts' => 'boolean',
            'prioritize_speed_over_slider_loading' => 'boolean',
            'compress_inline_js' => 'boolean',
            'lazy_load_iframes_youtube' => 'boolean',
            'lazy_load_vimeo_videos' => 'boolean',
            'compress_final_html' => 'boolean',
            'skip_lazy_load_css_patterns' => 'array',
            'skip_lazy_load_js_patterns' => 'array',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
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
            'cache_ttl' => 'origin',
            'skip_lazy_load_css_patterns' => [],
            'skip_lazy_load_js_patterns' => [],
        ];
    }

    public function prestashopStore(): BelongsTo
    {
        return $this->belongsTo(PrestashopStore::class);
    }
}
