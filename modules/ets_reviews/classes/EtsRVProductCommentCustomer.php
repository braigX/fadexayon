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

class EtsRVProductCommentCustomer extends ObjectModel
{
    const IMAGE_MAX_LENGTH = 500;

    public $firstname;
    public $lastname;
    public $email;
    public $id_customer;
    public $display_name;
    public $avatar;
    public $is_block = 0;
    public $is_staff = 0;

    // Custom:
    public $total_qa_comments;
    public $total_answers;
    public $total_questions;
    public $total_review_replies;
    public $total_review_comments;
    public $total_reviews;
    public $grade;
    public $link;
    public $customer;
    public $customer_name;
    public $id;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_product_comment_customer',
        'primary' => 'id_customer',
        'multilang' => false,
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'display_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'avatar' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 500),
            'is_block' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_staff' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        if ($id) {
            $this->id_customer = $id;
        }
    }

    public static function getAll($context)
    {
        if ($context == null)
            $context = Context::getContext();
        $query = '
            SELECT c.`id_customer` as `id`
                 , CONCAT(c.firstname, " ", c.lastname) as `name`
                 , c.`email`
                 , c.`id_lang`
                 , 0 as `employee` 
            FROM `' . _DB_PREFIX_ . 'customer` c
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` cc ON (cc.`id_customer`=c.`id_customer`)
            WHERE c.`id_shop`=' . (int)$context->shop->id . ' AND (cc.`id_customer` > 0 AND cc.`is_staff`=1)
        ';
        return Db::getInstance()->executeS($query);
    }

    public static function getUserByIdProductComment($id_product_comment)
    {
        if (!Validate::isUnsignedId($id_product_comment))
            return false;

        $id_customer = (int)Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE id_ets_rv_product_comment=' . (int)$id_product_comment);
        if ($id_customer == 0)
            return false;

        return (new self($id_customer));
    }

    public static function isBlockByIdCustomer($id_customer)
    {
        if (!Validate::isUnsignedId($id_customer))
            return false;

        return (int)Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` WHERE is_block=1 AND id_customer=' . (int)$id_customer) > 0;
    }

    public static function getAvatarByIdCustomer($id_customer)
    {
        if (!$id_customer ||
            !Validate::isUnsignedId($id_customer)
        ) {
            return false;
        }
        $cacheId = 'EtsRVProductCommentCustomer::getAvatarByIdCustomer' . md5((int)$id_customer);
        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance()->getValue('SELECT `avatar` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` WHERE id_customer=' . (int)$id_customer);
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }

    public static function searchByName($query, $limit = null)
    {
        $sql = 'SELECT c.*
                FROM `' . _DB_PREFIX_ . 'customer` c
                LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` pcc ON (pcc.id_customer=c.id_customer) 
                WHERE (pcc.id_customer is NULL OR pcc.is_staff = 0)';
        $search_items = explode(' ', $query);
        $research_fields = ['id_customer', 'firstname', 'lastname', 'email'];
        if (Configuration::get('PS_B2B_ENABLE')) {
            $research_fields[] = 'company';
        }

        $items = [];
        foreach ($research_fields as $field) {
            foreach ($search_items as $item) {
                $items[$item][] = 'c.' . $field . ' LIKE \'%' . pSQL($item) . '%\' ';
            }
        }

        foreach ($items as $likes) {
            $sql .= ' AND (' . implode(' OR ', $likes) . ') ';
        }

        $sql .= Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

        if ($limit) {
            $sql .= ' LIMIT 0, ' . (int)$limit;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function itemExist($id)
    {
        if (!$id || !Validate::isUnsignedInt($id))
            return false;

        return Db::getInstance()->getValue('SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` WHERE `id_customer`=' . (int)$id);
    }

    public static function getCustomer($id_customer)
    {
        if (!$id_customer || !Validate::isUnsignedInt($id_customer))
            return false;

        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` WHERE `id_customer`=' . (int)$id_customer);
    }

    public static function saveConfig()
    {
        $customers = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` WHERE `is_staff`=1');
        $mana = [];
        if ($customers) {
            foreach ($customers as $customer)
                $mana[] = (int)$customer['id_customer'];
        }

        return Configuration::updateValue('ETS_RV_MANAGITOR', implode(',', $mana), true);
    }

    public static function updateManagitor($managitor = [], $active = true)
    {
        if (empty($managitor))
            $managitor = trim(Configuration::get('ETS_RV_MANAGITOR'));
        if ($managitor !== '') {
            $managitor = explode(',', $managitor);
            $res = true;
            foreach ($managitor as $id_customer) {
                $res &= Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer`(`id_customer`, `display_name`, `avatar`, `is_block`, `is_staff`) VALUES(' . (int)$id_customer . ', NULL, \'\', 0, 1) ON DUPLICATE KEY UPDATE `is_staff`=' . (int)$active . ';');
            }
            return $res;
        }
        return true;
    }

    public static function isGrandStaff($id_customer)
    {
        if (!$id_customer || !Validate::isUnsignedInt($id_customer))
            return false;
        $cacheId = 'EtsRVProductCommentCustomer::isGrandStaff' . md5((int)$id_customer);
        if (!Cache::isStored($cacheId)) {
            $result = (bool)Db::getInstance()->getValue('
                SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_customer` 
                WHERE `id_customer`=' . (int)$id_customer . ' 
                    AND `is_block`=0 
                    AND `is_staff`=1
            ');
            Cache::store($cacheId, $result);
        } else {
            $result = Cache::retrieve($cacheId);
        }
        return $result;
    }


    public static function getTotal(EtsRVProductCommentCustomer &$userComment = null)
    {
        if ($userComment !== null) {
            // Total reviews of user:
            $userComment->total_reviews = (int)Db::getInstance()->getValue(
                (new DbQuery())
                    ->select('COUNT(id_ets_rv_product_comment)')
                    ->from('ets_rv_product_comment')
                    ->where('IF(`id_customer` > 0, `id_customer` = ' . (int)$userComment->id_customer . ', 0)')
                    ->where('question=0')
            );

            // Total comments of review:
            $userComment->total_review_comments = (int)Db::getInstance()->getValue(
                (new DbQuery())
                    ->select('COUNT(id_ets_rv_comment)')
                    ->from('ets_rv_comment')
                    ->where('id_customer = ' . (int)$userComment->id_customer)
                    ->where('question=0')
                    ->where('answer=0')
            );
            // Total replies of review:
            $userComment->total_review_replies = (int)Db::getInstance()->getValue(
                (new DbQuery())
                    ->select('COUNT(id_ets_rv_reply_comment)')
                    ->from('ets_rv_reply_comment')
                    ->where('id_customer = ' . (int)$userComment->id_customer)
                    ->where('question=0')
            );
            // Total questions of user:
            $userComment->total_questions = (int)Db::getInstance()->getValue(
                (new DbQuery())
                    ->select('COUNT(id_ets_rv_product_comment)')
                    ->from('ets_rv_product_comment')
                    ->where('IF(`id_customer` > 0, `id_customer` = ' . (int)$userComment->id_customer . ', 0)')
                    ->where('question=1')
            );
            // Total answers of question:
            $userComment->total_answers = (int)Db::getInstance()->getValue(
                (new DbQuery())
                    ->select('COUNT(id_ets_rv_comment)')
                    ->from('ets_rv_comment')
                    ->where('id_customer = ' . (int)$userComment->id_customer)
                    ->where('question=1')
                    ->where('answer=1')
            );
            // Total comments of question:
            $userComment->total_qa_comments = (int)Db::getInstance()->getValue(
                    (new DbQuery())
                        ->select('COUNT(id_ets_rv_comment)')
                        ->from('ets_rv_comment')
                        ->where('id_customer = ' . (int)$userComment->id_customer)
                        ->where('question=1')
                        ->where('answer=0')
                ) + (int)Db::getInstance()->getValue(
                    (new DbQuery())
                        ->select('COUNT(id_ets_rv_reply_comment)')
                        ->from('ets_rv_reply_comment')
                        ->where('id_customer = ' . (int)$userComment->id_customer)
                        ->where('question=1')
                );
        }
        return $userComment;
    }

    public static function deleteAll()
    {
        return Db::getInstance()->delete('ets_rv_product_comment_customer', '', false);
    }
}
