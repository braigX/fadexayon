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

class Ets_reviewsImageModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $guid = trim(Tools::getValue('uid'));
        if ($guid !== '') {
            EtsRVTracking::makeIsRead($guid);
        }
        header('Content-Type: image/png');
        $code_binary = call_user_func('base64_decode', 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
        $image = call_user_func('imagecreatefromstring', $code_binary);
        header('Content-Type: image/jpeg');
        call_user_func('imagejpeg', $image);
        call_user_func('imagedestroy', $image);
        ob_end_flush();
        exit();
    }
}