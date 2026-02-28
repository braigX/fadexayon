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

function upgrade_module_1_7_0($module)
{
    return RgPuNoConfig::update('REQUEST_DELAY_PAGES_VIEWED', 3) && RgPuNoConfig::update('REQUEST_DELAY_TIME', 3);
}
