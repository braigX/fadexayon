<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptimizationCssReportStylesheet extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'optimization_css_report_id',
        'position',
        'style_sheet_key',
        'source_url',
        'origin',
        'is_inline',
        'is_disabled',
        'bytes',
        'used_bytes',
        'used_ratio',
        'rule_count',
        'minified_bytes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_inline' => 'boolean',
            'is_disabled' => 'boolean',
            'used_ratio' => 'decimal:4',
        ];
    }

    public function optimizationCssReport(): BelongsTo
    {
        return $this->belongsTo(OptimizationCssReport::class);
    }
}
