<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptimizationArtifactVersion extends Model
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
        'device_class',
        'version_number',
        'status',
        'storage_prefix',
        'raw_html_path',
        'optimized_html_path',
        'critical_css_path',
        'used_css_path',
        'raw_html_bytes',
        'optimized_html_bytes',
        'critical_css_bytes',
        'used_css_bytes',
        'raw_html_sha256',
        'optimized_html_sha256',
        'critical_css_sha256',
        'used_css_sha256',
        'meta_json',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta_json' => 'array',
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

    public function cssReports(): HasMany
    {
        return $this->hasMany(OptimizationCssReport::class)->orderByDesc('created_at');
    }
}
