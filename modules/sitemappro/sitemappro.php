<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA    <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(dirname(__FILE__).'/classes/tools/config.php');
class SiteMapPro extends ModuleSMP
{
    public function __construct()
    {
        $this->name = 'sitemappro';
        $this->tab = 'front_office_features';
        $this->version = '2.0.13';
        $this->author = 'SeoSa';
        $this->need_instance = 0;

        $this->tabs = array(
            array(
                'tab' => 'AdminSiteMapPro',
                'parent' => (version_compare(_PS_VERSION_, '1.7', '<')
                    ? 'AdminPreferences' : 'ShopParameters'),
                'name' => array(
                    'en' => 'Sitemap pro',
                    'ru' => 'Карта сайта Pro'
                ),
                'icon' => '',
                'visible' => true
            )
        );

        $this->classes = array(
            'SitemapConfig',
            'UserLink'
        );

        $this->config = array(
            'PROTOCOL' => SitemapConfig::PROTOCOL_HTTPS,
            'ITEM_PER_SITEMAP' => 10000,
            'SYMBOL_LEGEND' => '^',
            'ALLOW_IMAGE_CAPTION_ATTR' => false,
            'EXPORT_IN_ROBOTS' => true,
            'EXPORT_COMBINATION' => false,
            'INCLUDE_ID_IN_ATTRIBUTE' => false,
            'EXPORT_CATEGORY_IMAGE' => true,
            'EXPORT_COMBINATION_DEF' => false
        );

        $this->hooks = array(
            'actionAdminMetaAfterWriteRobotsFile'
        );

        parent::__construct();
        $this->documentation_type = self::DOCUMENTATION_TYPE_SIMPLE;

        $this->displayName = $this->l('Site map pro');
        $this->description = $this->l('Create site map');
        $this->module_key = 'aea7975e9ae63a02758f638ecd70564d';
    }

    public function install($class_name = null)
    {
        $install = parent::install();
        ConfSMP::setConf('SMP_SECRET', md5(uniqid('smp', true)));
        if (isset($_SERVER['HTTPS'])) {
            ConfSMP::setConf('protocol', 'HTTPS');
        } else {
            ConfSMP::setConf('protocol', 'HTTP');
        }
        HelperDbSMP::loadClass($class_name)->createTable(
            'sitemap_category',
            array(
                'id_category' => array(
                    'type' => ObjectModel::TYPE_INT,
                    'validate' => 'isInt'
                ),
                'id_shop' => array(
                    'type' => ObjectModel::TYPE_INT,
                    'validate' => 'isInt'
                )
            )
        );
        return $install;
    }

    public function uninstall($class_name = null)
    {
        $uninstall = parent::uninstall();
        HelperDbSMP::loadClass($class_name)->dropTable(
            'sitemap_category'
        );
        return $uninstall;
    }

    public function hookActionAdminMetaAfterWriteRobotsFile($params)
    {
        $write_fd = $params['write_fd'];

        $sitemaps = ToolsSMP::getSitemaps(Shop::getContextShopID());

        if (ConfSMP::getConf('EXPORT_IN_ROBOTS')) {
            foreach ($sitemaps as $sitemap) {
                if (isset($sitemap['link'])
                && file_exists($sitemap['link']['full_link'])) {
                    fwrite($write_fd, 'User-Agent: *'.PHP_EOL);
                    fwrite($write_fd, 'Sitemap: '.$sitemap['link']['link'].PHP_EOL);
                }
                if (isset($sitemap['lang']) && is_array($sitemap['lang'])) {
                    foreach ($sitemap['lang'] as $item) {
                        if (file_exists($item['full_link'])) {
                            fwrite($write_fd, 'User-Agent: *'.PHP_EOL);
                            fwrite($write_fd, 'Sitemap: '.$item['link'].PHP_EOL);
                        }
                    }
                }
            }
        }
    }
}
