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
 * @var $module Jprestaspeedpack
 *
 * @return bool
 *
 * @throws Exception
 */
function upgrade_module_7_4_0($module)
{
    $ret = true;
    if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
        $ret = $module->addOverride('Media');
    }

    $module->registerHook('actionTaxManager');

    // Do not add CSS and JS version in the cache key if Media was correctly overriden
    Configuration::updateValue('pagecache_depend_on_css_js', !$ret);

    Configuration::deleteByName('pagecache_key_tax_country');
    Configuration::deleteByName('pagecache_key_tax_state');
    Configuration::deleteByName('pagecache_key_tax_postcode');

    if ($ret) {
        $module->clearCacheAndStats('Upgrade 7.4.0');
    }

    return (bool) $ret;
}
