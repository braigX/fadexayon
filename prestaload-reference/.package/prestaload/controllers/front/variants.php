<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

/**
 * Front controller: GET /module/prestaload/variants?site=URL
 *
 * Returns cache variant dimensions for a PrestaShop store.
 * V1 prepares guest variants only; logged-in customers must bypass cache.
 */
class PrestaloadVariantsModuleFrontController extends ModuleFrontController
{
    public function initContent(): void
    {
        header('Content-Type: application/json');

        if (! $this->verifyServerRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized.']);
            exit;
        }

        if (! $this->applyShopContext((string) Tools::getValue('site', ''))) {
            http_response_code(404);
            echo json_encode(['error' => 'Requested shop site was not found.']);
            exit;
        }

        $context = Context::getContext();

        echo json_encode([
            'languages' => $this->languageIsoCodes(),
            'currencies' => $this->currencyIsoCodes(),
            'countries' => $this->countryIsoCodes(),
            'shops' => [(string) $context->shop->id],
            'devices' => ['desktop', 'mobile'],
            'auth_states' => ['guest'],
            'default_ttl_seconds' => 86400,
            'metadata' => [
                'platform' => 'prestashop',
                'multi_shop' => Shop::isFeatureActive(),
            ],
        ]);
        exit;
    }

    private function languageIsoCodes(): array
    {
        $context = Context::getContext();
        $languages = Language::getLanguages(true, (int) $context->shop->id, false);

        return array_values(array_unique(array_filter(array_map(static function ($language): string {
            return isset($language['iso_code']) ? (string) $language['iso_code'] : '';
        }, is_array($languages) ? $languages : []))));
    }

    private function currencyIsoCodes(): array
    {
        $context = Context::getContext();
        $currencies = Currency::getCurrenciesByIdShop((int) $context->shop->id);

        return array_values(array_unique(array_filter(array_map(static function ($currency): string {
            if (empty($currency['active']) || ! empty($currency['deleted'])) {
                return '';
            }

            return isset($currency['iso_code']) ? (string) $currency['iso_code'] : '';
        }, is_array($currencies) ? $currencies : []))));
    }

    private function countryIsoCodes(): array
    {
        $context = Context::getContext();

        if (! isset($context->country->iso_code) || $context->country->iso_code === '') {
            return [];
        }

        return [(string) $context->country->iso_code];
    }

    private function applyShopContext(string $siteUrl): bool
    {
        $normalizedUrl = $this->normalizeUrl($siteUrl);

        if ($normalizedUrl === '') {
            return false;
        }

        if (! Shop::isFeatureActive()) {
            return $normalizedUrl === $this->normalizeUrl(Context::getContext()->shop->getBaseURL(true));
        }

        foreach (Shop::getShops(true) as $shop) {
            $shopObj = new Shop((int) $shop['id_shop']);
            $shopUrl = 'https://' . $shopObj->domain_ssl . $shopObj->getBaseURI();

            if ($this->normalizeUrl($shopUrl) === $normalizedUrl) {
                Shop::setContext(Shop::CONTEXT_SHOP, (int) $shop['id_shop']);
                Context::getContext()->shop = $shopObj;

                return true;
            }
        }

        return false;
    }

    private function normalizeUrl(string $url): string
    {
        $parsed = parse_url(strtolower(rtrim($url, '/')));
        $host = $parsed['host'] ?? '';
        $path = $parsed['path'] ?? '';

        return $host . ($path !== '' && $path !== '/' ? rtrim($path, '/') : '');
    }

    private function verifyServerRequest(): bool
    {
        $receivedHash = $_SERVER['HTTP_X_PRESTALOAD_KEY'] ?? '';
        $settings = json_decode(Configuration::get(Prestaload::CONFIG_KEY, '{}'), true) ?: [];

        if (empty($settings['api_key']) || empty($receivedHash)) {
            return false;
        }

        return hash_equals(hash('sha256', $settings['api_key']), $receivedHash);
    }
}
