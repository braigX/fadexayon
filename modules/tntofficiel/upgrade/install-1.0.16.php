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
function upgrade_module_1_0_16($objArgTNTOfficiel_1_0_16)
{
    // Module::uninstall().
    if (!$objArgTNTOfficiel_1_0_16->uninstall()) {
        return false;
    }

    // If MultiShop and more than 1 Shop.
    if (Shop::isFeatureActive()) {
        // Define Shop context to all Shops.
        Shop::setContext(Shop::CONTEXT_ALL);
    }

    TNTOfficiel_Tools::removeFiles(
        _PS_MODULE_DIR_ . TNTOfficiel::MODULE_NAME . DIRECTORY_SEPARATOR,
        array(),
        array(
            'views/css/s08ww0/',
            'views/js/s08ww0/',
        )
    );

    // Module::install().
    if (!$objArgTNTOfficiel_1_0_16->install()) {
        return false;
    }

    // Success.
    return true;
}
