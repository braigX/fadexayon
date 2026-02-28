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

if (!defined('_PS_VERSION_'))
	exit;
class Ybc_blog_polls_class extends ObjectModel
{
    public $id_user;
    public $name;
    public $email;
    public $id_post;
    public $feedback;
    public $polls;
    public $dateadd;
    public static $definition = array(
		'table' => 'ybc_blog_polls',
		'primary' => 'id_polls',
		'multilang' => false,
		'fields' => array(			
            'id_user' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 5000),
            'email' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 5000),
            'id_post' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'polls' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'feedback' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 99000),
            'dateadd' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),  
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public function duplicate()
    {
        $this->id = null; 
        if($this->add())
        {
            return $this->id;
        }
        return false;        
    }
    public static function getPollsWithFilter($filter = false, $sort = false, $start = false, $limit = false,$fontend=true)
    {
        $req = "SELECT po.*,pl.description,pl.short_description,pl.thumb,pl.title
            FROM `"._DB_PREFIX_."ybc_blog_polls` po
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps on (po.id_post=ps.id_post)
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=ps.id_post)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang='".(int)Context::getContext()->language->id."')
            LEFT JOIN `"._DB_PREFIX_."customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
            WHERE ".($fontend ? "(ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.enabled=1 AND ":"")." ps.id_shop=".(int)Context::getContext()->shop->id." ".($filter ? $filter : '')."
            ORDER BY ".($sort ? $sort : '')." po.id_polls desc " . ($start !== false && $limit ? " LIMIT ".(int)$start.", ".(int)$limit : "");
        return  Db::getInstance()->executeS($req);
    }
    public static function countPollsWithFilter($filter,$fontend=true)
    {
        $req = "SELECT count(*)
            FROM `"._DB_PREFIX_."ybc_blog_polls` po
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps on (po.id_post=ps.id_post)
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=ps.id_post)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang='".(int)Context::getContext()->language->id."')
            LEFT JOIN `"._DB_PREFIX_."customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
            WHERE ".($fontend ? "(ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.enabled=1 AND ":"")." ps.id_shop=".(int)Context::getContext()->shop->id." ".($filter ? $filter : '');
        return  Db::getInstance()->getValue($req);
    }
    public static function getIDPolls($id_post,$id_customer)
    {
        return Db::getInstance()->getValue('SELECT id_polls FROM `'._DB_PREFIX_.'ybc_blog_polls` WHERE id_post="'.(int)$id_post.'" AND id_user='.(int)$id_customer);
    }
}