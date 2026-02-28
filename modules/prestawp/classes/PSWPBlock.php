<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2018 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PSWPBlock extends ObjectModel
{
    public $hook;
    public $title;
    public $number_of_posts;
    public $grid_columns;
    public $show_featured_image;
    public $carousel;
    public $carousel_autoplay;
    public $carousel_dots;
    public $carousel_arrows;
    public $show_preview;
    public $show_preview_no_img;
    public $masonry;
    public $title_color;
    public $title_bg_color;
    public $show_article_footer;
    public $show_full_posts;
    public $strip_tags;
    public $truncate;
    public $ajax_load;

    public $shops;
    public $active;

    public $wp_categories;
    public $wp_posts;

    public $ps_categories = [];

    public static $definition = [
        'table' => 'prestawp_block',
        'primary' => 'id_prestawp_block',
        'multilang' => true,
        'fields' => [
            // Classic fields
            'hook' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'number_of_posts' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'grid_columns' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'show_featured_image' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'carousel' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'carousel_autoplay' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'carousel_dots' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'carousel_arrows' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'show_preview' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'show_preview_no_img' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'masonry' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'title_color' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'title_bg_color' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'show_article_footer' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'show_full_posts' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'strip_tags' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'wp_categories' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'multiple_values' => true],
            'wp_posts' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'multiple_values' => true],
            'truncate' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'ajax_load' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            // Lang fields
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
        $this->loadRelations();

        $this->shops = $this->getShops();
        $this->loadWpCategories();
        $this->loadWpPosts();
    }

    public function validateAllFields()
    {
        $errors = [];

        $valid = $this->validateFields(false, true);
        if ($valid !== true) {
            $errors[] = $valid . "\n";
        }
        $valid_lang = $this->validateFieldsLang(false, true);
        if ($valid_lang !== true) {
            $errors[] = $valid_lang . "\n";
        }

        return $errors;
    }

    public function validateField($field, $value, $id_lang = null, $skip = [], $human_errors = true)
    {
        return parent::validateField($field, $value, $id_lang, $skip, $human_errors);
    }

    public function save($null_values = false, $auto_date = true)
    {
        $result = parent::save($null_values, $auto_date);

        if ($result && $this->id) {
            // save product and category relations
            $this->saveRelations();

            // save shop data
            if (Shop::isFeatureActive()) {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'prestawp_block_shop`
                     WHERE `id_prestawp_block` = ' . (int) $this->id .
                    ($this->shops
                        ? ' AND `id_shop` NOT IN (' . implode(',', array_map('intval', $this->shops)) . ')' : '')
                );
                foreach ($this->shops as $id_shop) {
                    Db::getInstance()->execute(
                        'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'prestawp_block_shop`
                         (`id_prestawp_block`, `id_shop`)
                         VALUES
                         (' . (int) $this->id . ', ' . (int) $id_shop . ')'
                    );
                }
            } else {
                Db::getInstance()->execute(
                    'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'prestawp_block_shop`
                     (`id_prestawp_block`, `id_shop`)
                     VALUES
                     (' . (int) $this->id . ', ' . (int) Configuration::get('PS_SHOP_DEFAULT') . ')'
                );
            }

            $module = Module::getInstanceByName('prestawp');
            $module->clearSmartyCache();
            $module->registerHook($this->hook);
        }

        return $result;
    }

    public static function getBlocksFront($hook)
    {
        if (!$hook) {
            return null;
        }

        $context = Context::getContext();
        $id_category = Tools::getValue('id_category');
        $id_product = Tools::getValue('id_product');
        $controller = Tools::getValue('controller');
        $categories = [];

        // if need to check category restriction
        if ($controller == 'category' && $id_category) {
            $categories[] = $id_category;
        } elseif ($controller == 'product' && $id_product) {
            $categories = Product::getProductCategories($id_product);
        }

        $rows = Db::getInstance()->executeS(
            'SELECT `id_prestawp_block`
             FROM `' . _DB_PREFIX_ . 'prestawp_block`
             WHERE `active` = 1
              ' . (is_array($hook)
                ? ' AND `hook` IN ("' . implode('", "', array_map('pSQL', $hook)) . '")'
                : ' AND `hook` = "' . pSQL($hook) . '"')
            // select blocks assigned to this category OR those who doesn't have any restrictions
            . ($categories
                ? ' AND ( 
                      `id_prestawp_block` IN (
                        SELECT `id_prestawp_block` FROM `' . _DB_PREFIX_ . 'prestawp_block_relation`
                        WHERE `id_object` IN ("' . implode('", "', array_map('intval', $categories)) . '")
                         AND `type` = "category" 
                      )
                      OR `id_prestawp_block` IN (
                        SELECT `id_prestawp_block` FROM `' . _DB_PREFIX_ . 'prestawp_block`
                        WHERE `id_prestawp_block` NOT IN (
                            SELECT `id_prestawp_block` FROM `' . _DB_PREFIX_ . 'prestawp_block_relation`
                        )
                      )
                    )'
                : '')
            . (Shop::isFeatureActive()
                ? ' AND `id_prestawp_block` IN (
                    SELECT `id_prestawp_block` FROM `' . _DB_PREFIX_ . 'prestawp_block_shop`
                    WHERE `id_shop` = ' . (int) $context->shop->id . '
                )'
                : '')
        );

        $blocks = [];
        foreach ($rows as $row) {
            $block = new PSWPBlock($row['id_prestawp_block'], $context->language->id);

            // check if set category restriction
            if ($block->ps_categories && $controller != 'category' && $controller != 'product') {
                continue;
            }

            $blocks[] = $block;
        }

        return $blocks;
    }

    protected function getPSVersion($without_dots = false)
    {
        $ps_version = _PS_VERSION_;
        $ps_version = Tools::substr($ps_version, 0, 3);

        if ($without_dots) {
            $ps_version = str_replace('.', '', $ps_version);
        }

        return (float) $ps_version;
    }

    public function getPostsFront()
    {
        if ($this->ajax_load) {
            return [];
        }

        $module = Module::getInstanceByName('prestawp');
        $this->loadWpCategories();
        $this->loadWpPosts();
        $this->wp_posts = array_filter($this->wp_posts);
        $this->wp_categories = array_filter($this->wp_categories);

        $params = [];
        if (count($this->wp_posts)) {
            $params['posts'] = $this->wp_posts;
        } elseif (count($this->wp_categories)) {
            $params['categories'] = $this->wp_categories;
        }

        return $module->getWPData('posts', $this->number_of_posts, $params);
    }

    public function loadWpCategories()
    {
        if (is_string($this->wp_categories)) {
            $this->wp_categories = explode(',', $this->wp_categories);
        }

        if (!$this->wp_categories) {
            $this->wp_categories = [];
        }
    }

    public function loadWpPosts()
    {
        if (is_string($this->wp_posts)) {
            $this->wp_posts = explode(',', $this->wp_posts);
        }

        if (!$this->wp_posts) {
            $this->wp_posts = [];
        }
    }

    protected function saveRelations()
    {
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'prestawp_block_relation`
             WHERE `id_prestawp_block` = ' . (int) $this->id
        );
        // categories:
        if (is_array($this->ps_categories) && count($this->ps_categories)) {
            foreach ($this->ps_categories as $id_category) {
                Db::getInstance()->execute(
                    'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'prestawp_block_relation`
                     (`id_prestawp_block`, `id_object`, `type`)
                     VALUES
                     (' . (int) $this->id . ', ' . (int) $id_category . ', "category")'
                );
            }
        }
    }

    public function loadRelations()
    {
        if (!$this->id) {
            return false;
        }
        $this->ps_categories = [];

        $items = Db::getInstance()->executeS(
            'SELECT *
             FROM `' . _DB_PREFIX_ . 'prestawp_block_relation`
             WHERE `id_prestawp_block` = ' . (int) $this->id
        );
        foreach ($items as $item) {
            if ($item['type'] == 'category') {
                $this->ps_categories[] = $item['id_object'];
                $this->ps_categories = array_filter($this->ps_categories);
                $this->ps_categories = array_unique($this->ps_categories);
            }
        }
    }

    public function getPSCategoryNames()
    {
        if ($this->ps_categories) {
            $context = Context::getContext();

            return Db::getInstance()->getValue(
                'SELECT GROUP_CONCAT(cl.`name` SEPARATOR ", ")
                 FROM `' . _DB_PREFIX_ . 'prestawp_block_relation` br
                 LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON br.`id_object` = cl.`id_category`
                  AND cl.`id_lang` = ' . (int) $context->language->id . '
                 WHERE br.`id_prestawp_block` = ' . (int) $this->id . '
                  AND br.`type` = "category"'
            );
        }

        return '';
    }

    public function getShops()
    {
        if (!$this->id) {
            return [];
        }

        $result = [];

        if (Shop::isFeatureActive()) {
            $shops = Db::getInstance()->executeS(
                'SELECT DISTINCT `id_shop`
                 FROM `' . _DB_PREFIX_ . 'prestawp_block_shop`
                 WHERE `id_prestawp_block` = ' . (int) $this->id
            );

            foreach ($shops as $shop) {
                $result[] = $shop['id_shop'];
            }
        } else {
            $result[] = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

        return $result;
    }

    public static function getShopsStatic($id_block)
    {
        $block = new PSWPBlock($id_block);

        return $block->getShops();
    }
}
