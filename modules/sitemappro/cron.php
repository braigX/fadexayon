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
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(dirname(__FILE__).'/../../config/config.inc.php');
$request_lang = Tools::getValue('id_lang');
$old_value = Configuration::get('PS_SHOP_ENABLE');
Configuration::updateValue('PS_SHOP_ENABLE', 1);
require_once(dirname(__FILE__).'/../../init.php');
Configuration::updateValue('PS_SHOP_ENABLE', $old_value);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(dirname(__FILE__).'/classes/tools/config.php');

if (ConfSMP::getConf('SMP_PROTECT')) {
    if (ConfSMP::getConf('SMP_SECRET') != Tools::getValue('secret')) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Forbidden';
        exit;
    }
}

$id_shop = (int)Tools::getValue('id_shop');
if (!$id_shop) {
    $id_shop = Context::getContext()->shop->id;
}

$shops = array($id_shop);

if (ToolsModuleSMP::isPHPCLI() && !$id_shop) {
    $shops = Shop::getShops(true, null, true);
}

foreach ($shops as $shop_id) {
    $shop = new Shop((int)$shop_id);
    $id_lang = null;
    if ($request_lang) {
        $id_lang = (int)$request_lang;
    }
    $with_image  = (int)Tools::getValue('with_image');
    $with_link  = (int)Tools::getValue('with_link');

    $context = Context::getContext();
    if (Validate::isLoadedObject($shop)) {
        $context->shop = $shop;
        Shop::setContext(Shop::CONTEXT_SHOP, $shop->id);
    }
    $_SERVER['HTTP_X_FORWARDED_HOST'] = Tools::usingSecureMode()
        ? Tools::getShopDomainSSL() : Tools::getShopDomain();

    try {
        SitemapBuilder::getInstance()->generate($id_lang, $with_image, $with_link);
        if (Tools::isSubmit('ajax') && !ToolsModuleSMP::isPHPCLI()) {
            $t = TransModSMP::getInstance();
            $exists_sitemap_link = file_exists(SitemapConfig::getFullLinkSitemap(
                $id_lang,
                false,
                $with_image,
                $with_link
            ));
            die(json_encode(array(
                'hasError' => ($exists_sitemap_link ? false : true),
                'link' => SitemapConfig::getLinkSitemap(
                    $id_lang,
                    false,
                    $with_image,
                    $with_link
                ),
                'pages' => ToolsSMP::getPages(SitemapConfig::getFullLinkSitemap(
                    $id_lang,
                    false,
                    $with_image,
                    $with_link
                )),
                'date' => ToolsSMP::getDateSitemap(SitemapConfig::getFullLinkSitemap(
                    $id_lang,
                    false,
                    $with_image,
                    $with_link
                )),
                'message' => (
                $exists_sitemap_link
                    ? $t->l('Sitemap generate successfully!', __FILE__)
                    : $t->l('Sitemap generate with error!', __FILE__)
                ),
            )));
        }
    } catch (Exception $e) {
        ErrorLoggerSMP::getInstance()->add(
            $e->getMessage().PHP_EOL
            .$e->getTraceAsString()
        );

        die(json_encode(array(
            'hasError' => true,
            'message' => $e->getMessage()
        )));
    }
}
exit;
