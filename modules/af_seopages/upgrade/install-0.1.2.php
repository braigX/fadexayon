<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_0_1_2($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $module_obj->db->execute('
        ALTER TABLE ' . $module_obj->sqlTable('_lang') . '
        ADD description_lower TEXT NOT NULL AFTER description
    ');
    $module_obj->registerHook('displayHeader');
    $module_obj->registerHook('gSitemapAppendUrls');
    $module_obj->fillMissingSettings();

    return true;
}
