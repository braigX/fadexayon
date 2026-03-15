<?php
/**
 * Writes a small PHP config file that can be loaded by the early bootstrap
 * before Prestashop starts. This avoids querying Prestashop configuration
 * inside the shop root index.php.
 */

class PrestaLoadRuntimeConfig
{
    private $settings;
    private $modulePath;

    public function __construct(PrestaLoadCacheSettings $settings, $modulePath)
    {
        $this->settings = $settings;
        $this->modulePath = rtrim((string) $modulePath, '/');
    }

    /**
     * The early bootstrap only needs a tiny subset of settings.
     */
    public function write()
    {
        $path = $this->getPath();
        $directory = dirname($path);

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $payload = [
            'enabled' => $this->settings->isEnabled(),
            'cache_directory' => $this->settings->getCacheDirectory(),
            'allowed_controllers' => $this->settings->getAllowedControllers(),
            'written_at' => time(),
        ];

        $export = "<?php\nreturn " . var_export($payload, true) . ";\n";

        return @file_put_contents($path, $export, LOCK_EX) !== false;
    }

    public function delete()
    {
        $path = $this->getPath();

        return !is_file($path) || @unlink($path);
    }

    public function getPath()
    {
        return $this->modulePath . '/cache/runtime-config.php';
    }
}
