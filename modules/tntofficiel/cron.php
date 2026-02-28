<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */
/**
 * CRON hourly: php7.0 /<INSTALL>/modules/tntofficiel/cron.php
 */

$_SERVER['REMOTE_ADDR'] = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : null;
$_SERVER['SERVER_PROTOCOL'] = array_key_exists('SERVER_PROTOCOL', $_SERVER) ? $_SERVER['SERVER_PROTOCOL'] : null;
$_SERVER['SERVER_NAME'] = array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : null;

$boolCLI = (php_sapi_name() === 'cli'
    && $_SERVER['REMOTE_ADDR'] === null
    && $_SERVER['SERVER_NAME'] === null
    && $_SERVER['SERVER_PROTOCOL'] === null
);

$arrAllowedIP = array(
    // Your Local IP.
    '127.0.0.1',
    //'192.168.1.1',
    // Your External IP.
);

$boolAllowedIP = (in_array(
        $_SERVER['REMOTE_ADDR'],
        $arrAllowedIP
        , true
    ) === true);

// If not a command-line and not an allowed HTTP request.
if (!$boolCLI && !$boolAllowedIP) {
    // redirect.
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/config.inc.php';

require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_ClassLoader.php';

// Update All Parcels for up to 1 hour(s) of processing time and up to 2048 orders, for orders less than 30 days old.
$arrOrderUpdatedID = TNTOfficielOrder::updateAllOrderStateDeliveredParcels(3000, 2048, 30, 7200);
