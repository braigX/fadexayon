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

function upgrade_module_4_7_0($module)
{
    $upgrade = $module->upgrade;

    $upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

    $upgrade->registerHook('backOfficeHeader');
    $upgrade->registerHook('actionAdminControllerSetMedia');
    $upgrade->registerHook('registerGDPRConsent');
    $upgrade->registerHook('actionDeleteGDPRCustomer');
    $upgrade->registerHook('actionExportGDPRData');

    $upgrade->alter('newsletter_pro_unsibscribed', '
		'.$upgrade->addColumnIfNotExists('newsletter_pro_unsibscribed', 'date_upd', 'datetime default null AFTER `date_add`', true).'
		CHANGE COLUMN `date_add` `date_add` datetime default null
	');

    $upgrade->alter('newsletter_pro_fwd_unsibscribed', '
		'.$upgrade->addColumnIfNotExists('newsletter_pro_fwd_unsibscribed', 'date_upd', 'datetime default null AFTER `date_add`', true).'
		CHANGE COLUMN `date_add` `date_add` datetime default null
	');

    if (!$upgrade->hasPrimaryKey('newsletter_pro_customer_category')) {
        $upgrade->alter('newsletter_pro_customer_category', '
			'.$upgrade->addColumnIfNotExists('newsletter_pro_customer_category', 'id_newsletter_pro_customer_category', 'int(10) unsigned NOT null AUTO_INCREMENT FIRST', true).'
			'.$upgrade->addColumnIfNotExists('newsletter_pro_customer_category', 'date_add', 'datetime default null AFTER `categories`', true).'
			'.$upgrade->addColumnIfNotExists('newsletter_pro_customer_category', 'date_upd', 'datetime default null AFTER `date_add`', true).'
			ADD PRIMARY KEY(`id_newsletter_pro_customer_category`)
		');
    }

    if (!$upgrade->hasPrimaryKey('newsletter_pro_customer_list_of_interests')) {
        $upgrade->alter('newsletter_pro_customer_list_of_interests', '
			'.$upgrade->addColumnIfNotExists('newsletter_pro_customer_list_of_interests', 'id_newsletter_pro_customer_list_of_interests', 'int(10) unsigned NOT null AUTO_INCREMENT FIRST', true).'
			'.$upgrade->addColumnIfNotExists('newsletter_pro_customer_list_of_interests', 'date_add', 'datetime default null AFTER `categories`', true).'
			'.$upgrade->addColumnIfNotExists('newsletter_pro_customer_list_of_interests', 'date_upd', 'datetime default null AFTER `date_add`', true).'
			ADD PRIMARY KEY(`id_newsletter_pro_customer_list_of_interests`)
		');
    }

    $upgrade->updateConfiguration('SUBSCRIPTION_SECURE_SUBSCRIBE', '1');
    $upgrade->deleteConfiguration('USE_CACHE');
    $upgrade->updateConfiguration('LOAD_MINIFIED', true);

    return $upgrade->success();
}
