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

/**
 * This function updates the module from previous versions to this version.
 * Triggered if module is installed and source is directly updated.
 * http://doc.prestashop.com/display/PS17/Enabling+the+Auto-Update
 */
function upgrade_module_1_0_13($objArgTNTOfficiel_1_0_13)
{
    // Module::uninstall().
    if (!$objArgTNTOfficiel_1_0_13->uninstall()) {
        return false;
    }

    // If MultiShop and more than 1 Shop.
    if (Shop::isFeatureActive()) {
        // Define Shop context to all Shops.
        Shop::setContext(Shop::CONTEXT_ALL);
    }

    TNTOfficiel_Tools::removeFiles(
        _PS_MODULE_DIR_ . TNTOfficiel::MODULE_NAME . DIRECTORY_SEPARATOR,
        array(
            'libraries/TNTOfficiel_Cache.php',
            'libraries/TNTOfficiel_Parcel.php',
            'libraries/pdf/fpdf/fpdf.css',
            'views/img/ajax-loader.gif',
            'views/img/map/marker/1.png',
            'views/img/map/marker/2.png',
            'views/img/map/marker/3.png',
            'views/img/map/marker/4.png',
            'views/img/map/marker/5.png',
            'views/img/map/marker/6.png',
            'views/img/map/marker/7.png',
            'views/img/map/marker/8.png',
            'views/img/map/marker/9.png',
            'views/img/map/marker/10.png',
            'views/templates/admin/displayAdminOrder.tpl',
        ),
        array(
            'libraries/data/hra/',
            'libraries/pdf/manifest/',
            'views/css/pihmhe/',
            'views/css/pl43w0/',
            'views/css/psvns0/',
            'views/css/q0y640/',
            'views/css/q7qx80/',
            'views/css/qhhrs0/',
            'views/css/qji2k0/',
            'views/css/qtzrs0/',
            'views/fonts/',
            'views/js/pihmhe/',
            'views/js/pl43w0/',
            'views/js/psvns0/',
            'views/js/q0y640/',
            'views/js/q7qx80/',
            'views/js/qhhrs0/',
            'views/js/qji2k0/',
            'views/js/qtzrs0/',
            'views/templates/hook/displayAfterCarrier/',
        )
    );

    // Module::install().
    if (!$objArgTNTOfficiel_1_0_13->install()) {
        return false;
    }

    // Success.
    return true;
}
