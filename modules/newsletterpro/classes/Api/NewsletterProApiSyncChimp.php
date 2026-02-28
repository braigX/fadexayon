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

class NewsletterProApiSyncChimp extends NewsletterProApi
{
    public function call()
    {
        $module = NewsletterProTools::module();

        if (!isset($module->chimp)) {
            throw new Exception('The chimp is not defined');
        }

        $chimp = &$module->chimp;

        ignore_user_abort(true);
        set_time_limit(0);
        @ini_set('max_execution_time', '0');

        if (!NewsletterProConfig::test('LAST_DATE_CHIMP_SYNC')) {
            NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', date('Y-m-d H:i:s'));
        }

        $dbFrom = NewsletterProConfig::get('LAST_DATE_CHIMP_SYNC');

        if (strtotime($dbFrom)) {
            $from = $dbFrom;
        } else {
            $from = date('Y-m-d H:i:s');
            NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', $from);
        }

        $to = date('Y-m-d', strtotime('+1 days'));

        if ($chimp->ping()) {
            NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', date('Y-m-d H:i:s'));
        }

        if (Tools::isSubmit('forceSync')) {
            $from = '0000-00-00 00:00:00';
        }

        $sync = $chimp->syncLists($from, $to);

        $module->context->smarty->assign($sync);
        $module->context->smarty->assign([
            'last_date_chimp_sync' => NewsletterProConfig::get('LAST_DATE_CHIMP_SYNC'),
            'chimp_last_date_sync_orders' => NewsletterProConfig::get('CHIMP_LAST_DATE_SYNC_ORDERS'),
            'subscription_active' => (bool) pqnp_config('SUBSCRIPTION_ACTIVE'),
        ]);

        return $module->context->smarty->fetch(pqnp_template_path($module->dir_location.'views/templates/admin/sync_chimp.tpl'));
    }
}
