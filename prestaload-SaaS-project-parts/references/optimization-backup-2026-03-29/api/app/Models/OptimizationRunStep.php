<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptimizationRunStep extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'optimization_run_id',
        'step_name',
        'worker_type',
        'status',
        'queue_name',
        'started_at',
        'finished_at',
        'duration_ms',
        'input_summary_json',
        'output_summary_json',
        'error_summary',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'input_summary_json' => 'array',
            'output_summary_json' => 'array',
        ];
    }

    public function optimizationRun(): BelongsTo
    {
        return $this->belongsTo(OptimizationRun::class);
    }
}
