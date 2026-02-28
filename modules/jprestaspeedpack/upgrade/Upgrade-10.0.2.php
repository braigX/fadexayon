<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Use the new JSON encoding to replace serialize function
 */
function upgrade_module_10_0_2($module)
{
    $shopIds = Shop::getShops(true, null, true);
    foreach ($shopIds as $shopId) {
        $encodedConf = JprestaUtils::getConfigurationByShopId('pagecache_cachekey_usergroups', $shopId, '');
        if (!$encodedConf) {
            continue;
        }

        $compressedConf = @base64_decode($encodedConf, true);
        if ($compressedConf === false) {
            continue;
        }

        $decompressedConf = @gzuncompress($compressedConf);
        if ($decompressedConf === false) {
            continue;
        }

        // Required here to migrate to the new JSON-based storage format, as requested by Addons.
        $conf = @unserialize($decompressedConf);
        if ($conf === false || !is_array($conf)) {
            continue;
        }

        // Save with new JSON format
        Jprestaspeedpack::saveCacheKeyForUserGroups($shopId, $conf);
    }

    // It does not matter if it fails
    return true;
}
