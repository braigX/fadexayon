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

class EtsAbancartNoteManual
{
    public static function writeNote($id_cart, $content)
    {
        if (!$id_cart || !Validate::isUnsignedInt($id_cart)) {
            return false;
        }
        return (bool)Db::getInstance()->insert('ets_abancart_note_manual', ['id_cart' => $id_cart, 'content' => pSQL($content, true)], false, false, Db::ON_DUPLICATE_KEY);
    }

    public static function getNote($id_cart)
    {
        if (!$id_cart || !Validate::isUnsignedInt($id_cart)) {
            return '';
        }
        return Db::getInstance()->getValue('SELECT `content` FROM `' . _DB_PREFIX_ . 'ets_abancart_note_manual` WHERE `id_cart`=' . (int)$id_cart);
    }
}