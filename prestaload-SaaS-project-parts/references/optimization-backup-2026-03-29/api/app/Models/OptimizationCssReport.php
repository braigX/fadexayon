<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptimizationCssReport extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'optimization_target_id',
        'optimization_run_id',
        'optimization_artifact_version_id',
        'variant_key',
        'variant_label',
        'device_class',
        'final_url',
        'status_code',
        'stylesheet_count',
        'total_css_bytes',
        'total_used_css_bytes',
        'used_ratio',
        'unused_ratio',
        'scroll_height',
        'viewport_height',
        'console_message_count',
        'duration_ms',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'used_ratio' => 'decimal:4',
            'unused_ratio' => 'decimal:4',
        ];
    }

    public function optimizationTarget(): BelongsTo
    {
        return $this->belongsTo(OptimizationTarget::class);
    }

    public function optimizationRun(): BelongsTo
    {
        return $this->belongsTo(OptimizationRun::class);
    }

    public function optimizationArtifactVersion(): BelongsTo
    {
        return $this->belongsTo(OptimizationArtifactVersion::class);
    }

    public function stylesheets(): HasMany
    {
        return $this->hasMany(OptimizationCssReportStylesheet::class)->orderBy('position');
    }
}
