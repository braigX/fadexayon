<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Configuration file to switch bettween GEODIS and France Express
 */

$path = dirname(__FILE__);
if (Tools::strlen($path) - strrpos($path, 'geodisofficiel') == 22) {
    define('GEODIS_NAME', 'GEODIS');
    define('GEODIS_MODULE_NAME', 'geodisofficiel');
    define('GEODIS_NAME_SQL', 'geodis');
    define('GEODIS_ADMIN_PREFIX', 'AdminGeodis');
} else {
    define('GEODIS_NAME', 'France Express');
    define('GEODIS_MODULE_NAME', 'franceexpress');
    define('GEODIS_NAME_SQL', 'franceexpress');
    define('GEODIS_ADMIN_PREFIX', 'AdminFranceExpress');
}

define('GEODIS_MODULE_DIR', _PS_MODULE_DIR_.GEODIS_MODULE_NAME.'/');
define('GEODIS_LOGO', _PS_MODULE_DIR_.'geodisofficiel/views/img/logo_'.GEODIS_MODULE_NAME.'.png');

$customApiUri = getenv('GEODIS_API_URI');

if ($customApiUri) {
    define('GEODIS_API_URI', $customApiUri);
} else {
    define('GEODIS_API_URI', 'https://espace-client.geodis.com/services/');
}

define('GEODIS_API_TIMEOUT', 1500);
define('GEODIS_MODULE_VERSION', '1.0.0');

define('GEODIS_LIBELLE_API', 'GEODIS'.'_LIBELLE_API');
define('GEODIS_SECRET_API', '9167c9e56a9a4e1bb97fbed018bdb62b');
