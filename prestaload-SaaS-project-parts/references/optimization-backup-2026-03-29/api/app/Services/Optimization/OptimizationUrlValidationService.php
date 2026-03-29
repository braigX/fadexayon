<?php

namespace App\Services\Optimization;

use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use App\Models\PrestashopStore;

class OptimizationUrlValidationService
{
    /**
     * @return array{
     *   valid: bool,
     *   url_host: string|null,
     *   allowed_hosts: list<string>,
     *   reason: string|null
     * }
     */
    public function validateTrustedUrl(PrestashopStore $store, PrestashopShop $shop, PrestashopShopUrl $shopUrl): array
    {
        $allowedHosts = $this->buildAllowedHosts($store, $shop);
        $urlHost = $this->normalizeHost($shopUrl->canonical_url ?: $shopUrl->url);

        if ($urlHost === null) {
            return [
                'valid' => false,
                'url_host' => null,
                'allowed_hosts' => $allowedHosts,
                'reason' => 'invalid_url_host',
            ];
        }

        if (! in_array($urlHost, $allowedHosts, true)) {
            return [
                'valid' => false,
                'url_host' => $urlHost,
                'allowed_hosts' => $allowedHosts,
                'reason' => 'foreign_domain',
            ];
        }

        return [
            'valid' => true,
            'url_host' => $urlHost,
            'allowed_hosts' => $allowedHosts,
            'reason' => null,
        ];
    }

    /**
     * @param  list<string>  $allowedHosts
     * @return array{valid: bool, host: string|null, reason: string|null}
     */
    public function validateHostAgainstAllowed(?string $candidateUrl, array $allowedHosts): array
    {
        $host = $this->normalizeHost($candidateUrl);

        if ($host === null) {
            return [
                'valid' => false,
                'host' => null,
                'reason' => 'invalid_url_host',
            ];
        }

        if (! in_array($host, $allowedHosts, true)) {
            return [
                'valid' => false,
                'host' => $host,
                'reason' => 'foreign_domain',
            ];
        }

        return [
            'valid' => true,
            'host' => $host,
            'reason' => null,
        ];
    }

    /**
     * @return list<string>
     */
    private function buildAllowedHosts(PrestashopStore $store, PrestashopShop $shop): array
    {
        $hosts = array_filter([
            $this->normalizeHost($store->shop_url),
            $this->normalizeHost($shop->url),
            $this->normalizeHost($shop->domain),
            $this->normalizeHost($shop->domain_ssl),
        ]);

        return array_values(array_unique($hosts));
    }

    private function normalizeHost(?string $candidate): ?string
    {
        $candidate = trim((string) $candidate);
        if ($candidate === '') {
            return null;
        }

        $host = parse_url($candidate, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            $host = parse_url('https://' . ltrim($candidate, '/'), PHP_URL_HOST);
        }

        if (! is_string($host) || $host === '') {
            return null;
        }

        return strtolower($host);
    }
}
