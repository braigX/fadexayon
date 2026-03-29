<?php

namespace App\Services\Optimization;

use App\Models\PrestashopStore;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ModuleCacheService
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function prepareCache(PrestashopStore $store, array $payload): array
    {
        return $this->sendSignedJsonRequest($store, '/module/prestaload/cacheprepare', $payload, 'cache_prepare');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function getCacheVariants(PrestashopStore $store, array $payload): array
    {
        $shopId = (int) ($payload['shop_id'] ?? 0);
        if ($shopId <= 0) {
            return $this->sendSignedJsonRequest($store, '/module/prestaload/cachevariants', $payload, 'cache_variants');
        }

        return Cache::remember(
            $this->cacheVariantsKey($store, $shopId),
            now()->addDay(),
            fn (): array => $this->sendSignedJsonRequest($store, '/module/prestaload/cachevariants', $payload, 'cache_variants')
        );
    }

    public function forgetCacheVariants(PrestashopStore $store, int $shopId): void
    {
        if ($shopId <= 0) {
            return;
        }

        Cache::forget($this->cacheVariantsKey($store, $shopId));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function publishCache(PrestashopStore $store, array $payload): array
    {
        return $this->sendSignedJsonRequest($store, '/module/prestaload/cachepublish', $payload, 'cache_publish');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function purgeCache(PrestashopStore $store, array $payload): array
    {
        return $this->sendSignedJsonRequest($store, '/module/prestaload/cachepurge', $payload, 'cache_purge');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function purgeAllCache(PrestashopStore $store, array $payload): array
    {
        return $this->sendSignedJsonRequest($store, '/module/prestaload/cachepurgeall', $payload, 'cache_purge_all');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function sendSignedJsonRequest(PrestashopStore $store, string $path, array $payload, string $stage): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($body === false) {
            throw new RuntimeException('Failed to encode ' . $stage . ' payload.');
        }

        $timestamp = time();
        $signedPayload = implode("\n", [
            $timestamp,
            'POST',
            $path,
            hash('sha256', $body),
        ]);
        $signature = hash_hmac('sha256', $signedPayload, $store->getSharedSecret());

        $endpoint = rtrim((string) $store->shop_url, '/') . $path;
        $response = Http::timeout(90)
            ->acceptJson()
            ->withOptions([
                'verify' => $this->shouldVerifySsl($endpoint),
            ])
            ->withHeaders([
                'X-PrestaBoost-Store' => $store->id,
                'X-PrestaBoost-Timestamp' => (string) $timestamp,
                'X-PrestaBoost-Signature' => $signature,
            ])
            ->withBody($body, 'application/json')
            ->post($endpoint);

        $json = $response->json();

        if (! $response->successful()) {
            throw new RuntimeException(ucwords(str_replace('_', ' ', $stage)) . ' request failed with HTTP ' . $response->status() . '.');
        }

        if (! is_array($json) || ! ($json['success'] ?? false)) {
            throw new RuntimeException(ucwords(str_replace('_', ' ', $stage)) . ' returned an invalid payload.');
        }

        return $json;
    }

    private function shouldVerifySsl(string $endpoint): bool
    {
        $host = parse_url($endpoint, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return true;
        }

        if ($host === 'localhost' || $host === '127.0.0.1' || str_ends_with($host, '.local.test')) {
            return false;
        }

        return true;
    }

    private function cacheVariantsKey(PrestashopStore $store, int $shopId): string
    {
        return 'prestaload:cache_variants:store:' . $store->id . ':shop:' . $shopId;
    }

}
