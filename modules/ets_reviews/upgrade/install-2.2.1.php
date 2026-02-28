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

/**
 * @return bool
 * @var Ets_reviews $object
 */
function upgrade_module_2_2_1($object)
{
    $object->registerHook('displayAllPhotos');
    $object->registerHook('displayHome');
    Configuration::get('ETS_RV_DISPLAY_ALL_PHOTO', 0);
    Configuration::get('ETS_RV_DISPLAY_ON_HOME', 0);
    Configuration::get('ETS_RV_NUMBER_OF_LAST_REVIEWS', 8);

    if ((int)Db::getInstance()->getValue('SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name`=\'AdminEtsRVUnsubscribe\'') <= 0) {
        $parentId = (int)Db::getInstance()->getValue('SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name`=\'AdminEtsRVEmail\'');
        $object->addQuickTab($parentId, 'Unsubscribe', 'Unsubscribe list');
    }

    return true;
}