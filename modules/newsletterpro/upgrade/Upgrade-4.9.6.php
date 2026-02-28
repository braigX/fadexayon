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

/**
 * @param NewsletterPro $module
 *
 * @return mixed
 */
function upgrade_module_4_9_6($module)
{
    $upgrade = $module->upgrade;

    $upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

    $upgrade->updateConfiguration('TASK_MEMORY_CHECK_ENABLED', true);

    $upgrade->updateConfiguration('SUBSCRIPTION_HOOK_POPUP_TYPE', NewsletterProSubscriptionHook::convertToUpper(pqnp_config('SUBSCRIPTION_HOOK_POPUP_TYPE')));

    $upgrade->addColumn('newsletter_pro_task', 'started', '`started` int(1) NOT NULL DEFAULT 0', 'send_method');

    return $upgrade->success();
}
