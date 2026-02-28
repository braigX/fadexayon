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
function upgrade_module_4_9_7($module)
{
    $upgrade = $module->upgrade;

    $upgrade->showCacheWarning();

    $output = [];

    $output[] = $module->l(sprintf('It\'s important to clear the prestashop cache and the browser cache because the file "%s" was removed. A new ajax path has been added.', 'scripts/ajax_newsletterpro_front.php'));
    $output[] = ' ';
    $output[] = $module->l('The CRON Job and API links have been changed. If you added them in your CPanel you must change them with the new ones.');
    $output[$module->l('[Tasks CRON URL]')] = NewsletterProApi::getLink('task', [], true, true);
    $output[$module->l('[Mailchimp CRON URL]')] = NewsletterProApi::getLink('syncChimp', [], true, true);
    $output[$module->l('[Mailchimp Webhook URL]')] = NewsletterProApi::getLink('mailchimp', [], true, true);
    $output[$module->l('[Sync Newsletter Block CRON URL]')] = NewsletterProApi::getLink('syncNewsletterBlock', [], true, true);
    $output[$module->l('[Bounced email Webhook URL]')] = NewsletterProApi::getLink('bounce', ['bounceAction' => 'unsubscribe', 'email' => 'demo@demo.com'], true, true);

    NewsletterProUpgrade::showWarningMessage($output);

    return $upgrade->success();
}
