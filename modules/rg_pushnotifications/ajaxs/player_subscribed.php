<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

require_once dirname(__FILE__) . '../../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../rg_pushnotifications.php';

$module = Module::getInstanceByName('rg_pushnotifications');

if ($module->active && Tools::getValue('token') == $module->secure_key) {
    if ($id_player = Tools::getValue('id_player')) {
        $notification = $module->setNewSubcriber($id_player);

        die(1);
    }
}

die(0);
