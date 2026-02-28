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
class Ybc_blog_slide_class extends ObjectModel
{
    public $id_slide;
    public $id_shop;
    public $caption;
	public $enabled;
	public $image;
    public $sort_order;
    public $url;
    public static $definition = array(
		'table' => 'ybc_blog_slide',
		'primary' => 'id_slide',
		'multilang' => true,
		'fields' => array(
			'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool',),
            'sort_order' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
                     
            // Lang fields
            'image' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 1000,'lang'=>true),   
            'url' =>	array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isCleanHtml', 'size' => 1000),
            'url' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml','lang'=>true, 'size' => 1000),
            'caption' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 900000),            
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        if($this->id)
            $this->id_shop = Db::getInstance()->getValue('SELECT id_shop FROM `'._DB_PREFIX_.'ybc_blog_slide_shop` where id_slide= '.(int)$this->id);
	}
    public function add($autodate = true, $null_values = false)
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$res = parent::add($autodate, $null_values);
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'ybc_blog_slide_shop` (`id_shop`, `id_slide`)
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
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$image))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$image);
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ybc_blog_slide_shop` WHERE id_slide='.(int)$this->id);
            $slides = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ybc_blog_slide` s
                INNER JOIN `'._DB_PREFIX_.'ybc_blog_slide_shop` ss ON (s.id_slide =ss.id_slide AND ss.id_shop="'.(int)Context::getContext()->shop->id.'")
                ORDER BY sort_order asc');
            if($slides)
            {
                foreach($slides as $key=>$slide)
                {
                    $position=$key+1;
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_slide` SET sort_order="'.(int)$position.'" WHERE id_slide='.(int)$slide['id_slide']);
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
        if($this->image)
        {
            foreach($this->image as $id_lang => $image)
            {
                if($image)
                    $this->image[$id_lang] = time().pathinfo($image, PATHINFO_BASENAME);
            }
        }
        if($this->add())
        {
            if($this->image)
            {
                foreach($this->image as $id_lang=> $image)
                {
                    if($image)
                        @copy(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$oldImages[$id_lang],_PS_YBC_BLOG_IMG_DIR_.'slide/'.$image);
                }
            }    
            return $this->id;
        }
        return false;        
    }
    public static function getSlidesWithFilter($filter = false, $sort = false, $start = false, $limit = false)
    {
        $req = "SELECT s.*, sl.caption, sl.url,sl.image
            FROM `"._DB_PREFIX_."ybc_blog_slide` s
            INNER JOIN `"._DB_PREFIX_."ybc_blog_slide_shop` ss ON (s.id_slide=ss.id_slide AND ss.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_slide_lang` sl ON s.id_slide = sl.id_slide
            WHERE sl.id_lang = ".(int)Context::getContext()->language->id.($filter ? $filter : '')." 
            ORDER BY ".($sort ? $sort : '')." s.id_slide ASC " . ($start !== false && $limit ? " LIMIT ".(int)$start.", ".(int)$limit : "");
        return Db::getInstance()->executeS($req);
    }
    public static function countSlidesWithFilter($filter = false)
    {
        $req = "SELECT COUNT(s.id_slide)
            FROM `"._DB_PREFIX_."ybc_blog_slide` s
            INNER JOIN `"._DB_PREFIX_."ybc_blog_slide_shop` ss ON (s.id_slide=ss.id_slide AND ss.id_shop='".(int)Context::getContext()->shop->id."')
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_slide_lang` sl ON s.id_slide = sl.id_slide
            WHERE sl.id_lang = ".(int)Context::getContext()->language->id.($filter ? $filter : '');
        return Db::getInstance()->getValue($req);
    }
    public static function updateSliderOrdering($slides,$page=1)
    {
        if($slides)
        {
            if($page<1)
                $page=1;
            foreach($slides as $key=> $slide)
            {
                $position=  1+ $key + ($page-1)*20;
                if($key==0)
                {
                    Hook::exec('actionUpdateBlog', array(
                        'id_slide' =>(int)$slide,
                    ));
                }
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_slide` SET sort_order="'.(int)$position.'" WHERE id_slide='.(int)$slide);
            }
        }

        return true;
    }
    public static function getMaxSortOrder()
    {
        return (int)Db::getInstance()->getValue('
            SELECT MAX(s.sort_order) FROM `'._DB_PREFIX_.'ybc_blog_slide` s
            INNER JOIN `'._DB_PREFIX_.'ybc_blog_slide_shop` ss ON (s.id_slide =ss.id_slide AND ss.id_shop="'.(int)Context::getContext()->shop->id.'")');
    }
}