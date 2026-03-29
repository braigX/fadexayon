<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrestashopPageType extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function shopUrls(): HasMany
    {
        return $this->hasMany(PrestashopShopUrl::class, 'page_type_id');
    }

    public function shopProfiles(): HasMany
    {
        return $this->hasMany(PrestashopShopPageTypeProfile::class, 'page_type_id');
    }
}
