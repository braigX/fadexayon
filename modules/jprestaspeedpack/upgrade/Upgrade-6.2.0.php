<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Update registered hooks and delete files from old versions that have been moved
 */
function upgrade_module_6_2_0($module)
{
    $ret = true;

    $module->unregisterHook('actionClearCache');
    $module->unregisterHook('actionObjectStockAddAfter');
    $module->unregisterHook('actionObjectStockUpdateAfter');
    $module->unregisterHook('actionObjectStockDeleteBefore');
    $module->unregisterHook('actionProductSave');
    $module->unregisterHook('actionProductUpdate');
    $module->unregisterHook('actionProductAttributeUpdate');
    $module->unregisterHook('actionProductAttributeDelete');
    $module->unregisterHook('actionUpdateQuantity');

    $module->registerHook('actionObjectStockAvailableUpdateBefore');
    $module->registerHook('actionObjectStockAvailableUpdateAfter');
    $module->registerHook('actionObjectProductUpdateBefore');
    $module->registerHook('actionObjectProductUpdateAfter');
    $module->registerHook('actionObjectCombinationUpdateBefore');
    $module->registerHook('actionObjectCombinationUpdateAfter');
    $module->registerHook('actionObjectCombinationDeleteAfter');
    $module->registerHook('actionObjectStockAddBefore');
    $module->registerHook('actionObjectStockAddAfter');
    $module->registerHook('actionObjectStockUpdateBefore');
    $module->registerHook('actionObjectStockUpdateAfter');
    $module->registerHook('actionObjectWarehouseProductLocationAddBefore');
    $module->registerHook('actionObjectWarehouseProductLocationAddAfter');
    $module->registerHook('actionObjectWarehouseProductLocationDeleteBefore');
    $module->registerHook('actionObjectWarehouseProductLocationDeleteAfter');

    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheCache.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheCacheMemcache.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheCacheMemcached.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheCacheMultiStore.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheCacheSimpleFS.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheCacheZipArchive.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheDAO.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheURLNormalizer.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/PageCacheUtils.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/http_build_url.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/pt.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/sv.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/fr.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/es.php');
    JprestaUtils::deleteFile(_PS_MODULE_DIR_ . '/' . $module->name . '/br.php');

    return (bool) $ret;
}
