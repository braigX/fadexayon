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
 * Fix multiple entries in menu
 */
function upgrade_module_6_4_2($module)
{
    $ret = true;

    // Delete all existing menus
    $tabIdsRows = Db::getInstance()->executeS('SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` WHERE module=\'' . pSQL($module->name) . '\' ', true, false);
    foreach ($tabIdsRows as $tabIdRow) {
        $tabToDelete = new Tab((int) $tabIdRow['id_tab']);
        $tabToDelete->delete();
    }

    // Create menus again
    if ($module->isSpeedPack()) {
        $module->installTab('AdminParentSpeedPack', 'JPresta - Speed pack', (int) Tab::getIdFromClassName('AdminAdvancedParameters'));
        $module->installTab('AdminPageCacheConfiguration', 'Page Cache Ultimate', (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
        $module->installTab('AdminJprestaLazyLoadingConfiguration', [
            'en' => 'Lazy load of images',
            'fr' => 'Chargement différé des images',
            'es' => 'Carga bajo demanda de imágenes',
        ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
        $module->installTab('AdminJprestaWebpConfiguration', [
            'en' => 'Compression of images',
            'fr' => 'Compression des images',
            'es' => 'Compresión de imágenes',
        ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
        $module->installTab('AdminJprestaDbOptimizerConfiguration', [
            'en' => 'Database optimisation',
            'fr' => 'Nettoyage de la base de données',
            'es' => 'Limpieza de la base de datos',
        ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
    } else {
        if (JprestaUtils::version_compare(_PS_VERSION_, '1.6', '>')) {
            $idTab = (int) Tab::getIdFromClassName('AdminAdvancedParameters');
            if (!$idTab) {
                $idTab = (int) Tab::getIdFromClassName('AdminTools');
            }
            $module->installTab('AdminPageCacheConfiguration', 'JPresta - Page Cache Ultimate', $idTab);
        } elseif (JprestaUtils::version_compare(_PS_VERSION_, '1.5', '>')) {
            $module->installTab('AdminPageCacheConfiguration', 'JPresta - Page Cache Ultimate', 17);
        }
    }
    $module->installTab('AdminPageCacheMemcachedTest');
    $module->installTab('AdminPageCacheMemcacheTest');
    $module->installTab('AdminPageCacheProfilingDatas');
    $module->installTab('AdminPageCacheDatas');

    return (bool) $ret;
}
