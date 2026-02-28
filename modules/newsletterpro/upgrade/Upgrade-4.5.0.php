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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_5_0($module)
{
    $upgrade = $module->upgrade;

    $upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

    $upgrade->addColumn('newsletter_pro_smtp', 'list_unsubscribe_active', '`list_unsubscribe_active` INT(1) NULL DEFAULT "0"', 'port');
    $upgrade->addColumn('newsletter_pro_smtp', 'list_unsubscribe_email', '`list_unsubscribe_email` VARCHAR(255) NOT NULL', 'list_unsubscribe_active');

    return $upgrade->success();
}
