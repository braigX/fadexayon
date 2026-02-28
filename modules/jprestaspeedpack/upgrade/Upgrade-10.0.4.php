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
 * Hook to actionObjectUpdateBefore and actionObjectDeleteBefore for groupinc
 */
function upgrade_module_10_0_4($module)
{
    $module->registerHook('actionObjectUpdateBefore');
    $module->registerHook('actionObjectDeleteBefore');

    return true;
}
