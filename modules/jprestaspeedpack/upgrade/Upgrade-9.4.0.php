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
 * Profiler state is now the same for all shops
 */
function upgrade_module_9_4_0($module)
{
    if ($module->isSpeedPack()) {
        JPresta\SpeedPack\JprestaUtils::saveConfigurationAllShop('SPEED_PACK_SQL_PROFILER_ENABLE', 0);
        $mod = new JprestaSQLProfilerModule($module);
        $mod->disableOverride();
    }

    return true;
}
