<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2018 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaWPSitemapModuleFrontController extends ModuleFrontController
{
    public $php_self = '';

    public function init()
    {
        if (!$this->module->enable_posts_page) {
            $controller = Controller::getController('PageNotFoundController');
            $controller->run();
            exit;
        }

        parent::init();

        $pswp = Module::getInstanceByName('prestawp');
        $id_shop = $this->context->shop->id;
        // Cached sitemap xml file:
        $xml_file_path = _PS_MODULE_DIR_ . $pswp->name . '/sitemap-' . (int) $id_shop . '.xml';

        $last_gen = Configuration::get($pswp->settings_prefix . 'SITEMAP_GEN_TIME');
        // Check if it's time to regen the sitemap, cache lifetime 1h:
        if (time() - $last_gen < 3600 && file_exists($xml_file_path)) {
            // Return the cached sitemap
            $sitemap = Tools::file_get_contents($xml_file_path);
        } else {
            // Generate the sitemap:
            $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
            foreach (Language::getLanguages() as $lang) {
                $params = ['lang' => $lang];
                $pages = $pswp->hookGSitemapAppendUrls($params);
                foreach ($pages as $page) {
                    $sitemap .= '<url>' . PHP_EOL;

                    $sitemap .= '<loc><![CDATA[' . $page['link'] . ']]></loc>' . PHP_EOL . '<changefreq>weekly</changefreq>' . PHP_EOL . '<priority>0.7</priority>' . PHP_EOL;

                    $sitemap .= '</url>' . PHP_EOL;
                }
            }
            $sitemap .= '</urlset>' . PHP_EOL;

            // Cache it:
            @file_put_contents($xml_file_path, $sitemap);
            Configuration::updateValue($pswp->settings_prefix . 'SITEMAP_GEN_TIME', time());
        }

        header('Content-Type: text/xml; charset=utf-8');
        exit($sitemap);
    }
}
