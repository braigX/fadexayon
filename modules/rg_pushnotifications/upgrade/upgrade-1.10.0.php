<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_10_0($module)
{
    if (file_exists(dirname(__FILE__) . '/../error_log')) {
        rename(dirname(__FILE__) . '/../error_log', dirname(__FILE__) . '/../logs/error_log');
    }

    return Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'rg_pushnotifications_subscriber`
        ADD `from_app` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `unsubscribed`;');
}
