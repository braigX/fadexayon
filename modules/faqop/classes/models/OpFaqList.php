<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/models/OpFaqObjectModel.php';

class OpFaqList extends OpFaqObjectModel
{
    public $not_all_pages;

    public $all_products;

    public $select_products_by_id;

    public $product_ids;

    public $select_products_by_category;

    public $category_ids_p;

    public $select_products_by_brand;

    public $brand_ids_p;

    public $select_products_by_tag;

    public $tag_ids_p;

    public $select_products_by_feature;

    public $feature_ids_p;

    public $all_categories;

    public $category_ids;

    public $all_brands;

    public $brand_ids;

    public $all_cms_pages;

    public $cms_page_ids;

    public $all_without;

    public $special_ids;

    public $position;

    public $not_all_languages;

    public $languages;

    public $not_all_currencies;

    public $currencies;

    public $not_all_customer_groups;

    public $customer_groups;

    public $admin_name;

    public $is_only_default_category;

    public static $definition = [
        'table' => 'op_faq_lists',
        'primary' => 'id',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'hook_name' => ['type' => self::TYPE_STRING, 'validate' => 'isHookName', 'size' => 191],
            'block_type' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'admin_name' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'not_all_pages' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'all_products' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'select_products_by_id' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'product_ids' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'select_products_by_category' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'category_ids_p' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'is_only_default_category' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'select_products_by_brand' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'brand_ids_p' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'select_products_by_tag' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'tag_ids_p' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'select_products_by_feature' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'feature_ids_p' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'all_categories' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'category_ids' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'all_brands' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'brand_ids' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'all_cms_pages' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'cms_page_ids' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'all_without' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'special_ids' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'not_all_languages' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'languages' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'not_all_currencies' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'currencies' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'not_all_customer_groups' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'customer_groups' => ['type' => self::TYPE_HTML, 'lang' => false, 'size' => 65535],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            'show_title' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'block_tag' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'block_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'title_tag' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'title_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'content_tag' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'content_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'item_tag' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'item_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'question_tag' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'question_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'answer_tag' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'answer_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'show_markup' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'accordion' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            // Lang fields
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
        ],
    ];

    public function __construct($module, $id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($module, $id_item, $id_lang, $id_shop);
        $this->block_type = 'list';
        if (!$this->position) {
            $this->position = 0;
        }
    }
}
