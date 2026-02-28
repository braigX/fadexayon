<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_0_2_4($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $module_obj->dataBase('runSql', [
        'ALTER TABLE ' . _DB_PREFIX_ . 'af_seopage CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
        'ALTER TABLE ' . _DB_PREFIX_ . 'af_seopage_lang CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
    ]);

    return true;
}
