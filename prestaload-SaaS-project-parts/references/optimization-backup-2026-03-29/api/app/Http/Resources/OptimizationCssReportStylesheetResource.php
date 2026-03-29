<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptimizationCssReportStylesheetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => (int) $this->position,
            'source_url' => $this->source_url,
            'origin' => $this->origin,
            'is_inline' => (bool) $this->is_inline,
            'is_disabled' => (bool) $this->is_disabled,
            'bytes' => (int) $this->bytes,
            'used_bytes' => (int) $this->used_bytes,
            'used_ratio' => (float) $this->used_ratio,
            'rule_count' => $this->rule_count !== null ? (int) $this->rule_count : null,
            'minified_bytes' => $this->minified_bytes !== null ? (int) $this->minified_bytes : null,
        ];
    }
}
