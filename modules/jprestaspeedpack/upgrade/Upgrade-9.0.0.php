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
 * Install SQL profiler
 */
function upgrade_module_9_0_0($module)
{
    $ret = true;

    if ($module->isSpeedPack()) {
        $webpTab = new Tab((int) Tab::getIdFromClassName('AdminJprestaWebpConfiguration'));
        $webpTab->updatePosition(0, 2);
        $module->installTab('AdminJprestaSQLProfilerConfiguration', [
            'en' => 'SQL Profiler',
            'fr' => 'Profilage SQL',
            'es' => 'SQL Profiler',
        ], (int) Tab::getIdFromClassName('AdminParentSpeedPack'));
        $sqlTab = new Tab((int) Tab::getIdFromClassName('AdminJprestaSQLProfilerConfiguration'));
        $sqlTab->updatePosition(0, 3);
        $mod = new JprestaSQLProfilerModule($module);
        $mod->install();
    }

    // Add use JPresta\SpeedPack\JprestaUtils;
    copy(_PS_MODULE_DIR_ . $module->name . '/override/modules/prestablog/controllers/front/blog.php', _PS_OVERRIDE_DIR_ . 'modules/prestablog/controllers/front/blog.php');

    return $ret;
}
