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

function upgrade_module_2_0_1()
{
    $ETS_RV_RECORDED_ACTIVITIES = explode(',', trim(Configuration::get('ETS_RV_RECORDED_ACTIVITIES')));
    if ($ETS_RV_RECORDED_ACTIVITIES) {
        $recorded_values = [];
        foreach ($ETS_RV_RECORDED_ACTIVITIES as $ETS_RV_RECORDED_ACTIVITY) {
            switch ($ETS_RV_RECORDED_ACTIVITY) {
                case 'rev':
                    $recorded_values[] = EtsRVActivity::ETS_RV_RECORDED_REVIEWS;
                    break;
                case 'que':
                    $recorded_values[] = EtsRVActivity::ETS_RV_RECORDED_QUESTIONS;
                    break;
                case 'lie':
                    $recorded_values[] = EtsRVActivity::ETS_RV_RECORDED_USEFULNESS;
                    break;
            }
        }
        Configuration::updateValue('ETS_RV_RECORDED_ACTIVITIES', implode(',', $recorded_values));
    }

    $res = Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'ets_rv_activity`
        SET `type` = CASE
                WHEN `type` = "rev" THEN "' . (int)EtsRVActivity::ETS_RV_TYPE_REVIEW . '"
                WHEN `type` = "com" THEN "' . (int)EtsRVActivity::ETS_RV_TYPE_COMMENT . '"
                WHEN `type` = "que" THEN "' . (int)EtsRVActivity::ETS_RV_TYPE_QUESTION . '"
                WHEN `type` = "cmq" THEN "' . (int)EtsRVActivity::ETS_RV_TYPE_COMMENT_QUESTION . '"
                WHEN `type` = "ans" THEN "' . (int)EtsRVActivity::ETS_RV_TYPE_ANSWER_QUESTION . '"
                WHEN `type` = "cma" THEN "' . (int)EtsRVActivity::ETS_RV_TYPE_COMMENT_ANSWER . '"
                ELSE "' . (int)EtsRVActivity::ETS_RV_TYPE_REPLY_COMMENT . '"
            END, 
            `action` = CASE
                WHEN `action` = "rev" THEN "' . (int)EtsRVActivity::ETS_RV_ACTION_REVIEW . '"
                WHEN `action` = "com" THEN "' . (int)EtsRVActivity::ETS_RV_ACTION_COMMENT . '"
                WHEN `action` = "rep" THEN "' . (int)EtsRVActivity::ETS_RV_ACTION_REPLY . '"
                WHEN `action` = "que" THEN "' . (int)EtsRVActivity::ETS_RV_ACTION_QUESTION . '"
                WHEN `action` = "ans" THEN "' . (int)EtsRVActivity::ETS_RV_ACTION_ANSWER . '"
                WHEN `action` = "lie" THEN "' . (int)EtsRVActivity::ETS_RV_ACTION_LIKE . '"
                ELSE "' . (int)EtsRVActivity::ETS_RV_ACTION_DISLIKE . '"
            END
    ');

    if ($res) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_activity` CHANGE `type` `type` TINYINT(2) NOT NULL');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_activity` CHANGE `action` `action` TINYINT(2) NOT NULL');
    }

    return true;
}