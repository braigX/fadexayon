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
 * Make sure pagecache_ignored_params is correctly set
 */
function upgrade_module_9_4_11($module)
{
    Jprestaspeedpack::updateIgnoredUrlParameters();

    return true;
}
