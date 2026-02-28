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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ets_abandonedcartUrlModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $params = trim(Tools::getValue('_ab'));
        $params = json_decode(EtsAbancartTools::getInstance()->decrypt($params), true);
        if (!empty($params['params'])) {
            $newParams = $params['params'];
            if (!is_array($params['params'])) {
                $newParams = json_decode($params['params'], true);
            }
            $id_ets_abancart_reminder = isset($newParams['id_ets_abancart_reminder']) ? (int)$newParams['id_ets_abancart_reminder'] : 0;
            $id_ets_abancart_campaign = isset($newParams['id_ets_abancart_campaign']) ? (int)$newParams['id_ets_abancart_campaign'] : 0;
            $id_cart = isset($newParams['id_cart']) ? (int)$newParams['id_cart'] : 0;
            $id_customer = isset($newParams['id_customer']) ? (int)$newParams['id_customer'] : 0;
            $email = isset($newParams['email']) ? trim($newParams['email']) : null;

            if ($id_ets_abancart_reminder > 0 && Validate::isUnsignedInt($id_ets_abancart_reminder)) {
                EtsAbancartReminder::doTrackingMailClick($id_ets_abancart_reminder, $id_ets_abancart_campaign, $id_cart, $id_customer, $email);
            }
        }
        if (!empty($params['url']))
            Tools::redirectLink($params['url']);
        exit;
    }
}