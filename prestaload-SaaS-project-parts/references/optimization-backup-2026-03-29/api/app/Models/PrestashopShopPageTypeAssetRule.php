<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestashopShopPageTypeAssetRule extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'device_class',
        'asset_type',
        'asset_url',
        'asset_pattern',
        'recommended_action',
        'effective_action',
        'reduced_css_status',
        'minified_css_status',
        'action_source',
        'confidence',
        'reasons_json',
        'evidence_json',
        'last_verified_at',
        'last_reduced_at',
        'last_minified_at',
        'reduced_css_public_path',
        'reduced_css_public_url',
        'reduced_css_bytes',
        'reduced_css_sha256',
        'minified_css_public_path',
        'minified_css_public_url',
        'minified_css_asset_bytes',
        'minified_css_sha256',
        'font_asset_status',
        'font_css_public_path',
        'font_css_public_url',
        'font_css_bytes',
        'font_css_sha256',
        'font_meta_json',
        'last_font_built_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:4',
            'reasons_json' => 'array',
            'evidence_json' => 'array',
            'font_meta_json' => 'array',
            'last_verified_at' => 'datetime',
            'last_reduced_at' => 'datetime',
            'last_minified_at' => 'datetime',
            'last_font_built_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PrestashopShopPageTypeProfile::class, 'profile_id');
    }
}
