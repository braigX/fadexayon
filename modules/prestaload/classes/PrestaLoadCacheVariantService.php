<?php

class PrestaLoadCacheVariantService
{
    private const CACHE_TTL_SECONDS = 86400;

    /**
     * @var Prestaload
     */
    private $module;

    public function __construct(Prestaload $module)
    {
        $this->module = $module;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function describe(array $payload)
    {
        $shopId = isset($payload['shop_id']) ? (int) $payload['shop_id'] : 0;
        if ($shopId <= 0) {
            $shopId = (int) $this->module->getCurrentShopId();
        }

        $cached = $this->loadCachedDescription($shopId);
        if ($cached !== null) {
            $cached['cache_hit'] = true;

            return $cached;
        }

        $languages = $this->getLanguages($shopId);
        $currencies = $this->getCurrencies($shopId);
        $devices = ['desktop', 'mobile'];
        $loginStates = ['guest'];
        $themeHash = $this->getThemeHash($shopId);

        $variants = [];
        foreach ($languages as $languageIso) {
            foreach ($currencies as $currencyIso) {
                foreach ($devices as $deviceClass) {
                    foreach ($loginStates as $loginState) {
                        $variant = [
                            'currency_iso' => $currencyIso,
                            'device_class' => $deviceClass,
                            'language_iso' => $languageIso,
                            'login_state' => $loginState,
                            'shop_id' => $shopId,
                            'theme_hash' => $themeHash,
                        ];

                        $variants[] = [
                            'label' => implode(' / ', [
                                $languageIso,
                                $currencyIso,
                                $deviceClass,
                                $loginState,
                            ]),
                            'variant' => $variant,
                        ];
                    }
                }
            }
        }

        $result = [
            'shop_id' => $shopId,
            'dimensions' => [
                'languages' => $languages,
                'currencies' => $currencies,
                'devices' => $devices,
                'login_states' => $loginStates,
                'theme_hash' => $themeHash,
            ],
            'variants_count' => count($variants),
            'variants' => $variants,
            'cache_hit' => false,
            'generated_at' => date('c'),
            'expires_at' => date('c', time() + self::CACHE_TTL_SECONDS),
        ];

        $this->storeCachedDescription($shopId, $result);

        return $result;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function purgeForShop(array $payload)
    {
        $shopId = isset($payload['shop_id']) ? (int) $payload['shop_id'] : 0;
        if ($shopId <= 0) {
            $shopId = (int) $this->module->getCurrentShopId();
        }

        $path = $this->variantCachePath($shopId);
        $deleted = false;

        if (is_file($path)) {
            $deleted = @unlink($path);
        }

        return [
            'shop_id' => $shopId,
            'deleted' => $deleted,
            'path' => $path,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function getLanguages($shopId)
    {
        $rows = Language::getLanguages(true, $shopId);
        $languages = [];

        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!is_array($row) || empty($row['iso_code'])) {
                    continue;
                }

                $languages[] = strtoupper((string) $row['iso_code']);
            }
        }

        $languages = array_values(array_unique(array_filter($languages)));

        if ($languages === []) {
            $languages[] = $this->getDefaultLanguageIso($shopId);
        }

        sort($languages);

        return $languages;
    }

    /**
     * @return array<int, string>
     */
    private function getCurrencies($shopId)
    {
        $rows = Currency::getCurrenciesByIdShop($shopId);
        $currencies = [];

        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!is_array($row) || empty($row['iso_code'])) {
                    continue;
                }

                $currencies[] = strtoupper((string) $row['iso_code']);
            }
        }

        $currencies = array_values(array_unique(array_filter($currencies)));

        if ($currencies === []) {
            $currencies[] = $this->getDefaultCurrencyIso($shopId);
        }

        sort($currencies);

        return $currencies;
    }

    private function getDefaultLanguageIso($shopId)
    {
        $languageId = (int) Configuration::get('PS_LANG_DEFAULT', null, null, $shopId > 0 ? $shopId : null);
        if ($languageId <= 0) {
            return 'EN';
        }

        $language = new Language($languageId);
        if (!Validate::isLoadedObject($language)) {
            return 'EN';
        }

        return strtoupper((string) $language->iso_code);
    }

    private function getDefaultCurrencyIso($shopId)
    {
        $currencyId = (int) Configuration::get('PS_CURRENCY_DEFAULT', null, null, $shopId > 0 ? $shopId : null);
        if ($currencyId <= 0) {
            return 'EUR';
        }

        $currency = new Currency($currencyId);
        if (!Validate::isLoadedObject($currency)) {
            return 'EUR';
        }

        return strtoupper((string) $currency->iso_code);
    }

    private function getThemeHash($shopId)
    {
        $themeName = (string) Configuration::get('PS_THEME_NAME', null, null, $shopId > 0 ? $shopId : null);

        if ($themeName === '') {
            $context = Context::getContext();
            if (isset($context->shop) && method_exists($context->shop, 'getTheme')) {
                $theme = $context->shop->getTheme();
                if (is_object($theme) && is_callable([$theme, 'getName'])) {
                    $themeName = (string) $theme->getName();
                } elseif (is_object($theme) && isset($theme->name)) {
                    $themeName = (string) $theme->name;
                }
            }
        }

        return sha1($themeName !== '' ? $themeName : 'default-theme');
    }

    private function loadCachedDescription($shopId)
    {
        $path = $this->variantCachePath($shopId);
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $raw = @file_get_contents($path);
        if (!is_string($raw) || $raw === '') {
            return null;
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            return null;
        }

        $expiresAt = isset($payload['expires_at']) ? strtotime((string) $payload['expires_at']) : false;
        if ($expiresAt === false || $expiresAt < time()) {
            @unlink($path);

            return null;
        }

        return $payload;
    }

    private function storeCachedDescription($shopId, array $payload)
    {
        $directory = $this->variantCacheDirectory();
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $encoded = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            return;
        }

        @file_put_contents($this->variantCachePath($shopId), $encoded, LOCK_EX);
    }

    private function variantCacheDirectory()
    {
        return rtrim($this->module->getModuleLocalPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'variants';
    }

    private function variantCachePath($shopId)
    {
        return $this->variantCacheDirectory() . DIRECTORY_SEPARATOR . 'shop-' . (int) $shopId . '.json';
    }
}
