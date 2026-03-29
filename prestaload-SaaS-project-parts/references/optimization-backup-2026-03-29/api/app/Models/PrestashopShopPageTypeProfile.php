<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrestashopShopPageTypeProfile extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'prestashop_shop_id',
        'page_type_id',
        'status',
        'scan_provider',
        'scan_source_prestashop_shop_url_id',
        'mobile_score',
        'desktop_score',
        'last_scanned_at',
        'last_font_scanned_at',
        'scan_report_json',
        'font_usage_json',
        'last_aggregated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scan_report_json' => 'array',
            'font_usage_json' => 'array',
            'last_scanned_at' => 'datetime',
            'last_font_scanned_at' => 'datetime',
            'last_aggregated_at' => 'datetime',
        ];
    }

    public function prestashopShop(): BelongsTo
    {
        return $this->belongsTo(PrestashopShop::class);
    }

    public function pageType(): BelongsTo
    {
        return $this->belongsTo(PrestashopPageType::class, 'page_type_id');
    }

    public function scanSourceUrl(): BelongsTo
    {
        return $this->belongsTo(PrestashopShopUrl::class, 'scan_source_prestashop_shop_url_id');
    }

    public function urls(): HasMany
    {
        return $this->hasMany(PrestashopShopPageTypeProfileUrl::class, 'profile_id');
    }

    public function cssReports(): HasMany
    {
        return $this->hasMany(PrestashopShopPageTypeCssReport::class, 'profile_id');
    }

    public function cssArtifacts(): HasMany
    {
        return $this->hasMany(PrestashopShopPageTypeCssArtifact::class, 'profile_id');
    }

    public function assetRules(): HasMany
    {
        return $this->hasMany(PrestashopShopPageTypeAssetRule::class, 'profile_id');
    }
}
