<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Accept cookie for all fake users for module shaim_gdpr
 */
function upgrade_module_6_3_11($module)
{
    $ret = true;
    if (Module::isInstalled('shaim_gdpr')) {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `shaim_gdpr_active` = 1 WHERE `firstname` = \'fake-user-for-pagecache\';');
    }

    return (bool) $ret;
}
