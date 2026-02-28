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

/**
 * Use /override/modules_ to avoid the check of overrides of modules
 */
function upgrade_module_9_4_14($module)
{
    JPresta\SpeedPack\JprestaUtils::deleteDirectory(_PS_MODULE_DIR_ . $module->name . '/override/modules');

    return true;
}
