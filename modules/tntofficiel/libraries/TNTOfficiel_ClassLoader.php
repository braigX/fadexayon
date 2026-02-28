<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_TNTOFFICIEL_CLASSLOADER_')) {
    define('_TNTOFFICIEL_CLASSLOADER_', true);
    require_once _PS_MODULE_DIR_ . 'tntofficiel/tntofficiel.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielCache.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielAccount.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielCarrier.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielCart.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielLabel.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielOrder.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielParcel.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielPickup.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/classes/TNTOfficielReceiver.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_Install.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_Logger.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_Logstack.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_SoapClient.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_Tools.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/pdf/TNTOfficiel_PDFMerger.php';
    require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/pdf/TNTOfficiel_PDFCreator.php';
}
