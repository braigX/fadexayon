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
class Ybc_blog_post_employee_class extends ObjectModel
{
    public $id_employee;
    public $name;
    public $is_customer;
    public $avata;
    public $profile_employee;
    public $description;
    public $status;
    public static $definition = array(
		'table' => 'ybc_blog_employee',
		'primary' => 'id_employee_post',
		'multilang' => true,
		'fields' => array(
			'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'name' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500), 
            'is_customer' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'avata' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'status' =>array('type' => self::TYPE_INT),
            'profile_employee' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500), 
            'description' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 9999999),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public function duplicate()
    {
        $this->id = null; 
        $oldImage= $this->avata;
        if($this->avata)
            $this->avata = time().pathinfo($this->avata, PATHINFO_BASENAME);
        if($this->add())
        {
            if($this->avata)
                @copy(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$oldImage,_PS_YBC_BLOG_IMG_DIR_.'avata/'.$this->avata);
            return $this->id;
        }
        return false;        
    }
    public static function getAuthors()
    {
        $admin_authors= Db::getInstance()->executeS(
            'SELECT e.id_employee,e.firstname,e.lastname,ybe.name,ybe.is_customer FROM `'._DB_PREFIX_.'employee` e
                LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` ybe ON (ybe.is_customer =0 AND ybe.id_employee=e.id_employee)
                ');
        if($admin_authors)
        {
            foreach($admin_authors as &$admin_author)
                $admin_author['link'] = Context::getContext()->link->getAdminLink('AdminEmployees').'&id_employee='.(int)$admin_author['id_employee'].'&updateemployee';
        }
        return $admin_authors;
    }
    public static function getIdEmployeePostById($id,$is_customer = true,$active= false)
    {
        return (int)Db::getInstance()->getValue('SELECT id_employee_post FROM `'._DB_PREFIX_.'ybc_blog_employee` WHERE id_employee='.(int)$id.($is_customer ? ' AND is_customer=1':' AND is_customer=0').($active ? ' AND status>0':''));
    }
    public static function getEmployeesFilter($filter = false, $sort = false, $start = false, $limit = false,$having='')
    {
        $sql ="SELECT e.*,CONCAT(e.firstname, ' ', e.lastname) as employee, be.name,bel.description,be.profile_employee,be.avata,pl.name as profile_name,IFNULL(be.status,1) as status,count(bp.id_post) as total_post FROM `"._DB_PREFIX_."employee` e
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` be ON (e.id_employee = be.id_employee AND be.is_customer=0)
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee_lang` bel ON (bel.id_employee_post=be.id_employee_post AND bel.id_lang='".(int)Context::getContext()->language->id."')
        LEFT JOIN `"._DB_PREFIX_."profile` p ON (e.id_profile=p.id_profile)
        LEFT JOIN `"._DB_PREFIX_."profile_lang` pl ON (p.id_profile=pl.id_profile AND pl.id_lang='".(int)Context::getContext()->language->id."')
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_post` bp ON (bp.added_by=e.id_employee AND bp.is_customer=0)
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_shop` bps ON (bps.id_post=bp.id_post AND bps.id_shop='".(int)Context::getContext()->shop->id."')
        WHERE 1 ".($filter ? $filter: '')."
        GROUP BY e.id_employee
        HAVING (1 ".($having ? $having :' ')." )
        ".($sort ?' ORDER BY '.$sort :'').($start !== false && $limit ? " LIMIT ".(int)$start.", ".(int)$limit : "");
        return Db::getInstance()->executeS($sql);
    }
    public static function getCustomersFilter($filter = false, $sort = false, $start = false, $limit = false,$having='')
    {
        $group_author= explode(',',Configuration::get('YBC_BLOG_GROUP_CUSTOMER_AUTHOR'));
        if($group_author)
        {
            $sql ="SELECT c.*,CONCAT(c.firstname, ' ', c.lastname) as customer, be.name,bel.description,be.profile_employee,be.avata,IFNULL(be.status,1) as status,count(bps.id_post) as total_post FROM `"._DB_PREFIX_."customer` c
            INNER JOIN `"._DB_PREFIX_."customer_group` cg ON (cg.id_customer=c.id_customer)
            INNER JOIN `"._DB_PREFIX_."group` g ON (cg.id_group=g.id_group)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` be ON (c.id_customer = be.id_employee AND be.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee_lang` bel ON (bel.id_employee_post=be.id_employee_post AND bel.id_lang='".(int)Context::getContext()->language->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post` bp ON (bp.added_by=c.id_customer AND bp.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_shop` bps ON (bp.id_post=bps.id_post AND bps.id_shop='".(int)Context::getContext()->shop->id."')
            WHERE c.id_shop='".(int)Context::getContext()->shop->id."' AND  g.id_group in (".implode(',',array_map('intval',$group_author)).") ".($filter ? $filter: '')."
            GROUP BY c.id_customer
            HAVING (1 ".($having ? $having :' ')." )
            ".($sort ?' ORDER BY '.$sort :'').($start !== false && $limit ? " LIMIT ".(int)$start.", ".(int)$limit : "");
            return Db::getInstance()->executeS($sql);
        }
        return array();
    }
    public static function countEmployeesFilter($filter,$having='')
    {
        $sql ="SELECT e.*,CONCAT(e.firstname, ' ', e.lastname) as employee, be.name,bel.description,be.profile_employee,be.avata,pl.name as profile_name,count(bp.id_post) as total_post FROM `"._DB_PREFIX_."employee` e
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` be ON (e.id_employee = be.id_employee AND be.is_customer=0)
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee_lang` bel ON (bel.id_employee_post=be.id_employee_post AND bel.id_lang='".(int)Context::getContext()->language->id."')
        LEFT JOIN `"._DB_PREFIX_."profile` p ON (e.id_profile=p.id_profile)
        LEFT JOIN `"._DB_PREFIX_."profile_lang` pl ON (p.id_profile=pl.id_profile AND pl.id_lang='".(int)Context::getContext()->language->id."')
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_post` bp ON (bp.added_by=e.id_employee AND bp.is_customer=0)
        WHERE 1 ".($filter ? $filter: '')."
        GROUP BY e.id_employee
        HAVING (1 ".($having ? $having :' ').")";
        return count(Db::getInstance()->executeS($sql));
    }
    public static function countCustomersFilter($filter,$having='')
    {
        $group_author= explode(',',Configuration::get('YBC_BLOG_GROUP_CUSTOMER_AUTHOR'));
        if($group_author)
        {
            $sql ="SELECT c.*,CONCAT(c.firstname, ' ', c.lastname) as customer, be.name,bel.description,be.profile_employee,be.avata,count(bp.id_post) as total_post FROM `"._DB_PREFIX_."customer` c
            INNER JOIN `"._DB_PREFIX_."customer_group` cg ON (cg.id_customer=c.id_customer)
            INNER JOIN `"._DB_PREFIX_."group` g ON (cg.id_group=g.id_group)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` be ON (c.id_customer = be.id_employee AND be.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee_lang` bel ON (bel.id_employee_post=be.id_employee_post AND bel.id_lang='".(int)Context::getContext()->language->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post` bp ON (bp.added_by=c.id_customer AND bp.is_customer=1)
            WHERE c.id_shop = '".(int)Context::getContext()->shop->id."' AND g.id_group in (".implode(',',array_map('intval',$group_author)).") ".($filter ? $filter: '')."
            GROUP BY c.id_customer
            HAVING (1 ".($having ? $having :' ')." )";
            return count(Db::getInstance()->executeS($sql));
        }
        return 0;
    }
    public static function getAuthorById($id_author,$is_customer=0)
    {
        if($is_customer)
        {
            $author= Db::getInstance()->getRow('SELECT *,c.id_customer FROM `'._DB_PREFIX_.'customer` c
            LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (c.id_customer=be.id_employee AND be.is_customer=1)
            LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post=bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE c.id_customer = '.(int)$id_author);
        }
        else
            $author= Db::getInstance()->getRow('SELECT *,e.id_employee FROM `'._DB_PREFIX_.'employee` e
            LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (e.id_employee=be.id_employee AND be.is_customer=0)
            LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post=bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE e.id_employee = '.(int)$id_author);
        $params=array();
        $params['id_author'] = $id_author;
        $params['is_customer']=$is_customer;
        if($author)
        {
            if(!$author['name'])
                $author['name']=trim(Tools::strtolower($author['firstname'].' '.$author['lastname']));
            $params['alias'] = str_replace(' ','-',$author['name']);
            $author['alias'] = $params['alias'];
            $author['author_link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',$params);
            if(!$author['avata'])
                $author['avata']=  (Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') ? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png');
        }

        return $author;
    }
    public static function checkPermistionPost($id_post=0,$permistion='')
    {
        $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById(Context::getContext()->customer->id,true);
        if(!$id_employee_post || (($employeePost = new Ybc_blog_post_employee_class($id_employee_post)) && $employeePost->status==1) )
        {
            if(($privileges = explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'))) && in_array($permistion,$privileges))
            {

                if($permistion=='edit_blog' || $permistion=='delete_blog')
                    return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE id_post="'.(int)$id_post.'" AND added_by="'.(int)Context::getContext()->customer->id.'" AND is_customer=1');
                else
                    return true;

            }
        }
        return false;
    }
    public static function checkPermisionComment($action='edit',$id_comment=0,$tabmanagament='comment')
    {
        if(!isset(Context::getContext()->customer) || !Context::getContext()->customer->isLogged())
            return false;
        $privileges = explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'));
        $ok=true;
        if(!$privileges || !self::checkGroupAuthor())
            $ok=false ;
        elseif(self::checkGroupAuthor())
        {
            if($tabmanagament=='comment' || $action!='')
            {
                if($action=='reply' && !in_array('reply_comments',$privileges))
                    $ok=false;
                elseif(!in_array('manage_comments',$privileges) && $action!='reply')
                    $ok=false;
                else
                {
                    $sql ='SELECT p.id_post FROM `'._DB_PREFIX_.'ybc_blog_post` p
                    INNER JOIN `'._DB_PREFIX_.'ybc_blog_comment` c ON  p.id_post=c.id_post
                    WHERE c.id_comment="'.(int)$id_comment.'" AND p.added_by="'.(int)Context::getContext()->customer->id.'" AND p.is_customer=1';
                    $ok= Db::getInstance()->getValue($sql);
                }
            }
        }
        if(!$ok && Db::getInstance()->getValue('SELECT id_comment FROM `'._DB_PREFIX_.'ybc_blog_comment` WHERE id_user="'.(int)Context::getContext()->customer->id.'" AND id_comment='.(int)$id_comment))
        {
            if ($action=='edit' && Configuration::get('YBC_BLOG_ALLOW_EDIT_COMMENT'))
                $ok=true;
            if ($action=='delete')
                $ok=true;
            if($action=='reply' && Configuration::get('YBC_BLOG_ALLOW_REPLY_COMMENT'))
                $ok=true;
        }
        elseif($action=='reply' && Configuration::get('YBC_BLOG_ALLOW_REPLY_COMMENT'))
            $ok=true;
        return $ok;
    }
    public static function checkGroupAuthor()
    {
        if(!Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR'))
            return false;
        $context = Context::getContext();
        if(isset($context->customer) && $context->customer->isLogged())
        {
            if($context->customer->id && ($authorGroups= explode(',',Configuration::get('YBC_BLOG_GROUP_CUSTOMER_AUTHOR'))))
            {
                $groups = Customer::getGroupsStatic($context->customer->id);
                if($groups)
                {
                    $exc_groups = array(Configuration::get('PS_UNIDENTIFIED_GROUP'),Configuration::get('PS_GUEST_GROUP'));
                    foreach($groups as $group)
                    {
                        if(in_array($group,$authorGroups) && !in_array($group,$exc_groups))
                        {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    public static function getCountAuthor($active=true)
    {
        $sql ='SELECT COUNT(DISTINCT p.added_by,p.is_customer) FROM `'._DB_PREFIX_.'ybc_blog_post` p
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post =ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
                LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=p.added_by)
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer =p.added_by)
                WHERE 1 '.($active ? ' AND p.enabled=1':'').' AND ((e.id_employee!=0 AND p.is_customer=0) OR (c.id_customer!=0 AND p.is_customer=1))';
        return Db::getInstance()->getValue($sql);
    }
    public static function getListAuthorPost($start,$limit=false)
    {
        $sql ='SELECT COUNT(p.id_post) as total_post, p.added_by,p.is_customer,CONCAT(e.firstname," ",e.lastname) as employee_name,CONCAT(c.firstname," ",c.lastname) as customer_name FROM `'._DB_PREFIX_.'ybc_blog_post` p
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post =ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
                LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=p.added_by)
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer =p.added_by)
                WHERE p.enabled=1 AND ((e.id_employee!=0 AND p.is_customer=0) OR (c.id_customer!=0 AND p.is_customer=1))
                GROUP BY p.added_by,p.is_customer ORDER BY total_post DESC limit '.(int)$start.','.(int)$limit;
        return Db::getInstance()->executeS($sql);
    }
    public static function getInformationByID($id,$is_customer = true)
    {
        if($is_customer)
        {
            return Db::getInstance()->getRow('
                    SELECT * FROM `'._DB_PREFIX_.'customer` c
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (be.id_employee=c.id_customer AND be.is_customer=1)
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post=bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'")
                    WHERE c.id_customer="'.(int)$id.'"');
        }
        else
        {
            return Db::getInstance()->getRow('
                    SELECT * FROM `'._DB_PREFIX_.'employee` e
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (be.id_employee=e.id_employee AND be.is_customer=0)
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post=bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'")
                    WHERE e.id_employee="'.(int)$id.'"');
        }
    }
    public static function getPosts($id,$is_customer= 1)
    {
        $sql ='SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post` p
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
                LEFT JOIN `'._DB_PREFIX_.'ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang="'.(int)Context::getContext()->language->id.'")
                WHERE p.enabled = 1 AND  p.added_by ="'.(int)$id.'" AND p.is_customer="'.(int)$is_customer.'"';
        return Db::getInstance()->executeS($sql);
    }

}