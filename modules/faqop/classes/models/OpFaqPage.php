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

class OpFaqPage extends OpFaqObjectModel
{
    public $description;

    public $description_tag;

    public $description_class;

    public $show_description;

    public static $definition = [
        'table' => 'op_faq_pages',
        'primary' => 'id',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'hook_name' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'block_type' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
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
            'description_tag' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'description_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'show_markup' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'show_description' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'accordion' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
            // Lang fields
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'description' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 65535],
        ],
    ];

    public function __construct($module, $id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($module, $id_item, $id_lang, $id_shop);
        $this->block_type = 'page';
    }
}
