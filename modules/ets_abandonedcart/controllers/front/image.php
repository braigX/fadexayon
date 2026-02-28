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


class Ets_abandonedcartImageModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $params = json_decode(EtsAbancartTools::getInstance()->decrypt(Tools::getValue('rewrite')), true);
        $id_ets_abancart_reminder = isset($params['id_ets_abancart_reminder']) ? (int)$params['id_ets_abancart_reminder'] : (int)Tools::getValue('r');
        $id_cart = isset($params['id_cart']) ? (int)$params['id_cart'] : (int)Tools::getValue('c', 0);
        $id_customer = isset($params['id_customer']) ? (int)$params['id_customer'] : (int)Tools::getValue('cus', 0);
        $email = isset($params['email']) ? trim($params['email']) : trim(Tools::getValue('email', ''));

        if ($id_ets_abancart_reminder > 0 && Validate::isUnsignedInt($id_ets_abancart_reminder)) {
            EtsAbancartReminder::doTrackingMailRead($id_ets_abancart_reminder, $id_cart, $id_customer, $email);
        }

        $code_binary = call_user_func('base64_decode', 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
        $image = call_user_func('imagecreatefromstring', $code_binary);
        header('Content-Type: image/jpeg');
        call_user_func('imagejpeg', $image);
        call_user_func('imagedestroy', $image);
        ob_end_flush();
        exit();
    }
}