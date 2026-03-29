<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptimizationRun extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'optimization_target_id',
        'run_number',
        'trigger_type',
        'status',
        'total_variants',
        'completed_variants',
        'failed_variants',
        'progress_percent',
        'current_variant_label',
        'variants_json',
        'started_at',
        'finished_at',
        'duration_ms',
        'failure_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'variants_json' => 'array',
        ];
    }

    public function optimizationTarget(): BelongsTo
    {
        return $this->belongsTo(OptimizationTarget::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(OptimizationRunStep::class)->orderBy('created_at');
    }

    public function artifactVersions(): HasMany
    {
        return $this->hasMany(OptimizationArtifactVersion::class)->orderByDesc('created_at');
    }

    public function cssReports(): HasMany
    {
        return $this->hasMany(OptimizationCssReport::class)->orderByDesc('created_at');
    }
}
