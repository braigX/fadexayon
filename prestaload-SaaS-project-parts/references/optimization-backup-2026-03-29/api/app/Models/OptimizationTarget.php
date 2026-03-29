<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OptimizationTarget extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'prestashop_store_id',
        'prestashop_shop_id',
        'prestashop_shop_url_id',
        'page_type',
        'normalized_url',
        'device_class',
        'status',
        'current_optimization_run_id',
        'last_error',
    ];

    public function prestashopStore(): BelongsTo
    {
        return $this->belongsTo(PrestashopStore::class);
    }

    public function prestashopShop(): BelongsTo
    {
        return $this->belongsTo(PrestashopShop::class);
    }

    public function prestashopShopUrl(): BelongsTo
    {
        return $this->belongsTo(PrestashopShopUrl::class);
    }

    public function currentOptimizationRun(): BelongsTo
    {
        return $this->belongsTo(OptimizationRun::class, 'current_optimization_run_id');
    }

    public function optimizationRuns(): HasMany
    {
        return $this->hasMany(OptimizationRun::class)->orderByDesc('created_at');
    }

    public function latestOptimizationRun(): HasOne
    {
        return $this->hasOne(OptimizationRun::class)->latestOfMany('created_at');
    }

    public function cssReports(): HasMany
    {
        return $this->hasMany(OptimizationCssReport::class)->orderByDesc('created_at');
    }
}
