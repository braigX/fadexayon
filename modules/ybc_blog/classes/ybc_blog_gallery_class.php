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
class Ybc_blog_gallery_class extends ObjectModel
{
    public $id_gallery;
    public $id_shop;
    public $title;
    public $description;
	public $enabled;
	public $image;
    public $thumb;
    public $sort_order;
    public $is_featured;
    public static $definition = array(
		'table' => 'ybc_blog_gallery',
		'primary' => 'id_gallery',
		'multilang' => true,
		'fields' => array(
			'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'sort_order' => array('type' => self::TYPE_INT),
            'is_featured' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),            
            // Lang fields
            'image' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500,'lang'=>true),
            'thumb' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 500,'lang'=>true),
            'title' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 900000),            
            'description' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 900000),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        if($this->id)
            $this->id_shop = Db::getInstance()->getValue('SELECT id_shop FROM `'._DB_PREFIX_.'ybc_blog_gallery_shop` where id_gallery= '.(int)$this->id);
        $languages = Language::getLanguages(false);        
        foreach($languages as $lang)
        {
            foreach(self::$definition['fields'] as $field => $params)
            {   
                $temp = $this->$field; 
                if(isset($params['lang']) && $params['lang'] && !isset($temp[$lang['id_lang']]))
                {                      
                    $temp[$lang['id_lang']] = '';                        
                }
                $this->$field = $temp;
            }
        }
        unset($context);
	}
    public function add($autodate = true, $null_values = false)
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$res = parent::add($autodate, $null_values);
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'ybc_blog_gallery_shop` (`id_shop`, `id_gallery`)
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
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$image))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$image);
            }
            if($this->thumb)
            {
                foreach($this->thumb as $thumb)
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$thumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$thumb);
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ybc_blog_gallery_shop` WHERE id_gallery='.(int)$this->id);
            $galleries = Db::getInstance()->executeS('
                SELECT * FROM `'._DB_PREFIX_.'ybc_blog_gallery` g, `'._DB_PREFIX_.'ybc_blog_gallery_shop` gs
                WHERE g.id_gallery=gs.id_gallery AND gs.id_shop="'.(int)Context::getContext()->shop->id.'" ORDER BY g.sort_order asc');
            if($galleries)
            {
                foreach($galleries as $key=> $gallery)
                {
                    $position = $key+1;
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_gallery` SET sort_order="'.(int)$position.'" WHERE id_gallery='.(int)$gallery['id_gallery']);
                }
            }
            return true;
        }
        return false;
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
                    $this->thumb[$id_lang]= time().pathinfo($thumb, PATHINFO_BASENAME);
            }
        }    
        if($this->add())
        {
            if($this->image)
            {
                foreach($this->image as $id_lang=>$image)
                {
                    if($image)
                        @copy(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImages[$id_lang],_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$image);
                }
            }
            if($this->thumb)
            {
                foreach($this->thumb as $id_lang=>$thumb)
                {
                    if($thumb)
                        @copy(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$oldThumbs[$id_lang],_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$thumb);
                }
            }
            return $this->id;
        }
        return false;        
    }
    public static function getGalleriesWithFilter($filter = false, $sort = false, $start = false, $limit = false)
    {
        $req = "SELECT g.*, gl.title, gl.description,gl.image,gl.thumb
            FROM `"._DB_PREFIX_."ybc_blog_gallery` g
            INNER JOIN `"._DB_PREFIX_."ybc_blog_gallery_shop` gs ON (g.id_gallery=gs.id_gallery AND gs.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_gallery_lang` gl ON g.id_gallery = gl.id_gallery
            WHERE gl.id_lang = ".(int)Context::getContext()->language->id.($filter ? $filter : '')." 
            ORDER BY ".($sort ? $sort : '')." g.id_gallery ASC " . ($start !== false && $limit ? " LIMIT ".(int)$start.", ".(int)$limit : "");

        return Db::getInstance()->executeS($req);
    }
    public static function countGalleriesWithFilter($filter = false)
    {
        $req = "SELECT COUNT(g.id_gallery)
            FROM `"._DB_PREFIX_."ybc_blog_gallery` g
            INNER JOIN `"._DB_PREFIX_."ybc_blog_gallery_shop` gs ON (g.id_gallery=gs.id_gallery AND gs.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_gallery_lang` gl ON g.id_gallery = gl.id_gallery
            WHERE gl.id_lang = ".(int)Context::getContext()->language->id.($filter ? $filter : '');
        return Db::getInstance()->getValue($req);
    }
    public static function updateGalleryOrdering($galleries,$page=1)
    {
        if($page <1)
            $page =1;
        if($galleries)
        {
            foreach($galleries as $key=> $gallery)
            {
                $position=  1+ $key + ($page-1)*20;
                Hook::exec('actionUpdateBlog', array(
                    'id_gallery' =>(int)$gallery,
                ));
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_gallery` SET sort_order="'.(int)$position.'" WHERE id_gallery='.(int)$gallery);
            }
        }
        return true;
    }
    public static function getMaxSortOrder()
    {
        return (int)Db::getInstance()->getValue('
            SELECT MAX(g.sort_order) FROM `'._DB_PREFIX_.'ybc_blog_gallery` g
            INNER JOIN `'._DB_PREFIX_.'ybc_blog_gallery_shop` gs ON g.id_gallery=gs.id_gallery AND gs.id_shop="'.(int)Context::getContext()->shop->id.'"'
        );
    }
}