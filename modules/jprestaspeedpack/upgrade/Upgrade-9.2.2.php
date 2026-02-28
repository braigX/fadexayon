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
 * Add hifaq as a managed controller
 */
function upgrade_module_9_2_2($module)
{
    $module->installOverridesForModules();
    Configuration::deleteByName('pagecache_hifaq__faq');
    Configuration::deleteByName('pagecache_hifaq__faqdetails');
    Configuration::deleteByName('pagecache_hifaq__faqcategory');

    // It does not matter if it fails
    return true;
}
