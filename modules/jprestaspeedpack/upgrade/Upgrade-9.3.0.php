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
 * Add af_producttags and faqs as a managed controllers
 */
function upgrade_module_9_3_0($module)
{
    $module->installOverridesForModules();
    Configuration::deleteByName('af_producttags__tags');
    Configuration::deleteByName('faqs__display');

    JPresta\SpeedPack\JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_CONVERT_UPLOAD_DIR', 0);

    // It does not matter if it fails
    return true;
}
