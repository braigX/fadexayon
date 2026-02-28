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

use PQNP\Upgrade\Upgrade500;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param NewsletterPro $module
 *
 * @return mixed
 */
function upgrade_module_5_0_0($module)
{
    /** @var NewsletterProUpgrade */
    $upgrade = $module->upgrade;

    // this is created in NewsletterPro::initConfiguration
    if (false == (bool) Configuration::get('NEWSLETTER_PRO_CONFIGURATION_CALL')) {
        (new Upgrade500())->call();
    }

    Configuration::deleteByName('NEWSLETTER_PRO_CONFIGURATION_CALL');

    $campaign = pqnp_config_get('CAMPAIGN');

    if (!is_array($campaign)) {
        $campaign = [
            'UTM_SOURCE' => 'Newsletter',
            'UTM_MEDIUM' => 'email',
            'UTM_CAMPAIGN' => '{newsletter_title}',
            'UTM_CONTENT' => '{product_name}',
        ];
        $upgrade->updateConfiguration('CAMPAIGN', $campaign);
    }

    return $upgrade->success();
}
