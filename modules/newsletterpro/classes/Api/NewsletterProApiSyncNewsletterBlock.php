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

class NewsletterProApiSyncNewsletterBlock extends NewsletterProApi
{
    public function call()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $module = NewsletterProTools::module();

        $response = NewsletterProTools::jsonDecode($module->importEmailsFromBlockNewsletterCron(pqnp_config('LAST_DATE_NEWSLETTER_BLOCK_SYNC')), true);

        echo '<pre>';
        if (!empty($response['errors'])) {
            echo $module->l('Errors');
            echo '<br>';
            echo '<br>';
            exit(implode('<br>', $response['errors']));
        } else {
            pqnp_config('LAST_DATE_NEWSLETTER_BLOCK_SYNC', date('Y-m-d H:i:s'));
            exit($response['msg']);
        }
    }
}
