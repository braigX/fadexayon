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
 * Update Media to logs why it is cleared
 */
function upgrade_module_8_1_17($module)
{
    $upgraded_ok = $module->upgradeOverride('Media');

    return (bool) $upgraded_ok;
}
