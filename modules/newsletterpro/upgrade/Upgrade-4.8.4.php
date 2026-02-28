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

function upgrade_module_4_8_4($module)
{
    $upgrade = $module->upgrade;

    $upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

    // fix for the update 4.7.0, it is missing from there
    $upgrade->alter('newsletter_pro_unsibscribed', '
		'.$upgrade->addColumnIfNotExists('newsletter_pro_unsibscribed', 'date_upd', 'datetime default null AFTER `date_add`', true).'
		CHANGE COLUMN `date_add` `date_add` datetime default null
	');

    return $upgrade->success();
}
