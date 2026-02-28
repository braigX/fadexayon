<?php
/**
 * 2007-2023 PrestaShop.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ádalop <contact@prestashop.com>
 *  @copyright 2023 Ádalop
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_4_2($object)
{
    Configuration::deleteByName('ADP_URL_IMG_HOME_OG');
    Configuration::deleteByName('ADP_URL_IMG_CATEGORY_OG');
    Configuration::deleteByName('ADP_URL_IMG_MANUFACTURER_OG');
    Configuration::deleteByName('ADP_FACEBOOK_ADMIN_ID');
    Configuration::deleteByName('ADP_ACTIVE_OPEN_GRAPH_SOCIAL_NETWORK');
    Configuration::deleteByName('ADP_ACTIVE_PIXEL_FACEBOOK');

    $object->unregisterHook('displayAfterTitleOpenGraph');
    $object->unregisterHook('adpMicrodatosHook');
    $object->unregisterHook('displayFooter');

    return true;
}
