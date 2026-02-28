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
class Ybc_blog_comment_class extends ObjectModel
{
    public $id_comment;
    public $id_user;
    public $name;
    public $email;
    public $id_post;
    public $subject;
    public $comment;
    public $reply;
    public $customer_reply;
	public $approved;
	public $datetime_added;
	public $reported;
    public $rating;
    public $viewed;
    public $replied_by;
    public static $definition = array(
		'table' => 'ybc_blog_comment',
		'primary' => 'id_comment',
		'multilang' => false,
		'fields' => array(			
            'id_comment' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'replied_by' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'customer_reply'=> array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_user' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 5000),
            'email' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 5000),
            'rating' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_post' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'approved' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'reported' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'subject' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 5000),
            'comment' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 99000),
            'reply' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 99000),
            'viewed' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'datetime_added' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),  
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
	public function add($auto_date = true,$null_values= false)
    {
        if(parent::add($auto_date,$null_values))
        {
            /** @var Ybc_blog $blog */
            $blog = Module::getInstanceByName('ybc_blog');
            $blog->_clearCache('comment_block.tpl');
            return true;
        }
    }
    public function update($null_values = false)
    {
        if(parent::update($null_values))
        {
            /** @var Ybc_blog $blog */
            $blog = Module::getInstanceByName('ybc_blog');
            $blog->_clearCache('comment_block.tpl');
            return true;
        }
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
    public static function getCommentsWithFilter($filter = false, $sort = false, $start = false, $limit = false,$fontend=true)
    {
        $req = "SELECT bc.*,pl.description,pl.short_description,pl.thumb,pl.title
            FROM `"._DB_PREFIX_."ybc_blog_comment` bc
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps on (bc.id_post=ps.id_post)
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=ps.id_post)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang='".(int)Context::getContext()->language->id."')
            LEFT JOIN `"._DB_PREFIX_."customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
            WHERE ".($fontend ? "(ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.enabled=1 AND ":"")." ps.id_shop=".(int)Context::getContext()->shop->id." ".($filter ? $filter : '')."
            GROUP BY bc.id_comment
            ORDER BY ".($sort ? $sort : '')." bc.id_comment desc " . ($start !== false && $limit ? " LIMIT ".(int)$start.", ".(int)$limit : "");
        $comments= Db::getInstance()->executeS($req);
        if($comments)
        {
            /** @var Ybc_blog $module */
            $module = Module::getInstanceByName('ybc_blog');
            foreach($comments as &$comment)
            {
                if($comment['customer_reply']==1)
                {
                    $customer= Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE id_shop="'.(int)Context::getContext()->shop->id.'" AND  id_customer='.(int)$comment['replied_by']);
                    $comment['efirstname']= $customer['firstname'];
                    $comment['elastname']= $customer['lastname'];
                }
                else
                {
                    $employee= Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'employee` WHERE id_employee='.(int)$comment['replied_by']);
                    $comment['efirstname']= isset($employee['firstname']) ? $employee['firstname'] :'';
                    $comment['elastname']= isset($employee['lastname']) ? $employee['lastname'] : '' ;
                }
                if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment['id_comment']))
                    $comment['url_edit'] = $module->getLink('blog',array('id_post'=>$comment['id_post'],'edit_comment'=>$comment['id_comment']));
                if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$comment['id_comment']))
                    $comment['url_delete'] = Context::getContext()->link->getModuleLink('ybc_blog','managementcomments',array('deletecomment'=>1,'id_comment'=>$comment['id_comment']));
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'ybc_blog_reply` WHERE id_comment='.(int)$comment['id_comment'].' AND approved=1';
                $comment['replies'] = Db::getInstance()->executeS($sql);
                if($comment['replies'])
                {
                    foreach($comment['replies'] as &$reply)
                    {
                        $reply['reply']= str_replace("\n",'<'.'b'.'r'.'>',$reply['reply']);
                        if($reply['id_employee'])
                        {
                            if($name= Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.'ybc_blog_employee` WHERE id_employee="'.(int)$reply['id_employee'].'" AND is_customer=0'))
                                $reply['name']= $name;
                            elseif($name = Db::getInstance()->getValue('SELECT CONCAT(firstname," ",lastname) FROM `'._DB_PREFIX_.'employee` WHERE id_employee='.(int)$reply['id_employee']))
                                $reply['name']= $name;
                        }
                        if($reply['id_user'])
                        {
                            if($name= Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.'ybc_blog_employee` WHERE id_employee="'.(int)$reply['id_user'].'" AND is_customer=1'))
                                $reply['name']= $name;
                            elseif($name = Db::getInstance()->getValue('SELECT CONCAT(firstname," ",lastname) FROM `'._DB_PREFIX_.'customer` WHERE id_customer='.(int)$reply['id_user']))
                                $reply['name']= $name;
                        }
                    }

                }
                $comment['comment'] = str_replace("\n",'<'.'b'.'r'.'>',$comment['comment']);
            }
        }
        return $comments;
    }
    public static function countCommentsWithFilter($filter = false,$fontend=true)
    {
        $req = "SELECT COUNT(bc.id_comment) 
            FROM `"._DB_PREFIX_."ybc_blog_comment` bc
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps on (bc.id_post=ps.id_post)
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=ps.id_post)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang='".(int)Context::getContext()->language->id."')
            LEFT JOIN `"._DB_PREFIX_."customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
            WHERE ".($fontend ? "(ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.enabled=1 AND ":"")."  ps.id_shop=".(int)Context::getContext()->shop->id." ".($filter ? $filter : '');
         return (int)Db::getInstance()->getValue($req);
    }
    public static function getRepliesByIdComment($id_comment)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_reply` WHERE id_comment='.(int)$id_comment);
    }
    public static function deleteReply($id_reply)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ybc_blog_reply` WHERE id_reply='.(int)$id_reply);
    }
    public static function updateApprovedReply($id_reply,$approved)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_reply` SET `approved`='.(int)$approved.' WHERE id_reply='.(int)$id_reply);
    }
    public static function getCommentById($id_comment)
    {
        return Db::getInstance()->getRow("SELECT bc.*, e.firstname as efirstname, e.lastname as elastname,pl.title as post_title
            FROM `"._DB_PREFIX_."ybc_blog_comment` bc
            LEFT JOIN `"._DB_PREFIX_."employee` e ON e.id_employee = bc.replied_by
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON bc.id_post = pl.id_post AND pl.id_lang=".(int)Context::getContext()->language->id."
            WHERE bc.id_comment=".(int)$id_comment."
            ");
    }
    public static function checkCustomerIsLikePost($id_post)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_log_like` WHERE id_customer="'.(int)Context::getContext()->customer->id.'" AND id_post="'.(int)$id_post.'"');
    }
}