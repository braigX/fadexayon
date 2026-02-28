<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_'))
    exit;
function upgrade_module_2_0_6()
{
    if ($shops = Shop::getShops()){
        $shops[] = array('id_shop' => null);
        foreach ($shops as $shop){
            Configuration::updateValue('ETS_CTF7_ENABLE_HOOK_SHORTCODE','0',false,null,(int)$shop['id_shop']);
            Configuration::updateValue('ETS_CTF7_RECAPTCHA_TYPE','v2',false,null,(int)$shop['id_shop']);
        }
    }
    return true;
}