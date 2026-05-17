<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

/**
 * Front controller: GET /module/prestaload/sites
 *
 * Returns all shops/stores in this PrestaShop installation.
 * Multi-shop: one entry per active shop. Single-shop: one entry.
 *
 * Authentication: X-Prestaload-Key header must equal hash('sha256', stored_api_key).
 */
class PrestaloadSitesModuleFrontController extends ModuleFrontController
{
    public function initContent(): void
    {
        header('Content-Type: application/json');

        if (! $this->verifyServerRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized.']);
            exit;
        }

        $sites = [];

        if (Shop::isFeatureActive()) {
            // Multi-shop installation
            $shops = Shop::getShops(true);
            foreach ($shops as $shop) {
                $shopObj = new Shop((int) $shop['id_shop']);
                $url     = 'https://' . $shopObj->domain_ssl . $shopObj->getBaseURI();

                $sites[] = [
                    'platform_site_id' => (string) $shop['id_shop'],
                    'url'              => rtrim($url, '/'),
                    'name'             => $shop['name'],
                    'metadata'         => [
                        'id_shop_group' => $shop['id_shop_group'],
                        'theme_name'    => $shop['theme_name'] ?? null,
                    ],
                ];
            }
        } else {
            // Single-shop installation
            $context = Context::getContext();
            $baseUrl = $context->shop->getBaseURL(true);

            $sites[] = [
                'platform_site_id' => (string) $context->shop->id,
                'url'              => rtrim($baseUrl, '/'),
                'name'             => Configuration::get('PS_SHOP_NAME'),
                'metadata'         => ['multi_shop' => false],
            ];
        }

        echo json_encode($sites);
        exit;
    }

    /**
     * Validate the X-Prestaload-Key header sent by the Prestaload control plane.
     * The received value must equal hash('sha256', stored plaintext api_key).
     */
    private function verifyServerRequest(): bool
    {
        $receivedHash = $_SERVER['HTTP_X_PRESTALOAD_KEY'] ?? '';
        $settings     = json_decode(Configuration::get(Prestaload::CONFIG_KEY, '{}'), true) ?: [];

        if (empty($settings['api_key']) || empty($receivedHash)) {
            return false;
        }

        return hash_equals(hash('sha256', $settings['api_key']), $receivedHash);
    }
}
