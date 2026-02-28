<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class SiteMap
{
    public function __construct($sp_module)
    {
        $this->module = $sp_module;
        $this->context = Context::getContext();
        $this->module->defineSettings();
        $this->type = $this->module->settings['sitemap_type'];
        $this->freq = 'weekly';
    }

    public function getAlldata($shop_ids = null)
    {
        $data = [];
        $shop_ids = $shop_ids ?: $this->module->shopIDs();
        foreach ($shop_ids as $id_shop) {
            $data[$id_shop] = ['shop_name' => $this->module->getShopName($id_shop), 'files' => []];
            foreach ($this->module->af()->getSuffixes('lang', $id_shop, false) as $id_lang) {
                $identifier = $this->getIdentifier($id_lang, $id_shop);
                $data[$id_shop]['files'][$identifier] = $this->getData($identifier);
            }
        }

        return $data;
    }

    public function getData($identifier)
    {
        $file_path = $this->getPath($identifier);
        $cache_id = 'sm-' . $identifier; // cache is used to auto update sitempas once a day when config page is opened
        if (!file_exists($file_path) || !$this->module->af()->cache('get', $cache_id, '', 86400)) {
            $this->updateData($identifier);
            $this->module->af()->cache('save', $cache_id, 1);
        }

        return [
            'path' => str_replace(_PS_ROOT_DIR_ . '/', __PS_BASE_URI__, $file_path),
            'links_num' => $this->getLinksNum($file_path, $this->type),
            'date_mod' => date('Y-m-d H:i:s', filemtime($file_path)),
        ];
    }

    public function updateAll($shop_ids = null)
    {
        foreach ($this->getAlldata($shop_ids) as $shop_data) {
            foreach (array_keys($shop_data['files']) as $identifier) {
                $this->updateData($identifier);
            }
        }
    }

    public function updateData($identifier)
    {
        $this->parseIdentifier($identifier, $id_lang, $id_shop);
        $links = $this->getSitemapLinks($id_lang, $id_shop);
        file_put_contents(
            $this->getPath($identifier),
            $this->prepareSitemapContent($links)
        );
    }

    public function getSitemapLinks($id_lang, $id_shop)
    {
        // make sure $this->base_url used in page links is defined correctly for selected shop
        unset($this->module->base_url);
        $id_shop_orig = $this->context->shop->id;
        $this->context->shop = new Shop($id_shop);
        $page_data_params = [
            'lang_ids' => [$id_lang],
            'shop_ids' => [$id_shop],
            'f' => ['active' => 1],
            'order' => ['by' => 'sp.id_seopage', 'way' => 'ASC'],
        ];
        $links = array_column($this->module->pageData('getAvailableItems', $page_data_params), 'link');
        // restore original shop context
        unset($this->module->base_url);
        $this->context->shop = new Shop($id_shop_orig);

        return $links;
    }

    public function prepareSitemapContent($links)
    {
        if ($this->type == 'xml') {
            $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
            foreach ($links as $link) {
                $content .= '<url>' . PHP_EOL;
                $content .= '<loc><![CDATA[' . $link . ']]></loc>' . PHP_EOL;
                $content .= '<changefreq>' . $this->freq . '</changefreq>' . PHP_EOL;
                // <priority> - a bag of noise: https://twitter.com/methode/status/846796737750712320
                // <lastmod>  - mostly ignored: https://stackoverflow.com/a/31354426
                $content .= '</url>' . PHP_EOL;
            }
            $content .= '</urlset>';
        } else {
            $content = implode("\n", $links);
        }

        return $content;
    }

    public function parseIdentifier($identifier, &$id_lang = 0, &$id_shop = 0)
    {
        $identifier = explode('-', $identifier);
        $id_lang = Language::getIdByIso($identifier[0]);
        $id_shop = $identifier[1];
    }

    public function getIdentifier($id_lang, $id_shop)
    {
        return Language::getIsoById($id_lang) . '-' . $id_shop;
    }

    public function getPath($identifier, $ext = '')
    {
        return _PS_ROOT_DIR_ . '/sp-sitemap-' . $identifier . ($ext ?: '.' . $this->type);
    }

    public function getLinksNum($file_path, $file_type)
    {
        $count_identifier = $file_type == 'xml' ? '<![CDATA[' : "\n";

        return substr_count(Tools::file_get_contents($file_path), $count_identifier);
    }

    public function getLinksForGSitemap($params)
    {
        $links = [];
        if (!empty($this->module->settings['gsitemap_hook']) && isset($params['lang'])) {
            foreach ($this->getSitemapLinks($params['lang']['id_lang'], $this->module->id_shop) as $link) {
                $links[] = ['page' => 'cms', 'link' => $link]; // priority 0.7; check gsitemap:getPriorityPage()
            }
        }

        return $links;
    }

    public function clear($type = false)
    {
        $ret = true;
        foreach (glob($this->getPath('', '*')) as $sitemap_file) {
            $ret &= unlink($sitemap_file);
        }

        return $ret;
    }
}
