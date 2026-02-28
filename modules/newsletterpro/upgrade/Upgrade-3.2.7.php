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

function upgrade_module_3_2_7($module)
{
    $upgrade = $module->upgrade;

    $chimp_config = Config::get('CHIMP', []);

    if (is_array($chimp_config) && array_key_exists('ORDERS_CHECKBOX', $chimp_config)) {
        if (!pqnp_config('CHIMP', array_merge($chimp_config, [
            'ORDERS_CHECKBOX' => '1',
        ]), true)) {
            $upgrade->addError(sprintf('Cannot update the configuration with the name "%s".', 'ORDERS_CHECKBOX'));

            return false;
        }
    }

    if (!$upgrade->valueExists('newsletter_pro_config', 'name', 'CHIMP_LAST_DATE_SYNC_ORDERS')) {
        $upgrade->insertValue('newsletter_pro_config', [
            'name' => 'CHIMP_LAST_DATE_SYNC_ORDERS',
            'value' => '0000-00-00 00:00:00',
        ]);
    }

    return $upgrade->success();
}
