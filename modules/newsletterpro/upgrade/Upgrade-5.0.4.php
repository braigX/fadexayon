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
function upgrade_module_5_0_4($module)
{
    /** @var NewsletterProUpgrade */
    $upgrade = $module->upgrade;

    $upgrade->showCacheWarning();

    return $upgrade->success();
}
