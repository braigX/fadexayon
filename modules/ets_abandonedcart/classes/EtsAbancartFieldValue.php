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


class EtsAbancartFieldValue extends EtsAbancartCache
{
    public $id_ets_abancart_form_submit;
    public $id_ets_abancart_field;
    public $value;
    public $file_name;

    public static $definition = array(
        'table' => 'ets_abancart_field_value',
        'primary' => 'id_ets_abancart_field_value',
        'fields' => array(
            'id_ets_abancart_form_submit' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_ets_abancart_field' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'value' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'file_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
        )
    );

    public static function getFieldValueByIdForm($id_form)
    {
        return Db::getInstance()->executeS("
            SELECT fv.*, f.display_column, f.type FROM `" . _DB_PREFIX_ . "ets_abancart_field_value` fv 
            JOIN `" . _DB_PREFIX_ . "ets_abancart_field` f ON f.id_ets_abancart_field=fv.id_ets_abancart_field
            WHERE f.id_ets_abancart_form=" . (int)$id_form);
    }

    public static function getMaxIdSubmit()
    {
        return (int)Db::getInstance()->getValue("SELECT MAX(id_submit) FROM `" . _DB_PREFIX_ . "ets_abancart_field_value`");
    }

    public function add($auto_date = true, $null_values = false)
    {
        if ($res = parent::add($auto_date, $null_values)) {
            $this->clearCacheBoSmarty('*', 'lead_form_list');
            $this->clearCacheAllSmarty('*', 'lead_form_short_code');
        }
        return $res;
    }

    public function update($null_values = false)
    {
        if ($res = parent::update($null_values)) {
            $this->clearCacheBoSmarty('*', 'lead_form_list');
            $this->clearCacheAllSmarty('*', 'lead_form_short_code');
        }
        return $res;
    }

    public function delete()
    {
        if ($res = parent::delete()) {
            $this->clearCacheBoSmarty('*', 'lead_form_list');
            $this->clearCacheAllSmarty('*', 'lead_form_short_code');
        }
        return $res;
    }
}