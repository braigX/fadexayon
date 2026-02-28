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

function upgrade_module_1_8_3($module)
{
    $upgrade = $module->upgrade;

    // hooks update (no)
    // configuration update (no)
    $upgrade->updateConfiguration('DISPLAY_ACTIONS_COLUMN', '1');
    $upgrade->updateConfiguration('CAMPAIGN', $module->default_campaign_params);

    $upgrade->updatePSConfiguration('NEWSLETTER_PRO_CAMPAIGN', '', false, 0, 0);
    // database update (no)
    return $upgrade->success();
}
