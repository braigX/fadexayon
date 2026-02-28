<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Make sure pagecache_ignored_params is correctly set
 * Page Cache: Bug fixes for PS1.6 and PS1.5
 */
function upgrade_module_9_3_6($module)
{
    Jprestaspeedpack::updateIgnoredUrlParameters();

    if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '<')) {
        $module->installOverridesForModules();
        Jprestaspeedpack::removeManagedControllerName('hifaq__faq');
        Configuration::deleteByName('pagecache_hifaq__faq');
        Jprestaspeedpack::removeManagedControllerName('hifaq__faqcategory');
        Configuration::deleteByName('pagecache_hifaq__faqcategory');
        Jprestaspeedpack::removeManagedControllerName('hifaq__faqdetails');
        Configuration::deleteByName('pagecache_hifaq__faqdetails');

        $module->upgradeOverride('FrontController');
    }

    return true;
}
