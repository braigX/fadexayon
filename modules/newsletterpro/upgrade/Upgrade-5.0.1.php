<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

use PQNP\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param NewsletterPro $module
 *
 * @return mixed
 */
function upgrade_module_5_0_1($module)
{
    /** @var NewsletterProUpgrade */
    $upgrade = $module->upgrade;

    $chimp = pqnp_config_get('CHIMP');

    $chimp_data = [
        'INSTALLED' => false,
        'API_KYE' => '',
        'ID_LIST' => '',
        'ID_GROUPING' => '',
        'CUSTOMERS_GROUP_IDS' => [],
        'FIELDS' => [],
        'CUSTOMERS_CHECKBOX' => 0,
        'VISITORS_CHECKBOX' => 0,
        'ADDED_CHECKBOX' => 0,
        'ORDERS_CHECKBOX' => 0,
    ];

    if (!is_array($chimp) || (is_array($chimp) && 0 == count($chimp))) {
        $upgrade->updateConfiguration('CHIMP', $chimp_data);
    } elseif (is_array($chimp)) {
        foreach ($chimp_data as $key => $value) {
            if (!array_key_exists($key, $chimp)) {
                $chimp[$key] = $value;
            }
        }

        $upgrade->updateConfiguration('CHIMP', $chimp);
    }

    $config = pqnp_config();
    if (!array_key_exists('LEFT_MENU_ACTIVE', $config)) {
        Config::write('LEFT_MENU_ACTIVE', 1);
    }

    $upgrade->showCacheWarning();

    return $upgrade->success();
}
