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
 * Create missing tables if it has been migrated
 */
function upgrade_module_9_4_3($module)
{
    if ($module->isSpeedPack()) {
        // Tables will not be created if they already exist
        JprestaSQLProfilerRun::createTable();
        JprestaSQLProfilerQuery::createTable();
        JprestaSQLProfilerCallstack::createTable();
    }

    return true;
}
