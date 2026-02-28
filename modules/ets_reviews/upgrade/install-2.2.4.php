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
function upgrade_module_2_2_4($object)
{
    $res = Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_email_queue` ADD `content` TEXT NOT NULL AFTER `subject`;');
    if (file_exists(($file = _PS_CACHE_DIR_ . '/' . $object->name . '/cronjob.log'))) {
        @file_put_contents(_PS_ROOT_DIR_ . '/var/logs/' . $object->name . '.cronjob.log', Tools::file_get_contents($file));
        @unlink($file);
    }
    $res &= $object->unregisterHook('displayPCListImages') ||
        $object->unregisterHook('renderReCaptcha') ||
        $object->unregisterHook('renderTemplateModal') ||
        $object->unregisterHook('renderJavascript') ||
        $object->unregisterHook('renderUploadImage') ||
        $object->unregisterHook('renderProductCommentsList') ||
        $object->unregisterHook('renderProductQuestionsList') ||
        $object->unregisterHook('renderProductCommentModal') ||
        $object->unregisterHook('renderProductQuestionModal') ||
        $object->unregisterHook('displayVerifyPurchase') ||
        $object->unregisterHook('displayAllPhotos') ||
        $object->unregisterHook('displayFrontend');

    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value`= \'' . pSQL($object::_REWRITE_ . '/{id_product_comment}-{id_product}.html') . '\' WHERE `name`=\'PS_ROUTE_module-' . $object->name . '-detail\'');
    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value`= \'' . pSQL('activity/activity-list.html') . '\' WHERE `name`=\'PS_ROUTE_module-' . $object->name . '-activity\'');
    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value`= \'' . pSQL('review/all-reviews.html') . '\' WHERE `name`=\'PS_ROUTE_module-' . $object->name . '-all\'');

    Configuration::deleteByName('PS_ROUTE_module-' . $object->name . '-paginate');
    Configuration::deleteByName('PS_ROUTE_module-' . $object->name . '-all-paginate');
    Configuration::deleteByName('PS_ROUTE_module-' . $object->name . '-activity-paginate');

    return $res;
}