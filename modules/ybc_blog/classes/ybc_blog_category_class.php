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
class Ybc_blog_category_class extends ObjectModel
{
    public $id_category;
    public $id_shop;
    public $id_parent;
    public $title;
    public $meta_title;
    public $description;
    public $meta_description;
    public $meta_keywords;
	public $enabled;
	public $url_alias;
	public $image;
    public $thumb;
    public $sort_order;
    public $datetime_added;
    public $datetime_modified;
    public $added_by;
    public $modified_by;
    public static $definition = array(
		'table' => 'ybc_blog_category',
		'primary' => 'id_category',
		'multilang' => true,
		'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT),
			'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'sort_order' => array('type' => self::TYPE_INT),
            'added_by' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'modified_by' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'image' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500,'lang' => true), 
            'thumb' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500,'lang' => true),             
            'datetime_added' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'datetime_modified' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            // Lang fields
            'url_alias' =>	array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 500,),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 700),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml', 'size' => 700),   
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 700),         
			'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml',  'size' => 700),			
            'description' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 900000),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        if($this->id)
            $this->id_shop = Db::getInstance()->getValue('SELECT id_shop FROM `'._DB_PREFIX_.'ybc_blog_category_shop` where id_category= '.(int)$this->id);
	}
    public function add($autodate = true, $null_values = false)
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$res = parent::add($autodate, $null_values);
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'ybc_blog_category_shop` (`id_shop`, `id_category`)
			VALUES('.(int)$id_shop.', '.(int)$this->id.')'
		);
		return $res;
	}
    public function delete()
    {
       if(parent::delete())
       {
           if($this->image)
           {
               foreach($this->image as $image)
                   if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$image))
                       @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$image);
           }
           if($this->thumb)
           {
               foreach($this->thumb as $thumb)
                   if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$thumb))
                       @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$thumb);
           }
           Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_category` SET id_parent="'.(int)$this->id_parent.'" WHERE id_parent="'.(int)$this->id.'"');
           $posts = Ybc_blog_post_class::getPostsByIdCategory($this->id);
           if($posts)
           {
               foreach($posts as $post)
               {
                   $categories = self::getCategoriesByIdPost($post['id_post']);
                   if(count($categories) <= 1)
                   {
                       Ybc_blog_post_class::_deletePost($post['id_post']);
                   }
               }
           }
           $req ="DELETE FROM `"._DB_PREFIX_."ybc_blog_category_shop` WHERE id_category=".(int)$this->id;
           Db::getInstance()->execute($req);
           $categories = Db::getInstance()->executeS('SELECT c.id_category FROM `'._DB_PREFIX_.'ybc_blog_category` c
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_category_shop` cs ON (c.id_category=cs.id_category)
                WHERE cs.id_shop= "'.(int)Context::getContext()->shop->id.'" AND c.id_parent='.(int)$this->id_parent.' ORDER BY c.sort_order ASC');
           if($categories)
           {
               foreach($categories as $key=> $category)
               {
                   $position =$key+1;
                   Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_post_category` SET position="'.(int)$position.'" WHERE id_category='.(int)$category['id_category']);
               }
           }
           return true;
       }
       return false;
    }
    public static function getCategoriesByIdPost($id_post, $id_lang = false, $enabled = false)
    {
        if(!$id_lang)
            $id_lang = Context::getContext()->language->id;
        $req = "SELECT c.*, cl.* 
            FROM `"._DB_PREFIX_."ybc_blog_category` c
            INNER JOIN `"._DB_PREFIX_."ybc_blog_category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop ='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_lang` cl ON c.id_category = cl.id_category AND cl.id_lang=".(int)$id_lang."
            WHERE c.id_category IN (SELECT id_category FROM `"._DB_PREFIX_."ybc_blog_post_category` WHERE id_post = ".(int)$id_post.")
            ".($enabled ? " AND c.enabled = 1" : '');
        $categories = Db::getInstance()->executeS($req);
        if($categories)
        {
            foreach($categories as &$cat)
                $cat['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_category' => $cat['id_category']));
        }
        return $categories;
    }
    public function duplicate()
    {
        $this->id = null; 
        $oldImages= $this->image;
        $oldThumbs = $this->thumb;
        if($this->image)
        {
            foreach($this->image as $id_lang=>$image)
            {
                if($image)
                    $this->image[$id_lang] = time().pathinfo($image, PATHINFO_BASENAME);
            }
        }
        if($this->thumb)
        {
            foreach($this->thumb as $id_lang=>$thumb)
            {
                if($thumb)
                    $this->thumb[$id_lang] = time().pathinfo($thumb,PATHINFO_BASENAME);
            }
        }    
        if($this->add())
        {
            if($this->image)
            {
                foreach($this->image as $id_lang=>$image)
                {
                    if($image)
                        @copy(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImages[$id_lang],_PS_YBC_BLOG_IMG_DIR_.'category/'.$image);
                }
            }
            if($this->thumb)
            {
                foreach($this->thumb as $id_lang=>$thumb)
                {
                    if($thumb)
                        @copy(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$oldThumbs[$id_lang],_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$thumb);
                }
            }
            return $this->id;
        }
        return false;        
    }
    public static function getCategories($id_category=0)
    {
        $req = "SELECT c.*, cl.*
            FROM `"._DB_PREFIX_."ybc_blog_category` c
            INNER JOIN `"._DB_PREFIX_."ybc_blog_category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_lang` cl ON c.id_category = cl.id_category
            WHERE cl.id_lang = ".(int)Context::getContext()->language->id.($id_category ? ' AND c.id_category<"'.(int)$id_category.'"':'');
        return Db::getInstance()->executeS($req);
    }
    public static function getCategoriesWithFilter($filter = false, $sort = false, $start = false, $limit = false,$id_parent=false)
    {
        $req = "SELECT c.*, cl.*
            FROM `"._DB_PREFIX_."ybc_blog_category` c
            INNER JOIN `"._DB_PREFIX_."ybc_blog_category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_lang` cl ON c.id_category = cl.id_category AND cl.id_lang = ".(int)Context::getContext()->language->id."
            WHERE ".($id_parent!==false ? 'c.id_parent = '.(int)$id_parent:'1').($filter ? $filter : '')." 
            ORDER BY ".($sort ? $sort : '')." c.id_category desc " . ($start !== false && $limit ? " LIMIT ".(int)$start.", ".(int)$limit : "");
        return Db::getInstance()->executeS($req);
    }
    public static function countCategoriesWithFilter($filter,$id_parent=0)
    {
        $req = "SELECT count(c.id_category)
            FROM `"._DB_PREFIX_."ybc_blog_category` c
            INNER JOIN `"._DB_PREFIX_."ybc_blog_category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_lang` cl ON c.id_category = cl.id_category
            WHERE c.id_parent='".(int)$id_parent."' AND  cl.id_lang = ".(int)Context::getContext()->language->id.($filter ? $filter : '');
        return  Db::getInstance()->getValue($req);
    }
    public static function getCategoryById($id_category, $id_lang = false)
    {
        if(!$id_lang)
            $id_lang = (int)Context::getContext()->language->id;
        $req = "SELECT c.*, cl.*
            FROM `"._DB_PREFIX_."ybc_blog_category` c
            INNER JOIN `"._DB_PREFIX_."ybc_blog_category_shop` cs ON (c.id_category =cs.id_category AND cs.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_lang` cl ON c.id_category = cl.id_category
            WHERE cl.id_lang = ".(int)$id_lang." AND c.id_category=".(int)$id_category;
        return Db::getInstance()->getRow($req);
    }
    public static function getCategoryAlias($id_category,$id_lang=0)
    {
        if(!$id_lang)
            $id_lang = Context::getContext()->language->id;
        $req = "SELECT cl.url_alias
            FROM `"._DB_PREFIX_."ybc_blog_category_lang` cl
            WHERE cl.id_category = ".(int)$id_category.' AND cl.id_lang='.(int)$id_lang;
        $row = Db::getInstance()->getRow($req);
        if(isset($row['url_alias']))
            return $row['url_alias'];
        return false;
    }
    public static function getIDCategoryByUrlAlias($url_alias,$id_lang=0)
    {
        $sql = 'SELECT cs.id_category FROM `'._DB_PREFIX_.'ybc_blog_category` c
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_category_lang` cl ON (c.id_category = cl.id_category'.($id_lang ? ' AND cl.id_lang="'.(int)$id_lang.'"':'').')
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_category_shop` cs ON (cs.id_category = c.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE  cl.url_alias ="'.pSQL($url_alias).'"';
        return (int)Db::getInstance()->getValue($sql);
    }
    public static function getChildrenBlogCategories($id_parent, $active=true, $id_lang=null,$id_category=0)
    {
        if(!$id_lang)
            $id_lang = (int)Context::getContext()->language->id;
        $sql = "SELECT c.id_category, cl.title,cl.image,cl.thumb
                FROM `"._DB_PREFIX_."ybc_blog_category` c
                LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_shop` cs ON (c.id_category=cs.id_category)
                LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_lang` cl ON c.id_category = cl.id_category AND cl.id_lang = ".(int)$id_lang."
                WHERE c.id_parent = ".(int)$id_parent." ".($active ? " AND  c.enabled = 1" : "").($id_category?' AND c.id_category <'.(int)$id_category :'')." AND cs.id_shop='".(int)Context::getContext()->shop->id."' GROUP BY c.id_category ORDER BY c.sort_order";
        return Db::getInstance()->executeS($sql);
    }
    public static function getCategoriesDisabled()
    {
        if($categories = explode(',',Configuration::get('YBC_BLOG_CATEGOGY_CUSTOMER')))
        {
            $in = implode(',',array_map('intval',$categories));
        }
        $slq="SELECT id_category FROM `"._DB_PREFIX_."ybc_blog_category` WHERE 1".(isset($in) && $in? ' AND id_category NOT IN ('.$in.')':'') ;
        $categories = Db::getInstance()->executeS($slq);
        if($categories)
        {
            $array=array();
            foreach($categories as $category)
                $array[]=$category['id_category'];
            return $array;
        }
        return array();
    }
    public static function getBlogCategoriesTree($id_root,$active=true,$id_lang=null,$id_category=0,$link=true)
    {
        if(is_null($id_lang))
            $id_lang = (int)Context::getContext()->language->id;
        $tree=array();
        if($id_root==0)
        {
            $cat = array(
                'id_category' => 0,
                'title' => 'Root',
            );
            $children = self::getChildrenBlogCategories($id_root, $active, $id_lang,$id_category);
            $temp = array();
            if($children)
            {
                foreach($children as &$child)
                {
                    $arg = self::getBlogCategoriesTree($child['id_category'], $active, $id_lang,$id_category,$link);
                    if($arg && isset($arg[0]))
                    {
                        if($link)
                        {
                            $arg[0]['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_category'=>$child['id_category']));
                            $arg[0]['link_rss'] = Context::getContext()->link->getModuleLink('ybc_blog','rss',array('id_category'=>$child['id_category']));
                        }
                        else
                        {
                            $arg[0]['link']='#';
                            $arg[0]['link_rss']='#';
                        }
                        if($child['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$child['thumb']))
                            $arg[0]['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/thumb/'.$child['thumb'].'" style="width:20px;"/>';
                        elseif($child['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$child['image']))
                            $arg[0]['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/'.$child['image'].'" style="width:20px;"/>';
                        $temp[] = $arg[0];
                    }

                }
            }
            $cat['children'] = $temp;
            $tree[] = $cat;
        }
        else
        {
            if(($category  = new Ybc_blog_category_class($id_root,Context::getContext()->language->id)) && Validate::isLoadedObject($category) && (!$active || $category->enabled))
            {
                $cat = array(
                    'id_category' => $id_root,
                    'title' =>$category->title,
                    'count_posts' => Ybc_blog_post_class::countPostsWithFilter(' AND pc.id_category="'.(int)$id_root.'" AND p.enabled=1'),
                );
                $children = self::getChildrenBlogCategories($id_root, $active, $id_lang,$id_category);
                $temp = array();
                if($children)
                {
                    foreach($children as &$child)
                    {
                        $arg = self::getBlogCategoriesTree($child['id_category'], $active, $id_lang,$id_category,$link);
                        if($arg && isset($arg[0]))
                        {
                            if($link)
                            {
                                $arg[0]['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_category'=>$child['id_category']));
                                $arg[0]['link_rss'] = Context::getContext()->link->getModuleLink('ybc_blog','rss',array('id_category'=>$child['id_category']));
                            }
                            else
                            {
                                $arg[0]['link'] ='#';
                                $arg[0]['link_rss']='#';
                            }
                            if($child['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$child['thumb']))
                                $arg[0]['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/thumb/'.$child['thumb'].'" style="width:20px;"/>';
                            elseif($child['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$child['image']))
                                $arg[0]['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/'.$child['image'].'" style="width:20px;"/>';
                            $temp[] = $arg[0];
                        }

                    }
                }
                $cat['children'] = $temp;
                $tree[] = $cat;
            }
        }
        return $tree;
    }
    public static function getBlogCategoriesTreeFontEnd($id_root,$active=true,$id_lang=null,$id_category=0)
    {
        $tree = array();
        if(is_null($id_lang))
            $id_lang = (int)Context::getContext()->language->id;
        if($id_root==0)
        {
            $cat = array(
                'id_category' => 0,
                'title' => 'Root',
            );
            $children = self::getChildrenBlogCategories($id_root, $active, $id_lang,$id_category);
            $temp = array();
            if($children)
            {
                foreach($children as &$child)
                {
                    $arg = self::getBlogCategoriesTreeFontEnd($child['id_category'], $active, $id_lang,$id_category);
                    if($arg && isset($arg[0]))
                    {
                        $arg[0]['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_category'=>$child['id_category']));
                        $arg[0]['link_rss'] = Context::getContext()->link->getModuleLink('ybc_blog','rss',array('id_category'=>$child['id_category']));
                        if($child['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$child['thumb']))
                            $arg[0]['thumb_link'] = '<img src="'.Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/thumb/'.$child['thumb']).'" style="width:20px;"/>';
                        elseif($child['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$child['image']))
                            $arg[0]['thumb_link'] = '<img src="'.Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/'.$child['image']).'" style="width:20px;"/>';
                        if(self::checkCategoryEnabled($child['id_category']))
                            $temp[] = $arg[0];
                    }

                }
            }
            $cat['children'] = $temp;
            $tree[] = $cat;
        }
        else
        {
            if(($category = new Ybc_blog_category_class($id_root,Context::getContext()->language->id)) && Validate::isLoadedObject($category) && (!$active || $category->enabled))
            {
                $cat = array(
                    'id_category' => $id_root,
                    'title' => $category->title,
                    'count_posts' => Ybc_blog_post_class::countPostsWithFilter(' AND pc.id_category="'.(int)$id_root.'" AND p.enabled=1'),
                );
                $children = self::getChildrenBlogCategories($id_root, $active, $id_lang,$id_category);
                $temp = array();
                if($children)
                {
                    foreach($children as &$child)
                    {
                        $arg = self::getBlogCategoriesTreeFontEnd($child['id_category'], $active, $id_lang,$id_category);
                        if($arg && isset($arg[0]))
                        {
                            $arg[0]['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_category'=>$child['id_category']));
                            $arg[0]['link_rss'] = Context::getContext()->link->getModuleLink('ybc_blog','rss',array('id_category'=>$child['id_category']));
                            if($child['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$child['thumb']))
                                $arg[0]['thumb_link'] = '<img src="'.Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/thumb/'.$child['thumb']).'" style="width:20px;"/>';
                            elseif($child['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$child['image']))
                                $arg[0]['thumb_link'] = '<img src="'.Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/'.$child['image']).'" style="width:20px;"/>';
                            if(self::checkCategoryEnabled($child['id_category']))
                                $temp[] = $arg[0];
                        }

                    }
                }
                $cat['children'] = $temp;
                $tree[] = $cat;
            }
        }
        return $tree;
    }
    public static function checkCategoryEnabled($id_category){
        $categories_enabled= explode(',',Configuration::get('YBC_BLOG_CATEGOGY_CUSTOMER'));
        if(in_array($id_category,$categories_enabled))
            return true;
        elseif($childs = Ybc_blog_category_class::getChildrenBlogCategories($id_category))
        {
            foreach($childs as $child)
                if(self::checkCategoryEnabled($child['id_category']))
                    return true;
        }
        return false;
    }
    public static function getSelectedRelatedCategories($id_post)
    {
        $categories = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post_related_categories` WHERE id_post='.(int)$id_post);
        $relateds= array();
        if($categories)
        {
            foreach($categories as $cat)
            {
                $relateds[]=$cat['id_category'];
            }
        }
        return $relateds;
    }
    public static function getSelectedRelatedProductCategories($id_post)
    {
        $categories = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post_related_product_categories` WHERE id_post='.(int)$id_post);
        $relateds= array();
        if($categories)
        {
            foreach($categories as $cat)
            {
                $relateds[]=$cat['id_category'];
            }
        }
        return $relateds;
    }
    public static function updateCategoryOrdering($categories,$page=1)
    {
        if($page <=1)
            $page=1;
        if($categories)
        {
            foreach($categories as $key=> $category)
            {
                $position=  1+ $key + ($page-1)*20;
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_category` SET sort_order="'.(int)$position.'" WHERE id_category='.(int)$category);
            }
        }

        return true;
    }
    public static function getMaxSortOrder($id_parent)
    {
       return Db::getInstance()->getValue('SELECT MAX(c.sort_order) FROM `'._DB_PREFIX_.'ybc_blog_category` c
       INNER JOIN `'._DB_PREFIX_.'ybc_blog_category_shop` cs ON c.id_category =cs.id_category AND cs.id_shop='.(int)Context::getContext()->shop->id.'
       WHERE 1 AND c.id_parent='.(int)$id_parent );
    }
    public static function checkUrlAliasExists($url_alias,$id_category)
    {
        return Db::getInstance()->getValue('SELECT cs.id_category FROM `'._DB_PREFIX_.'ybc_blog_category_lang` cl
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_category_shop` cs ON cs.id_category= cl.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'"
        WHERE cl.url_alias ="'.pSQL($url_alias).'" AND cs.id_category!="'.(int)$id_category.'"');
    }
}