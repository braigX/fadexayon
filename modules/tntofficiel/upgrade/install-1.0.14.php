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
function upgrade_module_1_0_14($objArgTNTOfficiel_1_0_14)
{
    // Module::uninstall().
    if (!$objArgTNTOfficiel_1_0_14->uninstall()) {
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
            'controllers/admin/AdminAccountSettingController.php',
            'controllers/admin/AdminCarrierSettingController.php',
            'controllers/admin/AdminTNTOrdersController.php',
        ),
        array(
            'views/css/rfxzw0/',
            'views/css/rwtx40/',
            'views/js/rfxzw0/',
            'views/js/rwtx40/',
        )
    );

    // Module::install().
    if (!$objArgTNTOfficiel_1_0_14->install()) {
        return false;
    }

    // Success.
    return true;
}
