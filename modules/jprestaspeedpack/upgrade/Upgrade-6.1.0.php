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
 * Update htaccess and config file for webp
 */
function upgrade_module_6_1_0($module)
{
    $ret = true;
    // SPEEDPACK
    $module->jpresta_submodules['JprestaWebpModule']->updateConfigFile();
    $module->jpresta_submodules['JprestaWebpModule']->updateHtaccessFile();

    // SPEEDPACK£
    return (bool) $ret;
}
