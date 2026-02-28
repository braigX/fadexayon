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


class EtsAbancartFormSubmit extends ObjectModel
{
    public $id_ets_abancart_form;
    public $id_ets_abancart_reminder;
    public $id_customer;
    public $id_lang;
    public $id_currency;
    public $id_country;
    public $id_cart;
    public $is_leaving_website;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'ets_abancart_form_submit',
        'primary' => 'id_ets_abancart_form_submit',
        'fields' => array(
            'id_ets_abancart_form' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_ets_abancart_reminder' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'is_leaving_website' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'date_upd' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
        )
    );

    public static function getFormSubmitData($idForm, $start = 0, $limit = 50)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from('ets_abancart_form_submit')
            ->where('id_ets_abancart_form = ' . (int)$idForm);
        if ($limit > 0) {
            $query->limit($limit, $start);
        }

        $formSubmits = Db::getInstance()->executeS($query);

        foreach ($formSubmits as &$item) {
            $fieldValuesQuery = new DbQuery();
            $fieldValuesQuery->select('*')
                ->from('ets_abancart_field_value')
                ->where('id_ets_abancart_form_submit = ' . (int)$item['id_ets_abancart_form_submit']);

            $item['field_values'] = Db::getInstance()->executeS($fieldValuesQuery);
        }

        if (!empty($formSubmits)) {
            unset($item); // unset $item to avoid accidental usage later
        }

        return $formSubmits;
    }


    public static function getTotalFormSubmit($idForm)
    {
        $query = new DbQuery();
        $query->select('COUNT(*)')
            ->from('ets_abancart_form_submit')
            ->where('id_ets_abancart_form = ' . (int)$idForm);

        return (int)Db::getInstance()->getValue($query);
    }
}