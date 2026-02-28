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
 * Hook to actionOnImageResizeAfter
 */
function upgrade_module_6_4_6($module)
{
    $ret = true;
    // SPEEDPACK
    $module->registerHook('actionOnImageResizeAfter');

    // SPEEDPACK£
    return (bool) $ret;
}
