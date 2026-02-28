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
 * Re-generate static cache script
 */
function upgrade_module_8_3_3($module)
{
    $module->installCache();

    // It does not matter if it fails
    return true;
}
