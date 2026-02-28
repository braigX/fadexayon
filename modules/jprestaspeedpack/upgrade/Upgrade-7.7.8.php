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
 */
function upgrade_module_7_7_8($module)
{
    $ret = true;

    JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price_rule', ['id_country', 'to']);
    JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price_rule', ['id_group', 'to']);
    JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price', ['id_country', 'to']);
    JprestaUtils::dbCreateIndexIfNotExists(_DB_PREFIX_ . 'specific_price', ['id_group', 'to']);

    return (bool) $ret;
}
