<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrestashopShopPageTypeCssReport extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'source_optimization_css_report_id',
        'device_class',
        'sample_count',
        'stylesheet_count',
        'total_css_bytes',
        'total_used_css_bytes',
        'used_ratio',
        'unused_ratio',
        'coverage_json',
        'last_compiled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'used_ratio' => 'decimal:4',
            'unused_ratio' => 'decimal:4',
            'coverage_json' => 'array',
            'last_compiled_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PrestashopShopPageTypeProfile::class, 'profile_id');
    }

    public function sourceOptimizationCssReport(): BelongsTo
    {
        return $this->belongsTo(OptimizationCssReport::class, 'source_optimization_css_report_id');
    }

    public function stylesheets(): HasMany
    {
        return $this->hasMany(PrestashopShopPageTypeCssReportStylesheet::class, 'shop_page_type_css_report_id')
            ->orderBy('position');
    }
}
