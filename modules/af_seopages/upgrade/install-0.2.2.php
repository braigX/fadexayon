<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_0_2_2($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $outdated_sitemap_dir = _PS_MODULE_DIR_ . $module_obj->name . '/sitemap/';
    if (file_exists($outdated_sitemap_dir) && is_dir($outdated_sitemap_dir)) {
        foreach (glob($outdated_sitemap_dir . '*') as $file) {
            unlink($file);
        }
        rmdir($outdated_sitemap_dir);
    }

    return true;
}
