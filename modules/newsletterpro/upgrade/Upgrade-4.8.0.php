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

function upgrade_module_4_8_0($module)
{
    $upgrade = $module->upgrade;

    $upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

    $upgrade->addColumn('newsletter_pro_subscription_tpl', 'render_loader', '`render_loader` int(11) DEFAULT 0', 'terms_and_conditions_url');
    $upgrade->addColumn('newsletter_pro_subscription_tpl_lang', 'email_unsubscribe_confirmation_message', '`email_unsubscribe_confirmation_message` longtext default null', 'email_subscribe_confirmation_message');
    $upgrade->addColumn('newsletter_pro_subscribers_temp', 'load_file', '`load_file` varchar(255) default null', 'id_newsletter_pro_subscription_tpl');

    // create the new template, an update the new field for the old ones
    $template_dir = _NEWSLETTER_PRO_DIR_.'/install/tables/subscription_tpl/responsive_new/';
    $subscription_tpl = NewsletterProSubscriptionTpl::loadFile($template_dir, true);
    if (!$subscription_tpl->isDuplicateName()) {
        $results = Db::getInstance()->executeS('
			SELECT `id_newsletter_pro_subscription_tpl` FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl`
		');

        foreach ($results as $row) {
            $stpl = new NewsletterProSubscriptionTpl((int) $row['id_newsletter_pro_subscription_tpl']);
            if (Validate::isLoadedObject($stpl)) {
                $stpl->email_unsubscribe_confirmation_message = $subscription_tpl->email_unsubscribe_confirmation_message;
                $stpl->update();
            }
        }

        $subscription_tpl->add();
    }

    return $upgrade->success();
}
