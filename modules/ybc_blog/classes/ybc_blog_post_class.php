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
class Ybc_blog_post_class extends ObjectModel
{
    public $id_post;
    public $id_shop;
    public $is_featured;
    public $title;
    public $description;
    public $short_description;
    public $enabled;
    public $url_alias;
    public $meta_description;
    public $meta_keywords;
    public $products;
    public $exclude_products;
    public $image;
    public $sort_order;
    public $datetime_added;
    public $datetime_modified;
    public $datetime_active;
    public $added_by;
    public $is_customer;
    public $modified_by;
    public $click_number;
    public $likes;
    public $thumb;
    public $meta_title;
    public $id_category_default;
    public static $definition = array(
        'table' => 'ybc_blog_post',
        'primary' => 'id_post',
        'multilang' => true,
        'fields' => array(
            'enabled' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'sort_order' => array('type' => self::TYPE_INT),
            'click_number' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'likes' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'is_featured' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_category_default' => array('type' => self::TYPE_INT),
            'added_by' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'is_customer' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'modified_by' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'products' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'exclude_products' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'datetime_added' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'datetime_modified' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500),
            'datetime_active' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500, 'allow_null' => true),
            // Lang fields
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500, 'lang' => true),
            'thumb' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500, 'lang' => true),
            'url_alias' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'lang' => true, 'size' => 500,),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 700),
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 700),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 700),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'short_description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml')

        )
    );

    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
        if(!$this->datetime_active)
            $this->datetime_active ='0000-00-00 00:00:00';
        if ($this->id)
            $this->id_shop = Db::getInstance()->getValue('SELECT id_shop FROM `' . _DB_PREFIX_ . 'ybc_blog_post_shop` where id_post= ' . (int)$this->id);
    }

    public function add($autodate = true, $null_values = false)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'ybc_blog_post_shop` (`id_shop`, `id_post`)
			VALUES(' . (int)$id_shop . ', ' . (int)$this->id . ')'
        );
        return $res;
    }

    public function duplicate()
    {
        $this->id = null;
        $oldImages = $this->image;
        $oldthumbs = $this->thumb;
        if ($this->image) {
            foreach ($this->image as $id_lang => $image) {
                if ($image)
                    $this->image[$id_lang] = time() . pathinfo($image, PATHINFO_BASENAME);
            }
        }
        if ($this->thumb) {
            foreach ($this->thumb as $id_lang => $thumb) {
                if ($thumb)
                    $this->thumb[$id_lang] = time() . pathinfo($thumb, PATHINFO_BASENAME);
            }
        }
        if ($this->add()) {
            if ($this->image) {
                foreach ($this->image as $id_lang => $image) {
                    if ($image)
                        @copy(_PS_YBC_BLOG_IMG_DIR_ . 'post/' . $oldImages[$id_lang], _PS_YBC_BLOG_IMG_DIR_ . 'post/' . $image);
                }
            }
            if ($this->thumb) {
                foreach ($this->thumb as $id_lang => $thumb) {
                    if ($thumb)
                        @copy(_PS_YBC_BLOG_IMG_DIR_ . 'post/thumb/' . $oldthumbs[$id_lang], _PS_YBC_BLOG_IMG_DIR_ . 'post/thumb/' . $thumb);
                }

            }
            return $this->id;
        }
        return false;
    }

    public static function getPostByID($id_post)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ybc_blog_post` p WHERE id_post=' . (int)$id_post);
    }

    public static function getAuthorByIdPost($id_post)
    {
        $post = self::getPostByID($id_post);
        if ($post) {
            if ($post['is_customer']) {
                $author = Db::getInstance()->getRow('SELECT c.id_customer,c.firstname,c.lastname,ybe.is_customer,ybe.name FROM `' . _DB_PREFIX_ . 'customer` c
                LEFT JOIN `' . _DB_PREFIX_ . 'ybc_blog_employee` ybe on (ybe.is_customer=1 AND ybe.id_employee=c.id_customer)
                WHERE c.id_customer= "' . (int)$post['added_by'] . '"
            ');
                if ($author)
                    $author['link'] = Context::getContext()->link->getAdminLink('AdminCustomers') . '&updatecustomer&id_customer=' . $author['id_customer'];
            } else {
                $author = Db::getInstance()->getRow('SELECT e.id_employee,e.firstname,e.lastname,ybe.is_customer,ybe.name FROM `' . _DB_PREFIX_ . 'employee` e
                LEFT JOIN `' . _DB_PREFIX_ . 'ybc_blog_employee` ybe on (ybe.is_customer=0 AND ybe.id_employee=e.id_employee)
                WHERE e.id_employee="' . (int)$post['added_by'] . '"
            ');
                if ($author)
                    $author['link'] = Context::getContext()->link->getAdminLink('AdminEmployees') . '&id_employee=' . (int)$author['id_employee'] . '&updateemployee';
            }
            if ($author) {
                return $author;
            }
        }
        return false;
    }

    public static function deleteAllPostByIdAuthor($id_author, $is_customer = true)
    {
        $posts = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ybc_blog_post` WHERE ' . ($is_customer ? 'is_customer=1' : 'is_customer=0') . ' AND added_by="' . (int)$id_author . '"');
        if ($posts) {
            foreach ($posts as $post) {
                self::_deletePost($post['id_post']);
            }
        }
        return true;
    }

    public static function _deletePost($id_post, $sort = true)
    {
        $post = new Ybc_blog_post_class($id_post);
        if (Validate::isLoadedObject($post)) {
            if ($post->delete()) {
                if ($post->image) {
                    foreach ($post->image as $image)
                        if (file_exists(_PS_YBC_BLOG_IMG_DIR_ . 'post/' . $image))
                            @unlink(_PS_YBC_BLOG_IMG_DIR_ . 'post/' . $image);
                }
                if ($post->thumb) {
                    foreach ($post->thumb as $thumb)
                        if (file_exists(_PS_YBC_BLOG_IMG_DIR_ . 'post/thumb/' . $thumb))
                            @unlink(_PS_YBC_BLOG_IMG_DIR_ . 'post/thumb/' . $thumb);
                }
                $req = "DELETE FROM `" . _DB_PREFIX_ . "ybc_blog_post_category` WHERE id_post=" . (int)$id_post;
                Db::getInstance()->execute($req);
                $req = "DELETE FROM `" . _DB_PREFIX_ . "ybc_blog_tag` WHERE id_post=" . (int)$id_post;
                Db::getInstance()->execute($req);
                $req = "DELETE FROM `" . _DB_PREFIX_ . "ybc_blog_comment` WHERE id_post=" . (int)$id_post;
                Db::getInstance()->execute($req);
                $req = "DELETE FROM `" . _DB_PREFIX_ . "ybc_blog_post_shop` WHERE id_post=" . (int)$id_post;
                Db::getInstance()->execute($req);
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ybc_blog_post_related_product_categories` WHERE id_post=' . (int)$id_post);
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ybc_blog_post_related_categories` WHERE id_post=' . (int)$id_post);
                if ($sort) {
                    $posts = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ybc_blog_post` p, `' . _DB_PREFIX_ . 'ybc_blog_post_shop` ps  WHERE p.id_post=ps.id_post AND  ps.id_shop="' . (int)Context::getContext()->shop->id . '" order by p.sort_order asc');
                    if ($posts) {
                        foreach ($posts as $key => $post) {
                            $position = $key + 1;
                            Db::getInstance()->execute('update `' . _DB_PREFIX_ . 'ybc_blog_post` SET sort_order ="' . (int)$position . '" WHERE id_post=' . (int)$post['id_post']);
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }

    public static function getPostsByIdCategory($id_category, $id_lang = false, $enabled = false)
    {
        if (!Configuration::get('YBC_BLOG_POST_SORT_BY'))
            $sort = 'p.datetime_added DESC, ';
        else {
            if (Configuration::get('YBC_BLOG_POST_SORT_BY') == 'sort_order')
                $sort = 'pc.position ASC, ';
            elseif (Configuration::get('YBC_BLOG_POST_SORT_BY') == 'id_post')
                $sort = 'p.datetime_added DESC, ';
            else
                $sort = 'p.' . Configuration::get('YBC_BLOG_POST_SORT_BY') . ' DESC, ';
        }
        unset($id_lang);
        $filter = ' AND pc.id_category="' . (int)$id_category . '"' . ($enabled ? ' AND p.enabled=1' : '');
        return self::getPostsWithFilter($filter, $sort, false, false, false, $id_category);
    }

    public static function getPostsWithFilter($filter = false, $sort = false, $start = false, $limit = false, $fontend = true, $id_category = 0)
    {
        $req = "SELECT p.*,pc.id_category, pl.image,pl.thumb, pl.title, pl.description, pl.short_description, pl.meta_keywords, pl.meta_description,pl.url_alias,e.firstname, e.lastname,pc.position,count(pcm.id_comment) as total_comment,IFNULL(ybe.status,1) as status
            FROM `" . _DB_PREFIX_ . "ybc_blog_post` p
            INNER JOIN `" . _DB_PREFIX_ . "ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop='" . (int)Context::getContext()->shop->id . "')
            LEFT JOIN `" . _DB_PREFIX_ . "ybc_blog_post_lang` pl ON p.id_post = pl.id_post AND pl.id_lang = " . (int)Context::getContext()->language->id . "
            LEFT JOIN `" . _DB_PREFIX_ . "ybc_blog_post_category` pc ON (p.id_post = pc.id_post " . ($id_category ? ' AND pc.id_category="' . (int)$id_category . '"' : '') . ") 
            LEFT JOIN `" . _DB_PREFIX_ . "ybc_blog_post_related_categories` rpc ON (p.id_post = rpc.id_post)
            LEFT JOIN `" . _DB_PREFIX_ . "customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
            LEFT JOIN `" . _DB_PREFIX_ . "employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
            LEFT JOIN `" . _DB_PREFIX_ . "ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
            LEFT JOIN `" . _DB_PREFIX_ . "ybc_blog_comment` pcm on (pcm.id_post=p.id_post)
            WHERE 1 " . ($fontend ? " AND (p.enabled=1 OR p.enabled=-1) AND (ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) " : "") . ($filter ? $filter : '') . "  
             GROUP BY p.id_post
            ORDER BY " . ($sort ? $sort : '') . " p.id_post DESC " . ($start !== false && $limit ? " LIMIT " . (int)$start . ", " . (int)$limit : "");
        $posts = Db::getInstance()->executeS($req);
        if ($posts) {
            foreach ($posts as $key=>  &$post) {
                $post['thumb_link'] = $post['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_ . 'post/thumb/' . $post['thumb']) ? '<img src="' . _PS_YBC_BLOG_IMG_ . 'post/thumb/' . $post['thumb'] . '" style="width:40px;"/>' : '';
                $author = Db::getInstance()->getRow('SELECT name,IFNULL(status,1) as status FROM `' . _DB_PREFIX_ . 'ybc_blog_employee` WHERE is_customer="' . (int)$post['is_customer'] . '" AND id_employee="' . (int)$post['added_by'] . '"');
                if ($post['is_customer']) {
                    if ($customer = Db::getInstance()->getValue('SELECT concat(firstname," ",lastname) as fullname  FROM `' . _DB_PREFIX_ . 'customer` WHERE id_shop="' . (int)Context::getContext()->shop->id . '" AND id_customer="' . (int)$post['added_by'] . '"')) {
                        $link_author = Module::getInstanceByName('ybc_blog')->getlink('blog', array('id_author' => $post['added_by'], 'is_customer' => $post['is_customer']));
                        if (!($author && isset($author['name']) && $author['name']))
                            $post['name_author'] = $customer;
                        else
                            $post['name_author'] = $author['name'];
                        $post['name_author'] = Module::getInstanceByName('ybc_blog')->displayText($post['name_author'],'a',null,null,$link_author,true).' (Role: customer' . ($author && $author['status'] <= 0 ? ', suspend' : '') . ')';
                    } else
                        $post['name_author'] = '';
                    $post['status_author'] = $author ? $author['status']:1;

                } else {
                    if ($employee = Db::getInstance()->getValue('SELECT concat(firstname," ",lastname) as fullname FROM `' . _DB_PREFIX_ . 'employee` WHERE id_employee="' . (int)$post['added_by'] . '"')) {
                        $link_author = Module::getInstanceByName('ybc_blog')->getlink('blog', array('id_author' => $post['added_by']));
                        if (!isset($author['name']) || !$author['name'])
                            $post['name_author'] = $employee;
                        else
                            $post['name_author'] = $author['name'];
                        $id_profile = Db::getInstance()->getValue('SELECT id_profile FROM `' . _DB_PREFIX_ . 'employee` WHERE id_employee=' . (int)$post['added_by']);
                        if ($id_profile == 1)
                            $post['status_author'] = 1;
                        else
                            $post['status_author'] = $author['status'];
                        $profile = Db::getInstance()->getValue('SELECT name from `' . _DB_PREFIX_ . 'profile_lang` WHERE id_lang="' . Context::getContext()->language->id . '" AND id_profile=' . (int)$id_profile);
                        $post['name_author'] = Module::getInstanceByName('ybc_blog')->displayText($post['name_author'],'a',null,null,$link_author,true). ' (Role: ' . $profile . ($author && $author['status'] <= 0 && $id_profile != 1 ? ', suspend' : '') . ')';
                    } else
                        $post['name_author'] = '';

                }
            }
        }
        return $posts;
    }

    public static function updateCategories($categories, $id_post)
    {
        $req = "DELETE FROM `" . _DB_PREFIX_ . "ybc_blog_post_category` WHERE id_post = " . (int)$id_post . ($categories ? ' AND id_category NOT IN (' . implode(',', array_map('intval', $categories)) . ')' : '');
        Db::getInstance()->execute($req);
        if ($categories) {
            foreach ($categories as $cat) {
                if (!self::checkPostCategory($id_post, (int)$cat)) {
                    $position = 1 + (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ybc_blog_post_category` WHERE id_category=' . (int)$cat);
                    $req = "INSERT INTO `" . _DB_PREFIX_ . "ybc_blog_post_category`(id_post, id_category,position) VALUES(" . (int)$id_post . ", " . (int)$cat . "," . (int)$position . ")";
                    Db::getInstance()->execute($req);
                }
            }
        }
    }

    public static function checkPostCategory($id_post, $id_category)
    {
        $req = "SELECT * FROM `" . _DB_PREFIX_ . "ybc_blog_post_category` WHERE id_post = " . (int)$id_post . " AND id_category = " . (int)$id_category;
        return Db::getInstance()->getRow($req);
    }

    public static function getOnlyCategoryBlog($id_post)
    {
        $req = "SELECT c.id_category,cl.title FROM `" . _DB_PREFIX_ . "ybc_blog_category` c
                INNER JOIN `" . _DB_PREFIX_ . "ybc_blog_post_category` pc ON (c.id_category = pc.id_category AND pc.id_post=" . (int)$id_post . ")
                LEFT JOIN `" . _DB_PREFIX_ . "ybc_blog_category_lang` cl ON c.id_category = cl.id_category AND cl.id_lang=" . (int)Context::getContext()->language->id . "
                WHERE pc.id_post = " . (int)$id_post;
        return Db::getInstance()->executeS($req);
    }

    public static function updateRelatedCategories($categories, $id_post)
    {
        $req = "DELETE FROM `" . _DB_PREFIX_ . "ybc_blog_post_related_categories` WHERE id_post = " . (int)$id_post . ($categories ? ' AND id_category NOT IN (' . implode(',', array_map('intval', $categories)) . ')' : '');
        Db::getInstance()->execute($req);
        if ($categories) {
            foreach ($categories as $cat) {
                if (!self::checkPostRelatedCategory($id_post, (int)$cat)) {
                    $req = "INSERT INTO `" . _DB_PREFIX_ . "ybc_blog_post_related_categories`(id_post, id_category) VALUES(" . (int)$id_post . ", " . (int)$cat . ")";
                    Db::getInstance()->execute($req);
                }
            }
        }
    }

    public static function updateRelatedProductCategories($categories, $id_post)
    {
        $req = 'DELETE FROM `' . _DB_PREFIX_ . 'ybc_blog_post_related_product_categories` WHERE id_post = ' . (int)$id_post . ($categories ? ' AND id_category NOT IN (' . implode(',', array_map('intval', $categories)) . ')' : '');
        Db::getInstance()->execute($req);
        $added = array();
        if ($categories) {
            foreach ($categories as $cat) {
                if (!in_array($cat,$added)) {
                    $added[] = $cat;
                    $req = "INSERT IGNORE INTO `" . _DB_PREFIX_ . "ybc_blog_post_related_product_categories`(id_post, id_category) VALUES(" . (int)$id_post . ", " . (int)$cat . ")";
                    Db::getInstance()->execute($req);
                }
            }
        }
    }
    public static function checkPostRelatedCategory($id_post, $id_category)
    {
        $req = "SELECT * FROM `"._DB_PREFIX_."ybc_blog_post_related_categories` WHERE id_post = ".(int)$id_post." AND id_category = ".(int)$id_category;
        return Db::getInstance()->getRow($req);
    }
    public static function getProductsByIDs($id_products)
    {
        if($id_products)
        {
            $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`
			FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` and pl.id_lang="'.(int)Context::getContext()->language->id.'" AND pl.id_shop=product_shop.id_shop)
            WHERE p.`id_product` IN ('.implode(',',array_map('intval',$id_products)).') GROUP BY p.id_product';
            $product_list = Db::getInstance()->executeS($sql);
            if($product_list)
            {
                if(version_compare(_PS_VERSION_, '1.7', '>='))
                    $type_image= ImageType::getFormattedName('small');
                else
                    $type_image= ImageType::getFormatedName('small');
                foreach($product_list as &$product)
                {
                    $id_image = Db::getInstance()->getValue("SELECT id_image FROM `"._DB_PREFIX_."image` WHERE id_product=".(int)$product['id_product'].' AND cover=1');
                    $product['link_image'] =  Context::getContext()->link->getImageLink($product['link_rewrite'], $id_image, $type_image);
                    $product['link'] = Context::getContext()->link->getProductLink($product['id_product'],null,null,null,null,null,Product::getDefaultAttribute($product['id_product']));
                }
            }
            return $product_list;
        }
        return false;
    }
    public static function getTagsByIdPost($id_post, $id_lang = false)
    {
        if(!$id_lang)
            $id_lang = Context::getContext()->language->id;
        $req = "SELECT * FROM `"._DB_PREFIX_."ybc_blog_tag`
            WHERE id_lang = ".(int)$id_lang." AND id_post = ".(int)$id_post."
            ORDER by tag asc";
        $tags = Db::getInstance()->executeS($req);
        if($tags)
        {
            foreach($tags as &$tag)
            {
                $tag['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('tag' => urlencode($tag['tag'])));
            }
        }
        return $tags;
    }
    public static function increasTagViews($tag)
    {
        $sql = "UPDATE `"._DB_PREFIX_."ybc_blog_tag`
            SET click_number = click_number + 1
            WHERE tag = '".pSQL($tag)."'";
        return Db::getInstance()->execute($sql);
    }
    public static function getTags($limit = 20, $id_lang = false)
    {
        if(!$id_lang)
            $id_lang = Context::getContext()->language->id;
        $req = "SELECT DISTINCT ROUND(SUM(t.click_number)/COUNT(t.id_tag)) as viewed, t.tag 
            FROM `"._DB_PREFIX_."ybc_blog_tag` t
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (t.id_post = p.id_post)
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (p.id_post = ps.id_post AND ps.id_shop='".(int)Context::getContext()->shop->id."')
            WHERE id_lang = ".(int)$id_lang."
            GROUP BY  t.tag
            ORDER BY viewed desc, tag asc
            LIMIT 0,".(int)$limit;
        $tags = Db::getInstance()->executeS($req);
        if($tags)
        {
            foreach($tags as &$tag)
            {
                $tag['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('tag' => urlencode($tag['tag'])));
            }
        }
        return $tags;
    }
    public static function updateTags($id_post, $tags)
    {
        if($id_post && $tags && is_array($tags))
        {
            foreach($tags as $id_lang => $tagList)
            {
                if($tagList && is_array($tagList))
                {
                    $str = '';
                    foreach($tagList as $tag)
                    {
                        $tag = Tools::strtolower($tag);
                        if($tag && !self::checkTagLang($id_post, $id_lang, $tag))
                        {
                            $req = "INSERT INTO `"._DB_PREFIX_."ybc_blog_tag`(id_tag,id_post, id_lang, tag, click_number)
                                        VALUES(null, ".(int)$id_post.", ".(int)$id_lang.", '".pSQL($tag)."',0)";
                            Db::getInstance()->execute($req);
                        }
                        $str .= $tag.',';
                    }
                    $str = explode(',',Tools::rtrimString($str, ','));
                    $req = "DELETE FROM `"._DB_PREFIX_."ybc_blog_tag` 
                                    WHERE id_post = ".(int)$id_post." AND id_lang = ".(int)$id_lang." AND tag NOT IN ('".implode("','",array_map('pSQL',$str))."')";
                    Db::getInstance()->execute($req);
                }
                else
                {
                    $req = "DELETE FROM `"._DB_PREFIX_."ybc_blog_tag` 
                                    WHERE id_post = ".(int)$id_post." AND id_lang = ".(int)$id_lang;
                    Db::getInstance()->execute($req);
                }
            }
        }
    }
    public static function checkTagLang($id_post, $id_lang, $tag)
    {
        $req = "SELECT * FROM `"._DB_PREFIX_."ybc_blog_tag`
            WHERE id_lang = ".(int)$id_lang." AND id_post = ".(int)$id_post." AND tag = '".pSQL($tag)."'";
        return Db::getInstance()->getRow($req);
    }
    public static function getTagStr($id_post, $id_lang)
    {
        if(!$id_post || !$id_lang)
            return '';
        $req = "SELECT tag FROM `"._DB_PREFIX_."ybc_blog_tag` WHERE id_post = ".(int)$id_post." AND id_lang = ".(int)$id_lang;
        $tags = Db::getInstance()->executeS($req);
        $tagStr = '';
        if($tags)
        {
            foreach($tags as $tag)
                $tagStr .= $tag['tag'].',';
        }
        return trim($tagStr,',');
    }
    public static function checkPostSuspend($id_post){
        if(!$id_post)
            return false;
        else
        {
            $author = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post` p
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_employee` ybc ON (p.is_customer=ybc.is_customer AND p.added_by = ybc.id_employee)
                WHERE id_post="'.(int)$id_post.'" AND ybc.status=-1');
            if($author)
            {
                return true;
            }
        }
        return false;
    }
    public static function updatePostOrdering($posts,$page=1,$id_category=0)
    {
        if($posts)
        {
            if($page <1)
                $page=1;
            foreach($posts as $key=> $post)
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_post' =>(int)$post,
                ));
                $position=  1+ $key + ($page-1)*20;
                if($id_category)
                {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_post_category` SET position="'.(int)$position.'" WHERE id_post='.(int)$post.' AND id_category='.(int)$id_category);
                }
                else
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_post` SET sort_order="'.(int)$position.'" WHERE id_post='.(int)$post);
            }
        }
        return true;
    }
    public static function checkUrlAliasExists($url_alias,$id_post)
    {
        return Db::getInstance()->getValue('SELECT ps.id_post FROM `'._DB_PREFIX_.'ybc_blog_post_lang` pl
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON ps.id_post= pl.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'"
        WHERE pl.url_alias ="'.pSQL($url_alias).'" AND ps.id_post!="'.(int)$id_post.'"');
    }
    public static function getMaxOrder()
    {
        return (int)Db::getInstance()->getValue('SELECT MAX(p.sort_order) FROM `'._DB_PREFIX_.'ybc_blog_post` p
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post = ps.id_post)    
        WHERE ps.id_shop='.(int)Context::getContext()->shop->id);
    }
    public static function countPostsWithFilter($filter,$fontend=true)
    {
        $req = "SELECT DISTINCT p.*, pl.title, pl.description
            FROM `"._DB_PREFIX_."ybc_blog_post` p
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON p.id_post = pl.id_post
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_category` pc ON p.id_post = pc.id_post
            LEFT JOIN `"._DB_PREFIX_."customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
            LEFT JOIN `"._DB_PREFIX_."employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_comment` pcm on (pcm.id_post=p.id_post)
            WHERE ".($fontend ? "(p.enabled=1 OR p.enabled=-1) AND (ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND ":"")."pl.id_lang = ".(int)Context::getContext()->language->id.($filter ? $filter : '');
        $res = Db::getInstance()->executeS($req);
        return $res ? count($res) : 0;
    }
    public static function getPostAlias($id_post,$id_lang=0)
    {
        if(!$id_lang)
            $id_lang = Context::getContext()->language->id;
        $req = "SELECT pl.url_alias
            FROM `"._DB_PREFIX_."ybc_blog_post_lang` pl
            WHERE pl.id_post = ".(int)$id_post.' AND pl.id_lang='.(int)$id_lang;
        $row = Db::getInstance()->getRow($req);
        if(isset($row['url_alias']))
            return $row['url_alias'];
        return false;
    }
    public static function getTotalReviewsWithRating($id_post)
    {
        $req = "SELECT SUM(rating)
            FROM `"._DB_PREFIX_."ybc_blog_comment`
            WHERE id_post = ".(int)$id_post." AND rating > 0 AND approved = 1";
        return (int)Db::getInstance()->getValue($req);
    }
    public static function countTotalReviewsWithRating($id_post)
    {
        $req = "SELECT COUNT(rating)
            FROM `"._DB_PREFIX_."ybc_blog_comment`
            WHERE id_post = ".(int)$id_post." AND rating > 0 AND approved = 1";
        return (int)Db::getInstance()->getValue($req);
    }
    public static function autoActivePost(){
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_post` SET enabled=1,datetime_added="'.pSQL(date('Y-m-d H:i:s')).'",datetime_modified="'.pSQL(date('Y-m-d H:i:s')).'" WHERE datetime_active!="0000-00-00" AND datetime_active is not NULL AND enabled=2 AND datetime_active<=NOW()');
    }
    public static function getBlogPositiveAuthor()
    {
        $sql ='SELECT COUNT(p.id_post) as total_post, p.added_by,p.is_customer FROM `'._DB_PREFIX_.'ybc_blog_post` p
            INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post =ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=p.added_by AND p.is_customer=1)
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer =p.added_by AND p.is_customer=0)
            LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
            WHERE (ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.enabled=1 
            GROUP BY p.added_by,p.is_customer ORDER BY total_post DESC LIMIT 0,'.(Configuration::get('YBC_BLOG_AUTHOR_NUMBER') ? Configuration::get('YBC_BLOG_AUTHOR_NUMBER') : 3);
        $authors= Db::getInstance()->executeS($sql);
        if($authors)
        {
            foreach($authors as &$author)
            {
                if($author['is_customer'])
                {
                    $information = Db::getInstance()->getRow('
                    SELECT * FROM `'._DB_PREFIX_.'customer` c
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (be.id_employee=c.id_customer AND be.is_customer=1)
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post=bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'")
                    WHERE c.id_customer="'.(int)$author['added_by'].'"');
                    if(!$information['name'])
                        $information['name']= $information['firstname'].' '.$information['lastname'];
                    $author['information']=$information;
                    $author['link']= Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_author'=>$author['added_by'],'is_customer'=>1,'alias'=> Tools::link_rewrite($information['name'])));
                    if(isset($information['avata'])&&$information['avata'])
                        $author['avata'] = Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$information['avata']);
                    else
                        $author['avata']= Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png'));
                }
                else
                {
                    $information = Db::getInstance()->getRow('
                    SELECT * FROM `'._DB_PREFIX_.'employee` e
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (be.id_employee=e.id_employee AND be.is_customer=0)
                    LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post=bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'")
                    WHERE e.id_employee="'.(int)$author['added_by'].'"');
                    if(!$information['name'])
                        $information['name']=$information['firstname'].' '.$information['lastname'];
                    $author['information']=$information;
                    $author['link']= Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_author'=>$author['added_by'],'is_customer'=>0,'alias'=> Tools::link_rewrite($information['name'])));
                    if(isset($information['avata']) && $information['avata'])
                        $author['avata'] = Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$information['avata']);
                    else
                        $author['avata']= Context::getContext()->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png'));
                }
                $sql ='SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post` p
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
                LEFT JOIN `'._DB_PREFIX_.'ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang="'.(int)Context::getContext()->language->id.'")
                WHERE p.enabled=1 AND  p.added_by ="'.(int)$author['added_by'].'" AND p.is_customer="'.(int)$author['is_customer'].'"';
                $author['posts'] = Db::getInstance()->executeS($sql);
                if($author['posts'])
                {
                    foreach($author['posts'] as &$post)
                    {
                        $post['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('id_post'=>$post['id_post']));
                    }
                }
            }
        }
        return $authors;
    }
    public static function getIDPostByUrlAlias($url_alias,$id_lang=0)
    {
        $sql = 'SELECT p.id_post FROM `'._DB_PREFIX_.'ybc_blog_post` p
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_lang` pl ON (p.id_post = pl.id_post'.($id_lang ? ' AND pl.id_lang="'.(int)$id_lang.'"':'').')
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE pl.url_alias ="'.pSQL($url_alias).'"';
        return (int)Db::getInstance()->getValue($sql);
    }
    public static function getBlogRssAuthor()
    {
        $employees= Db::getInstance()->executeS(
            'SELECT e.id_employee, e.firstname,e.lastname,be.name,bel.description FROM `'._DB_PREFIX_.'employee` e
            INNER JOIN `'._DB_PREFIX_.'ybc_blog_post` p ON (p.added_by=e.id_employee AND p.is_customer=0)
            LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (e.id_employee=be.id_employee AND be.is_customer=0)
            LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post= bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'") 
            WHERE be.status>=0 OR be.status is NULL OR e.id_profile=1
            GROUP BY e.id_employee
        ');
        if($employees)
        {
            foreach($employees as &$employee)
            {
                $employee['name'] = $employee['name'] ? $employee['name'] : $employee['firstname'].' '.$employee['lastname'];
                $employee['link']= Module::getInstanceByName('ybc_blog')->getLink('rss',array('id_author'=>$employee['id_employee'],'is_customer'=>0,'alias'=>Tools::link_rewrite($employee['name'])));
            }
        }
        $group_authors= explode(',',Configuration::get('YBC_BLOG_GROUP_CUSTOMER_AUTHOR'));
        if($group_authors)
        {
            $customers= Db::getInstance()->executeS(
                'SELECT c.id_customer, c.firstname,c.lastname,be.name,bel.description FROM `'._DB_PREFIX_.'customer` c
                INNER JOIN `'._DB_PREFIX_.'customer_group` gs ON (gs.id_customer=c.id_customer)
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_post` p ON (p.added_by=c.id_customer AND p.is_customer=1)
                LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` be ON (c.id_customer=be.id_employee AND be.is_customer=1)
                LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee_lang` bel ON (be.id_employee_post= bel.id_employee_post AND bel.id_lang="'.(int)Context::getContext()->language->id.'")
                WHERE (be.status>=0 OR be.status is NULL) AND gs.id_group IN ('.implode(',',array_map('intval',$group_authors)).') GROUP BY c.id_customer
            ');
            if($customers)
            {
                foreach($customers as &$customer)
                {
                    $customer['name'] = $customer['name'] ? $customer['name'] : $customer['firstname'].' '.$customer['lastname'];
                    $customer['link']= Module::getInstanceByName('ybc_blog')->getLink('rss',array('id_author'=>$customer['id_customer'],'is_customer'=>1,'alias'=>Tools::link_rewrite($customer['name'])));
                }
            }

        }
        else
            $customers=array();
        return array(
            'employees'=>$employees,
            'customers'=>$customers
        );
    }
    public static function getFirstCategory($id_post)
    {
        $sql = 'SELECT c.id_category FROM `'._DB_PREFIX_.'ybc_blog_category` c
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_category_shop` cs ON (c.id_category =cs.id_category)
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_category` pc on (pc.id_category =cs.id_category)
                WHERE pc.id_post="'.(int)$id_post.'" AND cs.id_shop="'.(int)Context::getContext()->shop->id.'" ORDER BY c.sort_order ASC';
        return  Db::getInstance()->getValue($sql);
    }
    public static function getRelatedPosts($id_post, $tags, $id_lang = false)
    {
        if(!Configuration::get('YBC_BLOG_DISPLAY_RELATED_POSTS'))
            return false;
        if(!$id_lang)
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $tagElements = array();
        $tagElements[]=0;
        $limit = (int)Configuration::get('YBC_BLOG_RELATED_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_RELATED_POST_NUMBER') : 5;
        if($tags && is_array($tags))
        {

            foreach($tags as $tag)
                if($tag)
                    $tagElements[] = $tag['tag'];
        }
        $sql = "SELECT pl.title, pl.short_description,pl.description,pl.image,pl.thumb, p.*
            FROM `"._DB_PREFIX_."ybc_blog_post` p
            INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (p.id_post= ps.id_post)
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_tag` t ON p.id_post = t.id_post
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON pl.id_post = p.id_post AND pl.id_lang = ".(int)$id_lang."
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_category` pc ON (pc.id_post=p.id_post)
            WHERE ps.id_shop='".(int)Context::getContext()->shop->id."' AND  p.enabled=1 AND (t.tag IN ('".implode("','",array_map('pSQL',$tagElements))."') OR pc.id_category IN (SELECT id_category FROM `"._DB_PREFIX_."ybc_blog_post_category` WHERE id_post=".(int)$id_post.")) AND p.id_post != ".(int)$id_post."
            GROUP BY pl.id_post
            ORDER BY p.sort_order ASC, p.datetime_added DESC
            LIMIT 0,".(int)$limit."
            ";
        return Db::getInstance()->executeS($sql);
    }
    public static function getPostsByIdProduct($id_product,$limit=20)
    {
        $sql ="SELECT p.*,pl.* FROM `"._DB_PREFIX_."ybc_blog_post` p
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (ps.id_post= p.id_post AND id_shop='".(int)Context::getContext()->shop->id."')
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON (p.id_post=pl.id_post)
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_related_product_categories` rpc ON (rpc.id_post=p.id_post)
        LEFT JOIN `"._DB_PREFIX_."category_product` cp ON (cp.id_category = rpc.id_category)      
        WHERE p.enabled=1 AND FIND_IN_SET('".(int)$id_product."', REPLACE(p.exclude_products,'-', ','))=0 AND (FIND_IN_SET('".(int)$id_product."', REPLACE(p.products,'-', ',')) >0 OR cp.id_product='".(int)$id_product."') AND pl.id_lang=".(int)Context::getContext()->language->id.' GROUP BY p.id_post LIMIT 0,'.(int)$limit;
        return Db::getInstance()->executeS($sql);
    }
    public static function getBlogArchives()
    {
        $sql='SELECT count(*) as total_post,YEAR(p.datetime_added) as year_add 
        FROM `'._DB_PREFIX_.'ybc_blog_post` p
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post = ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
        LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
        LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
        WHERE (ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.enabled=1 GROUP BY year_add ORDER BY year_add DESC';
        $years = Db::getInstance()->executeS($sql);
        if($years)
        {
            foreach($years as &$year)
            {
                $sql ='SELECT count(*) as total_post, MONTH(p.datetime_added) as month_add 
                FROM `'._DB_PREFIX_.'ybc_blog_post` p
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON (p.id_post = ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=p.added_by AND p.is_customer=1)
                LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee=p.added_by AND p.is_customer=0)
                LEFT JOIN `'._DB_PREFIX_.'ybc_blog_employee` ybe ON ((ybe.id_employee=c.id_customer AND ybe.is_customer=1) OR (ybe.id_employee=e.id_employee AND ybe.is_customer=0))
                WHERE (ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND enabled=1 AND YEAR(datetime_added)="'.pSQL($year['year_add']).'" GROUP BY month_add ORDER BY month_add DESC';
                $year['months'] = Db::getInstance()->executeS($sql);
                $year['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('year'=>$year['year_add']));
                if($year['months'])
                {
                    foreach($year['months'] as &$month)
                    {
                        $month['link'] = Module::getInstanceByName('ybc_blog')->getLink('blog',array('month'=>$month['month_add'],'year'=>$year['year_add']));
                        $month['month_add'] = Module::getInstanceByName('ybc_blog')->getMonthName($month['month_add']);
                    }
                }
            }
        }
        return $years;
    }
    public static function deleteAllPostCustomerByIdAuthor($id_author)
    {
        $posts= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE is_customer=1 AND added_by="'.(int)$id_author.'"');
        if($posts)
        {
            foreach($posts as $post)
            {
                if(self::_deletePost($post['id_post']))
                {
                    $sql ='SELECT * FROM `'._DB_PREFIX_.'ybc_blog_post` p
                    INNER JOIN  `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON  p.id_post = ps.id_post AND ps.id_shop="'.(int)Context::getContext()->shop->id.'"
                    order by sort_order asc';
                    $posts = Db::getInstance()->executeS($sql);
                    if($posts)
                    {
                        foreach($posts as $key=> $post)
                        {
                            $position=$key+1;
                            Db::getInstance()->execute('update `'._DB_PREFIX_.'ybc_blog_post` SET sort_order ="'.(int)$position.'" WHERE id_post='.(int)$post['id_post']);
                        }
                    }
                }
            }
        }
        return true;
    }
    public static function logViewCustomer($id_post,$browser)
    {
        $ip = Tools::getRemoteAddr();
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_log_view` WHERE '.(Context::getContext()->customer->id ? 'id_customer="'.(int)Context::getContext()->customer->id.'"' :'ip="'.pSQL($ip).'" AND id_customer=0').' AND DAY(datetime_added) ="'.pSQL(date('d')).'" AND MONTH(datetime_added) ="'.pSQL(date('m')).'" AND YEAR(datetime_added) ="'.pSQL(date('Y')).'" AND id_post='.(int)$id_post))
        {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ybc_blog_log_view`(ip,id_post,browser,id_customer,datetime_added) VALUES ("'.pSQL($ip).'","'.(int)$id_post.'","'.pSQL($browser).'","'.(int)Context::getContext()->customer->id.'","'.pSQL(date('Y-m-d H:i:s')).'")');
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_post` SET click_number= click_number+1 WHERE id_post='.(int)$id_post);
        }
    }
    public function addLike($browser)
    {
        $ip = Tools::getRemoteAddr();
        $sql = 'INSERT INTO `'._DB_PREFIX_.'ybc_blog_log_like`(ip,id_post,browser,id_customer,datetime_added) VALUES ("'.pSQL($ip).'","'.(int)$this->id.'","'.pSQL($browser).'","'.(int)Context::getContext()->customer->id.'","'.pSQL(date('Y-m-d H:i:s')).'")';
        Db::getInstance()->execute($sql);
        $this->likes++;
        $this->update();
        /** @var Ybc_blog $blog */
        $blog = Module::getInstanceByName('ybc_blog');
        $blog->_clearCache('single_post.tpl',$blog->_getCacheId(array($this->id)));
        return $this->likes;
    }
    public function unLike()
    {
        if(Context::getContext()->customer->logged)
        {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ybc_blog_log_like` WHERE id_customer="'.(int)Context::getContext()->customer->id.'"');
        }
        if($this->likes >0)
            $this->likes--;
        $this->update();
        /** @var Ybc_blog $blog */
        $blog = Module::getInstanceByName('ybc_blog');
        $blog->_clearCache('single_post.tpl',$blog->_getCacheId(array($this->id)));
        return $this->likes;
    }
    public static function getCountPostByIDCategory($id_category)
    {
        return (int)Db::getInstance()->getValue('SELECT count(DISTINCT p.id_post) FROM `'._DB_PREFIX_.'ybc_blog_post` p
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_category` pc ON pc.id_post=p.id_post AND pc.id_category="'.(int)$id_category.'"
        WHERE p.enabled=1');
    }
    public static function getMaxID()
    {
        return (int)Db::getInstance()->getValue('SELECT MAX(id_post) FROM `'._DB_PREFIX_.'ybc_blog_post`');
    }
    public static function clearAllViewLog()
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ybc_blog_log_view` WHERE id_post IN (SELECT id_post FROM `'._DB_PREFIX_.'ybc_blog_post_shop` WHERE id_shop='.(int)Context::getContext()->shop->id.')');
    }
    public static function clearAllLikeLog()
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ybc_blog_log_like` WHERE id_post IN (SELECT id_post FROM `'._DB_PREFIX_.'ybc_blog_post_shop` WHERE id_shop='.(int)Context::getContext()->shop->id.')');
    }
    public static function getMinYearAddPost($id_post)
    {
        return Db::getInstance()->getValue('SELECT MIN(YEAR(datetime_added)) FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE 1 '.((int)$id_post ? ' AND id_post='.(int)$id_post :''));
    }
    public static function getCountLogViews()
    {
        $sql = "SELECT COUNT(*) FROM `"._DB_PREFIX_."ybc_blog_log_view` lv 
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=lv.id_post)
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop='".(int)Context::getContext()->shop->id."')";
        return  Db::getInstance()->getValue($sql);
    }
    public static function getLogViews($start,$limit)
    {
        $sql = "SELECT lv.*,p.id_post,pl.url_alias,pl.description,pl.short_description,pl.title,m.lastname,m.firstname FROM `"._DB_PREFIX_."ybc_blog_log_view` lv 
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=lv.id_post)
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop='".(int)Context::getContext()->shop->id."')
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang='".(int)Context::getContext()->language->id."')
        LEFT JOIN `"._DB_PREFIX_."customer` m ON (lv.id_customer=m.id_customer)
        ORDER BY lv.datetime_added DESC LIMIT ".(int)$start.", ".(int)$limit;
        return Db::getInstance()->executeS($sql);
    }
    public static function getCountLogLikes()
    {
        $sql = "SELECT COUNT(*) FROM `"._DB_PREFIX_."ybc_blog_log_like` lv 
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=lv.id_post)
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop='".(int)Context::getContext()->shop->id."')";
        return Db::getInstance()->getValue($sql);
    }
    public static function getLogLikes($start,$limit)
    {
        $sql = "SELECT lv.*,p.id_post,pl.url_alias,pl.description,pl.short_description,pl.title,m.lastname,m.firstname FROM `"._DB_PREFIX_."ybc_blog_log_like` lv 
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post` p ON (p.id_post=lv.id_post)
        INNER JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON (p.id_post=ps.id_post AND ps.id_shop='".(int)Context::getContext()->shop->id."')
        LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_lang` pl ON (p.id_post=pl.id_post AND pl.id_lang='".(int)Context::getContext()->language->id."')
        LEFT JOIN `"._DB_PREFIX_."customer` m ON (lv.id_customer=m.id_customer)
        ORDER BY lv.datetime_added DESC LIMIT ".(int)$start.", ".(int)$limit;
        return Db::getInstance()->executeS($sql);
    }
    public static function getCountView($year='',$month='',$day='',$id_post=0)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ybc_blog_log_view` l
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON l.id_post=ps.id_post AND ps.id_shop='.(int)Context::getContext()->shop->id.' 
        WHERE 1 '.($id_post ? ' AND ps.id_post='.(int)$id_post : '').($year ? ' AND YEAR(l.datetime_added) ="'.pSQL($year).'"':'').($month ? ' AND MONTH(l.datetime_added) ="'.pSQL($month).'"':'').($day ? ' AND DAY(l.datetime_added) ="'.pSQL($day).'"':''));
    }
    public static function getCountLike($year='',$month='',$day='',$id_post=0)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ybc_blog_log_like` l
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON l.id_post=ps.id_post AND ps.id_shop='.(int)Context::getContext()->shop->id.'
        WHERE 1 '.($id_post ? ' AND ps.id_post='.(int)$id_post : '').($year ? ' AND YEAR(l.datetime_added) ="'.pSQL($year).'"':'').($month ? ' AND MONTH(l.datetime_added) ="'.pSQL($month).'"':'').($day ? ' AND DAY(l.datetime_added) ="'.pSQL($day).'"':''));
    }
    public static function getCountComment($year='',$month='',$day='',$id_post=0)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'ybc_blog_comment` c 
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_shop` ps ON c.id_post=ps.id_post AND ps.id_shop ="'.(int)Context::getContext()->shop->id.'"
        WHERE 1 '.($id_post ? ' AND ps.id_post='.(int)$id_post : '').($year ? ' AND YEAR(c.datetime_added) ="'.pSQL($year).'"':'').($month ? ' AND MONTH(c.datetime_added) ="'.pSQL($month).'"':'').($day ? ' AND DAY(c.datetime_added) ="'.pSQL($day).'"':''));
    }
    public static function getProductInfo($id_product)
    {
        $product = new Product($id_product, true, Context::getContext()->language->id, Context::getContext()->shop->id);
        if(!Validate::isLoadedObject($product) || !$product->active)
            return '';
        $id_customer = (int)Context::getContext()->customer->id;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $group= new Group($id_group);
        if($group->price_display_method)
            $tax=false;
        else
            $tax=true;

        if(!$product->active)
            return false;
        $pinfo = array();
        $pinfo['id_product'] = $product->id;
        $pinfo['id'] = $product->id;
        $pinfo['id_product_attribute'] = $product->getDefaultIdProductAttribute();
        $pinfo['short_description'] = $product->description_short;
        $pinfo['name'] = $product->name;
        $price = $product->getPrice($tax,null);
        $oldPrice = $product->getPriceWithoutReduct(!$tax,null);
        $discount = $oldPrice - $price;
        $pinfo['price'] = Tools::displayPrice($price);
        $pinfo['old_price'] = Tools::displayPrice($oldPrice);
        $pinfo['discount_percent'] = (($oldPrice - $price) >0 ?  round(($oldPrice - $price) / $oldPrice * 100):0);
        $pinfo['discount_amount'] = Tools::displayPrice($discount);
        $pinfo['product'] = array('id_product' => $id_product);
        $images = $product->getImages((int)Context::getContext()->cookie->id_lang);
        if(isset($images[0]))
            $id_image = Configuration::get('PS_LEGACY_IMAGES') ? ($product->id.'-'.$images[0]['id_image']) : $images[0]['id_image'];
        else
            $id_image = Context::getContext()->language->iso_code.'-default';
        $pinfo['img_url'] =  Context::getContext()->link->getImageLink($product->link_rewrite, $id_image, version_compare(_PS_VERSION_, '1.7', '>=') ? ImageType::getFormattedName('home') : ImageType::getFormatedName('home'));
        $pinfo['link'] = Context::getContext()->link->getProductLink($product,null,null,null,null,null,$product->cache_default_attribute);
        return $pinfo;
    }
    public static function getRelatedProductByProductsStr($id_post,$include_products,$exclude_products)
    {
        if(Configuration::get('YBC_BLOG_SHOW_RELATED_PRODUCTS'))
        {
            $products = array();
            $ids = explode('-', $include_products);
            $exclude_ids = $exclude_products ? explode('-',$exclude_products):array();
            if($ids)
            {
                foreach($ids as $pid)
                {
                    if($pid && !in_array($pid,$exclude_ids) && !isset($products[$pid]))
                    {
                        $product = self::getProductInfo((int)$pid);
                        if($product)
                            $products[$pid] = $product;
                    }
                }
            }
            $category_products = Db::getInstance()->executeS('SELECT DISTINCT p.id_product FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product= ps.id_product AND ps.id_shop = "'.(int)Context::getContext()->shop->id.'")
            INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (p.id_product = cp.id_product)
            INNER JOIN `'._DB_PREFIX_.'ybc_blog_post_related_product_categories` rpc ON (rpc.id_category = cp.id_category AND rpc.id_post="'.(int)$id_post.'")
            WHERE 1 '.($products ? ' AND p.id_product NOT IN ('.implode(',',array_map('intval',array_keys($products))).')':'').($exclude_ids ? ' AND p.id_product NOT IN ('.implode(',',array_map('intval',$exclude_ids)).')':'') );
            if($category_products)
            {
                foreach($category_products as $p)
                {
                    if(!isset($products[$p['id_product']]))
                    {
                        $product = self::getProductInfo((int)$p['id_product']);
                        if($product)
                            $products[$p['id_product']] = $product;
                    }
                }
            }
            return $products;
        }
        return false;
    }
}
