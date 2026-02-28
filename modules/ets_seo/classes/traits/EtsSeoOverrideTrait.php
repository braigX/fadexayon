<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Trait EtsSeoOverrideTrait
 */
trait EtsSeoOverrideTrait
{
    /**
     * Exclude host name from renaming Prestashop Twig override directory
     *
     * @var string[]
     */
    private $_excludedHosts = [
        'localhost',
        '*.zuko.pro',
        '*.presta-demos.com',
        '*.test',
    ];

    /**
     * Check if host is exclude
     *
     * @param string $host
     *
     * @return bool
     */
    private function isExcludedHost($host)
    {
        $rs = false;
        if (!class_exists('\EtsSeoStrHelper')) {
            require_once __DIR__ . '/../utils/EtsSeoStrHelper.php';
        }
        foreach ($this->_excludedHosts as $excludedHost) {
            if (EtsSeoStrHelper::contains($excludedHost, '*.')) {
                $excludedHost = explode('*.', $excludedHost)[1];
                if (false !== EtsSeoStrHelper::endsWith($host, $excludedHost)) {
                    $rs = true;
                }
            } elseif ($host === $excludedHost) {
                $rs = true;
            }
            if ($rs) {
                return true;
            }
        }

        return false;
    }

    public static function updateOldFeatureFlag()
    {
        if ($state = Db::getInstance()->getValue('SELECT state FROM `' . _DB_PREFIX_ . 'feature_flag` WHERE name="product_page_v2"')) {
            Configuration::updateGlobalValue('ETS_SEO_IS_REDIRECT_FROM_PRODUCT_PAGE_V2_TO_V1', $state);
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'feature_flag` SET state=0 WHERE name ="product_page_v2"');
        }
    }

    public static function updateNewFeatureFlag()
    {
        if (Configuration::getGlobalValue('ETS_SEO_IS_REDIRECT_FROM_PRODUCT_PAGE_V2_TO_V1')) {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'feature_flag` SET state=1 WHERE name ="product_page_v2"');
            Configuration::updateGlobalValue('ETS_SEO_IS_REDIRECT_FROM_PRODUCT_PAGE_V2_TO_V1', 0);
        }
    }

    public static function updateNewProduct($id_product)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product` SET state=1 WHERE id_product=' . (int) $id_product);
    }

    /**
     * @return bool
     * 812
     */
    private function copySymfonyServices()
    {
        if(version_compare(_PS_VERSION_, '9.0', '>='))
            return true;
        $dir = __DIR__ . '/../..';
        if (version_compare(_PS_VERSION_, '8.0', '>=') && !@file_exists($dir . '/config/services.yml')) {
            $fileName = version_compare(_PS_VERSION_, '8.1.2', '>=') ? 'services__.yml':'services811.yml';
            if (function_exists('\symlink')) {
                if (!@symlink($dir . '/config/'.$fileName, $dir . '/config/services.yml') || !@readlink($dir . '/config/services.yml')) {
                    Tools::copy($dir . '/config/'.$fileName, $dir . '/config/services.yml');
                }
            } else {
                Tools::copy($dir . '/config/'.$fileName, $dir . '/config/services.yml');
            }

            return true;
        }

        if (version_compare(_PS_VERSION_, '8.0', '<')) {
            @file_exists($dir . '/config/services.yml') && @unlink($dir . '/config/services.yml');

            return true;
        }

        return true;
    }

    /**
     * @param string $path
     * @param int $permission
     *
     * @return bool
     *
     * @throws \PrestaShopException
     */
    private function safeMkDir($path, $permission = 0755)
    {
        if (!@mkdir($concurrentDirectory = $path, $permission) && !is_dir($concurrentDirectory)) {
            throw new \PrestaShopException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return true;
    }

    /**
     * @throws \PrestaShopException
     */
    private function checkOverrideDir()
    {
        if (defined('_PS_OVERRIDE_DIR_')) {
            $psOverride = @realpath(_PS_OVERRIDE_DIR_) . DIRECTORY_SEPARATOR;
            if (!is_dir($psOverride)) {
                $this->safeMkDir($psOverride);
            }
            $base = str_replace('/', DIRECTORY_SEPARATOR, $this->getLocalPath() . 'override');
            $iterator = new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS);
            /** @var RecursiveIteratorIterator|\SplFileInfo[] $iterator */
            $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
            $iterator->setMaxDepth(4);
            foreach ($iterator as $k => $item) {
                if (!$item->isDir()) {
                    continue;
                }
                $path = str_replace($base . DIRECTORY_SEPARATOR, '', $item->getPathname());
                if (!@file_exists($psOverride . $path)) {
                    $this->safeMkDir($psOverride . $path);
                    @touch($psOverride . $path . DIRECTORY_SEPARATOR . '_do_not_remove');
                }
            }
            if (!file_exists($psOverride . 'index.php')) {
                Tools::copy($this->getLocalPath() . 'index.php', $psOverride . 'index.php');
            }
        }
    }

    /**
     * _installOverried.
     *
     * @return bool
     */
    public function _installOverried()
    {
        if(version_compare(_PS_VERSION_, '9.0', '>='))
            return true;
        $this->copyTplOverrides($this->getLocalPath() . 'views/templates/admin/_configure/templates', _PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        @rename($this->getLocalPath() . 'views/PrestaShop___', $this->getLocalPath() . 'views/PrestaShop');

        return true;
    }

    /**
     * _unInstallOverried.
     *
     * @return bool
     */
    public function _uninstallOverried()
    {
        if(version_compare(_PS_VERSION_, '9.0', '>='))
            return true;
        $this->deleteTplOverrides(_PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        if (!$this->isExcludedHost(Tools::getHttpHost(false, false, true))) {
            if (file_exists($this->getLocalPath() . 'views/PrestaShop___') && file_exists($this->getLocalPath() . 'views/PrestaShop')) {
                if (is_dir($this->getLocalPath() . 'views/PrestaShop___')) {
                    $this->recursiveDelete($this->getLocalPath() . 'views/PrestaShop___');
                } else {
                    @unlink($this->getLocalPath() . 'views/PrestaShop___');
                }
            }
            @rename($this->getLocalPath() . 'views/PrestaShop', $this->getLocalPath() . 'views/PrestaShop___');
        }

        return true;
    }

    /**
     * Recursive delete directory
     *
     * @param string $path
     */
    private function recursiveDelete($path)
    {
        if (@file_exists($path)) {
            /** @var \RecursiveDirectoryIterator|\SplFileInfo[] $iterator */
            $iterator = new \RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
            foreach ($iterator as $info) {
                if ($info->isDir()) {
                    $this->recursiveDelete($info->getRealPath());
                }
                if ($info->isFile()) {
                    @unlink($info->getRealPath());
                }
            }
            @is_dir($path) && @rmdir($path);
        }
    }

    /**
     * copy_directory.
     *
     * @param string $src
     * @param string $dst
     *
     * @return void
     */
    public function copyTplOverrides($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (('.' != $file) && ('..' != $file)) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyTplOverrides($src . '/' . $file, $dst . '/' . $file);
                } else {
                    if (file_exists($dst . '/' . $file) && 'index.php' != $file && ($content = Tools::file_get_contents($dst . '/' . $file)) && false === Tools::strpos($content, 'overried_by_hinh_ets')) {
                        copy($dst . '/' . $file, $dst . '/backup_' . $file);
                    }
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * delete_directory.
     *
     * @param string $directory
     *
     * @return void
     */
    public function deleteTplOverrides($directory)
    {
        $dir = opendir($directory);
        while (false !== ($file = readdir($dir))) {
            if (('.' != $file) && ('..' != $file)) {
                if (is_dir($directory . '/' . $file)) {
                    $this->deleteTplOverrides($directory . '/' . $file);
                } else {
                    if (file_exists($directory . '/' . $file) && 'index.php' != $file && ($content = Tools::file_get_contents($directory . '/' . $file)) && false !== Tools::strpos($content, 'overried_by_hinh_ets')) {
                        @unlink($directory . '/' . $file);
                        if (file_exists($directory . '/backup_' . $file)) {
                            copy($directory . '/backup_' . $file, $directory . '/' . $file);
                        }
                    }
                }
            }
        }
        closedir($dir);
    }

    public function copyAllFiles(&$copy_trans, $path, $cutPath)
    {
        if ($files = glob($path . '/*')) {
            foreach ($files as $file) {
                if (!@is_dir($file) && 'index' != basename($file, '.php')) {
                    $copy_trans[] = str_replace($cutPath, '', $file);
                } else {
                    $this->copyAllFiles($copy_trans, $file, $cutPath);
                }
            }
            unset($files);
        }
    }

    public function copyTranslations()
    {
        $ps_translations_dir = _PS_ROOT_DIR_ . '/app/Resources/translations/';
        $tempDir = $this->getLocalPath() . 'views/templates/admin/_configure/templates/';
        $copy_trans = [];
        $this->copyAllFiles($copy_trans, $tempDir, $tempDir);

        if (($languages = Language::getLanguages(false)) && $copy_trans) {
            foreach ($languages as $language) {
                if (!@file_exists($trans_file = $this->getLocalPath() . 'translations/' . $language['iso_code'] . '.php')) {
                    @file_put_contents($trans_file, "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n");
                }
                if (!is_writable($trans_file)) {
                    $this->displayWarning($this->l('This file must be writable:') . $trans_file);
                }

                $str_write = Tools::file_get_contents($trans_file);
                $_MODULE = [];
                include $trans_file;

                if ($order_trans = @glob($ps_translations_dir . $language['locale'] . DIRECTORY_SEPARATOR . '*.' . $language['locale'] . '.xlf')) {
                    foreach ($order_trans as $trans) {
                        if (($dataXML = @simplexml_load_string(Tools::file_get_contents($trans))) && !empty($dataXML->file)) {
                            foreach ($dataXML->file as $file) {
                                if ($this->fileInArray((string) $file['original'], $copy_trans) && ($array_trans = (array) $file->body) && !empty($array_trans['trans-unit'])) {
                                    foreach ((array) $array_trans['trans-unit'] as $trans_unit) {
                                        if (!empty($trans_unit['id']) && isset($trans_unit->target)) {
                                            $keyMd5 = '<{' . $this->name . '}prestashop>' . basename((string) $file['original'], '.tpl') . '_' . (string) $trans_unit['id'];
                                            $str_write .= "\$_MODULE['" . $keyMd5 . "'] = '" . pSQL($trans_unit->target) . "';\n";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                @file_put_contents($trans_file, $str_write);
            }
        }

        return true;
    }

    public function fileInArray($original, $copy_trans)
    {
        if (empty($copy_trans)) {
            return false;
        }
        foreach ($copy_trans as $file) {
            if (false !== @strpos($original, $file)) {
                return true;
            }
        }

        return false;
    }

    public function setSitemap()
    {
        $robots ='';
        if ((int) Configuration::get('ETS_SEO_ENABLE_XML_SITEMAP')) {
            if (@file_exists(_PS_ROOT_DIR_ . '/robots.txt')) {
                @rename(_PS_ROOT_DIR_ . '/robots.txt', _PS_ROOT_DIR_ . '/_robots.txt');
                $path = _PS_ROOT_DIR_ . '/_robots.txt';
                if (@file_exists($path) && @is_writable($path)) {
                    $robots = trim(Tools::file_get_contents($path));
                }
            }
        }
        $path = _PS_ROOT_DIR_ . '/robots.txt';
        if (!$robots && @file_exists($path) && @is_writable($path)) {
            $robots = trim(Tools::file_get_contents($path));
        }
        $robots = str_replace("\r\n", "\n", $robots);
        $robots = preg_replace('/^(Sitemap: .+index_sitemap.xml)$/im', '#$1', $robots);
        if ($shops = Shop::getShops(false)) {
            $link = new Link();
            foreach ($shops as $shop) {
                $shopId = (int) $shop['id_shop'];
                $shopObject = new Shop($shopId);
                $legacyUrl = $shopObject->getBaseURL(true, true) . 'sitemap.xml';
                $robots = preg_replace(
                    '/^Sitemap:\s*' . preg_quote($legacyUrl, '/') . '$/im',
                    '',
                    $robots
                );
                $shopGroupId = (int) Shop::getGroupFromShop($shopId, true);
                $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT', null, $shopGroupId, $shopId);
                if (!$defaultLangId) {
                    $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
                }
                $sitemapUrl = $link->getModuleLink(
                    'ets_seo',
                    'sitemap',
                    [],
                    true,
                    $defaultLangId,
                    $shopId
                );
                if ($sitemapUrl && !preg_match('/^Sitemap:\s*' . preg_quote($sitemapUrl, '/') . '$/im', $robots)) {
                    $robots .= "\nSitemap: " . $sitemapUrl;
                }
            }
        }
        @file_put_contents($path, $robots);

        return true;
    }

    public function removeSitemap()
    {
        if (@file_exists(_PS_ROOT_DIR_ . '/_robots.txt')) {
            if (@file_exists(_PS_ROOT_DIR_ . '/robots.txt')) {
                @unlink(_PS_ROOT_DIR_ . '/robots.txt');
            }
            @rename(_PS_ROOT_DIR_ . '/_robots.txt', _PS_ROOT_DIR_ . '/robots.txt');
        }
        $path = _PS_ROOT_DIR_ . '/robots.txt';
        if (@file_exists($path) && @is_writable($path) && ($robots = Tools::file_get_contents($path))) {
            $robots = str_replace("\r\n", "\n", $robots);
            $robots = preg_replace('/^Sitemap: .+\/sitemap.xml$/im', '', $robots);
            $robots = preg_replace('/^#(Sitemap: .+index_sitemap.xml)$/im', '$1', $robots);
            @file_put_contents($path, $robots);
        }

        return true;
    }
}
