<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Re-generate .htaccess
 */
function upgrade_module_9_2_1($module)
{
    if (Shop::isFeatureActive()) {
        $module->hookActionHtaccessCreate([]);
    }

    return true;
}
