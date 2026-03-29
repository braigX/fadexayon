<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestashopShopPageTypeProfileUrl extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'prestashop_shop_url_id',
        'sample_weight',
        'last_analyzed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sample_weight' => 'decimal:4',
            'last_analyzed_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PrestashopShopPageTypeProfile::class, 'profile_id');
    }

    public function prestashopShopUrl(): BelongsTo
    {
        return $this->belongsTo(PrestashopShopUrl::class, 'prestashop_shop_url_id');
    }
}
