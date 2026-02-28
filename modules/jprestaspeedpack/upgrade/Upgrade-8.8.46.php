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
 * Check for old GD version, disable PNG if necessary
 */
function upgrade_module_8_8_46($module)
{
    JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_CONVERTER_DISABLE_PNG', false);
    if (JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_CONVERTER_TO_USE') === 'gd') {
        $gdInfos = gd_info();
        if ($gdInfos['GD Version'] === 'bundled (2.1.0 compatible)') {
            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_CONVERTER_DISABLE_PNG', true);
        }
    }
    // /upload is now ignore for WEBP.
    $module->hookActionHtaccessCreate([]);

    if (!JprestaUtils::dbIndexExists(_DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS, ['used_by_cw', 'date_add'])) {
        return JprestaUtils::dbExecuteSQL('ALTER TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
            ADD INDEX `idx_order_context` (used_by_cw, date_add) USING BTREE
        ');
    }

    return true;
}
