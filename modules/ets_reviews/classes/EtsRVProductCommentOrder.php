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

class EtsRVProductCommentOrder extends ObjectModel
{
    public $id_ets_rv_product_comment;
    public $id_order;
    public $id_product;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_product_comment_order',
        'primary' => '',
        'multilang' => false,
        'fields' => array(
            'id_ets_rv_product_comment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_product' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 500),
        ),
    );

    public static function getOldestOrder($id_product)
    {
        if (!$id_product || !Validate::isUnsignedInt($id_product)) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('o.id_order')
            ->from('orders', 'o')
            ->leftJoin('order_detail', 'od', 'od.id_order = o.id_order')
            ->leftJoin('ets_rv_product_comment_order', 'pco', 'pco.id_order = o.id_order AND pco.id_product = od.product_id')
            ->where('od.product_id = ' . (int)$id_product)
            ->where('pco.id_product is NULL OR pco.id_product <= 0');

        return (int)Db::getInstance()->getValue($dq);
    }

    public static function getReviewed($id_order, $id_product)
    {
        if (!$id_product ||
            !Validate::isUnsignedInt($id_product) ||
            !$id_order ||
            !Validate::isUnsignedInt($id_order)
        ) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('id_ets_rv_product_comment')
            ->from('ets_rv_product_comment_order', 'pco')
            ->where('pco.id_product = ' . (int)$id_product)
            ->where('pco.id_order = ' . (int)$id_order);

        return (int)Db::getInstance()->getValue($dq);
    }

    public static function saveData($multi_data = array())
    {
        if (!is_array($multi_data) || !count($multi_data)) {
            return false;
        }
        return Db::getInstance()->insert(self::$definition['table'], $multi_data, true, true, Db::INSERT_IGNORE);
    }

    public static function deleteReviewed($product_comment_id)
    {
        if (!$product_comment_id || !Validate::isUnsignedInt($product_comment_id)) {
            return false;
        }

        return Db::getInstance()->delete('ets_rv_product_comment_order', 'id_ets_rv_product_comment=' . (int)$product_comment_id);
    }
}