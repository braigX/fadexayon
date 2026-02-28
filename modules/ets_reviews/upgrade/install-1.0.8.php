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

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_0_8()
{
    // Configuration:
    $free_downloads_disabled = Module::isEnabled('ets_free_downloads') ? 1 : 0;
    $execCmd = Configuration::updateValue('ETS_RV_FREE_DOWNLOADS_ENABLED', $free_downloads_disabled);

    $execCmd &= Configuration::deleteByName('ETS_RV_CRONJOB_CLEAR_DISCOUNT_USED');
    if (Configuration::getGlobalValue('ETS_RV_CRONJOB_CLEAR_DISCOUNT_EXPIRED'))
        $execCmd &= Configuration::updateGlobalValue('ETS_RV_AUTO_CLEAR_DISCOUNT', 1);
    $execCmd &= Configuration::deleteByName('ETS_RV_CRONJOB_CLEAR_DISCOUNT_EXPIRED');

    if ($free_downloads_disabled > 0) {
        $who_post_review = explode(',', Configuration::get('ETS_RV_WHO_POST_REVIEW'));
        $loop = 0;
        if (count($who_post_review)) {
            foreach ($who_post_review as $who_post) {
                if (trim($who_post) == 'no_purchased') {
                    $who_post_review[$loop] = 'no_purchased_incl';
                    $who_post_review[] = 'no_purchased_excl';
                    Configuration::updateValue('ETS_RV_WHO_POST_REVIEW', implode(',', $who_post_review), true);
                    break;
                }
                $loop++;
            }
        }
    }
    $execCmd &= Configuration::updateValue('ETS_RV_QA_AUTO_APPROVE', 0);

    // Query:
    $execCmd &= Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_activity` SET `type`=\'ans\' WHERE `action`=\'ans\'');

    return $execCmd;
}