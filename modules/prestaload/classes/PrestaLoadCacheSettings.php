<?php
/**
 * Reads and writes module configuration used by the page-cache services.
 */

class PrestaLoadCacheSettings
{
    public const CONFIG_ENABLED = 'PRESTALOAD_CACHE_ENABLED';
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
            && Configuration::updateValue(self::CONFIG_TTL, 300)
            && Configuration::updateValue(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms');
    }

    public function uninstallDefaults()
    {
        return Configuration::deleteByName(self::CONFIG_ENABLED)
            && Configuration::deleteByName(self::CONFIG_TTL)
            && Configuration::deleteByName(self::CONFIG_ALLOWED_CONTROLLERS);
    }

    public function isEnabled()
    {
        return (bool) Configuration::get(self::CONFIG_ENABLED, 1);
    }

    public function getTtl()
    {
        return max(60, (int) Configuration::get(self::CONFIG_TTL, 300));
    }

    public function getAllowedControllers()
    {
        $raw = (string) Configuration::get(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms');
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
        $ttl = max(60, (int) Tools::getValue(self::CONFIG_TTL, 300));
        $controllers = trim((string) Tools::getValue(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms'));

        return Configuration::updateValue(self::CONFIG_ENABLED, $enabled)
            && Configuration::updateValue(self::CONFIG_TTL, $ttl)
            && Configuration::updateValue(self::CONFIG_ALLOWED_CONTROLLERS, $controllers);
    }

    public function getFormValues()
    {
        return [
            self::CONFIG_ENABLED => (int) Configuration::get(self::CONFIG_ENABLED, 1),
            self::CONFIG_TTL => (int) Configuration::get(self::CONFIG_TTL, 300),
            self::CONFIG_ALLOWED_CONTROLLERS => (string) Configuration::get(self::CONFIG_ALLOWED_CONTROLLERS, 'index,category,product,cms'),
        ];
    }
}
