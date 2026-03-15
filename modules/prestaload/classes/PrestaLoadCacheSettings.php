<?php
/**
 * Reads and writes module configuration used by the page-cache services.
 */

class PrestaLoadCacheSettings
{
    /**
     * Fifteen days is a reasonable default for anonymous full-page cache entries.
     */
    private const DEFAULT_TTL = 1296000;

    public const CONFIG_ENABLED = 'PRESTALOAD_CACHE_ENABLED';
    public const CONFIG_FONT_OPTIMIZATION_ENABLED = 'PRESTALOAD_FONT_OPTIMIZATION_ENABLED';
    public const CONFIG_CSS_OPTIMIZATION_ENABLED = 'PRESTALOAD_CSS_OPTIMIZATION_ENABLED';
    public const CONFIG_IMAGE_OPTIMIZATION_ENABLED = 'PRESTALOAD_IMAGE_OPTIMIZATION_ENABLED';
    public const CONFIG_IMGPROXY_BASE_URL = 'PRESTALOAD_IMGPROXY_BASE_URL';
    public const CONFIG_IMGPROXY_QUALITY = 'PRESTALOAD_IMGPROXY_QUALITY';
    public const CONFIG_IMGPROXY_KEY = 'PRESTALOAD_IMGPROXY_KEY';
    public const CONFIG_IMGPROXY_SALT = 'PRESTALOAD_IMGPROXY_SALT';
    public const CONFIG_TTL = 'PRESTALOAD_CACHE_TTL';
    public const CONFIG_ALLOWED_CONTROLLERS = 'PRESTALOAD_CACHE_ALLOWED_CONTROLLERS';

    private $moduleName;
    private $modulePath;

    public function __construct($moduleName, $modulePath)
    {
        $this->moduleName = (string) $moduleName;
        $this->modulePath = rtrim((string) $modulePath, '/');
    }

    /**
     * Default values are intentionally conservative for a first full-page cache.
     */
    public function installDefaults()
    {
        return Configuration::updateValue(self::CONFIG_ENABLED, 1)
            && Configuration::updateValue(self::CONFIG_FONT_OPTIMIZATION_ENABLED, 1)
            && Configuration::updateValue(self::CONFIG_CSS_OPTIMIZATION_ENABLED, 0)
            && Configuration::updateValue(self::CONFIG_IMAGE_OPTIMIZATION_ENABLED, 0)
            && Configuration::updateValue(self::CONFIG_IMGPROXY_BASE_URL, 'http://127.0.0.1:8094')
            && Configuration::updateValue(self::CONFIG_IMGPROXY_QUALITY, 82)
            && Configuration::updateValue(self::CONFIG_IMGPROXY_KEY, '')
            && Configuration::updateValue(self::CONFIG_IMGPROXY_SALT, '')
            && Configuration::updateValue(self::CONFIG_TTL, self::DEFAULT_TTL)
            && Configuration::updateValue(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms');
    }

    public function uninstallDefaults()
    {
        return Configuration::deleteByName(self::CONFIG_ENABLED)
            && Configuration::deleteByName(self::CONFIG_FONT_OPTIMIZATION_ENABLED)
            && Configuration::deleteByName(self::CONFIG_CSS_OPTIMIZATION_ENABLED)
            && Configuration::deleteByName(self::CONFIG_IMAGE_OPTIMIZATION_ENABLED)
            && Configuration::deleteByName(self::CONFIG_IMGPROXY_BASE_URL)
            && Configuration::deleteByName(self::CONFIG_IMGPROXY_QUALITY)
            && Configuration::deleteByName(self::CONFIG_IMGPROXY_KEY)
            && Configuration::deleteByName(self::CONFIG_IMGPROXY_SALT)
            && Configuration::deleteByName(self::CONFIG_TTL)
            && Configuration::deleteByName(self::CONFIG_ALLOWED_CONTROLLERS);
    }

    public function isEnabled()
    {
        return (bool) $this->getStoredValue(self::CONFIG_ENABLED, 1);
    }

    public function getTtl()
    {
        return max(60, (int) $this->getStoredValue(self::CONFIG_TTL, self::DEFAULT_TTL));
    }

    public function isFontOptimizationEnabled()
    {
        return (bool) $this->getStoredValue(self::CONFIG_FONT_OPTIMIZATION_ENABLED, 1);
    }

    public function isCssOptimizationEnabled()
    {
        return (bool) $this->getStoredValue(self::CONFIG_CSS_OPTIMIZATION_ENABLED, 0);
    }

    public function isImageOptimizationEnabled()
    {
        return (bool) $this->getStoredValue(self::CONFIG_IMAGE_OPTIMIZATION_ENABLED, 0);
    }

    public function getImgProxyBaseUrl()
    {
        return trim((string) $this->getStoredValue(self::CONFIG_IMGPROXY_BASE_URL, 'http://127.0.0.1:8094'));
    }

    public function getImgProxyQuality()
    {
        return max(30, min(95, (int) $this->getStoredValue(self::CONFIG_IMGPROXY_QUALITY, 82)));
    }

    public function getImgProxyKey()
    {
        return trim((string) $this->getStoredValue(self::CONFIG_IMGPROXY_KEY, ''));
    }

    public function getImgProxySalt()
    {
        return trim((string) $this->getStoredValue(self::CONFIG_IMGPROXY_SALT, ''));
    }

    public function getAllowedControllers()
    {
        $raw = (string) $this->getStoredValue(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms');
        $parts = preg_split('/[\s,]+/', Tools::strtolower($raw), -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(is_array($parts) ? $parts : []));
    }

    public function getCacheDirectory()
    {
        return $this->modulePath . '/cache/pages';
    }

    public function getLogFile()
    {
        return $this->modulePath . '/cache/prestaload-requests.log';
    }

    public function updateFromRequest()
    {
        $enabled = (int) Tools::getValue(self::CONFIG_ENABLED, 0);
        $fontOptimizationEnabled = (int) Tools::getValue(self::CONFIG_FONT_OPTIMIZATION_ENABLED, 0);
        $cssOptimizationEnabled = (int) Tools::getValue(self::CONFIG_CSS_OPTIMIZATION_ENABLED, 0);
        $imageOptimizationEnabled = (int) Tools::getValue(self::CONFIG_IMAGE_OPTIMIZATION_ENABLED, 0);
        $imgProxyBaseUrl = trim((string) Tools::getValue(self::CONFIG_IMGPROXY_BASE_URL, 'http://127.0.0.1:8094'));
        $imgProxyQuality = max(30, min(95, (int) Tools::getValue(self::CONFIG_IMGPROXY_QUALITY, 82)));
        $imgProxyKey = trim((string) Tools::getValue(self::CONFIG_IMGPROXY_KEY, ''));
        $imgProxySalt = trim((string) Tools::getValue(self::CONFIG_IMGPROXY_SALT, ''));
        $ttl = max(60, (int) Tools::getValue(self::CONFIG_TTL, self::DEFAULT_TTL));
        $controllers = trim((string) Tools::getValue(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms'));

        return Configuration::updateValue(self::CONFIG_ENABLED, $enabled)
            && Configuration::updateValue(self::CONFIG_FONT_OPTIMIZATION_ENABLED, $fontOptimizationEnabled)
            && Configuration::updateValue(self::CONFIG_CSS_OPTIMIZATION_ENABLED, $cssOptimizationEnabled)
            && Configuration::updateValue(self::CONFIG_IMAGE_OPTIMIZATION_ENABLED, $imageOptimizationEnabled)
            && Configuration::updateValue(self::CONFIG_IMGPROXY_BASE_URL, $imgProxyBaseUrl)
            && Configuration::updateValue(self::CONFIG_IMGPROXY_QUALITY, $imgProxyQuality)
            && Configuration::updateValue(self::CONFIG_IMGPROXY_KEY, $imgProxyKey)
            && Configuration::updateValue(self::CONFIG_IMGPROXY_SALT, $imgProxySalt)
            && Configuration::updateValue(self::CONFIG_TTL, $ttl)
            && Configuration::updateValue(self::CONFIG_ALLOWED_CONTROLLERS, $controllers);
    }

    public function getFormValues()
    {
        return [
            self::CONFIG_ENABLED => (int) $this->getStoredValue(self::CONFIG_ENABLED, 1),
            self::CONFIG_FONT_OPTIMIZATION_ENABLED => (int) $this->getStoredValue(self::CONFIG_FONT_OPTIMIZATION_ENABLED, 1),
            self::CONFIG_CSS_OPTIMIZATION_ENABLED => (int) $this->getStoredValue(self::CONFIG_CSS_OPTIMIZATION_ENABLED, 0),
            self::CONFIG_IMAGE_OPTIMIZATION_ENABLED => (int) $this->getStoredValue(self::CONFIG_IMAGE_OPTIMIZATION_ENABLED, 0),
            self::CONFIG_IMGPROXY_BASE_URL => (string) $this->getStoredValue(self::CONFIG_IMGPROXY_BASE_URL, 'http://127.0.0.1:8094'),
            self::CONFIG_IMGPROXY_QUALITY => (int) $this->getStoredValue(self::CONFIG_IMGPROXY_QUALITY, 82),
            self::CONFIG_IMGPROXY_KEY => (string) $this->getStoredValue(self::CONFIG_IMGPROXY_KEY, ''),
            self::CONFIG_IMGPROXY_SALT => (string) $this->getStoredValue(self::CONFIG_IMGPROXY_SALT, ''),
            self::CONFIG_TTL => (int) $this->getStoredValue(self::CONFIG_TTL, self::DEFAULT_TTL),
            self::CONFIG_ALLOWED_CONTROLLERS => (string) $this->getStoredValue(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms'),
        ];
    }

    /**
     * Prestashop's Configuration::get does not take the default value as the
     * second argument, so this helper provides an explicit fallback.
     */
    private function getStoredValue($key, $default)
    {
        $value = Configuration::get($key);

        return $value === false ? $default : $value;
    }
}
