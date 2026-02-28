<?php
/**
 *
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

if (!defined('_PS_VERSION_')) {
    exit;
}
 require_once(_PS_ROOT_DIR_ . '/modules/pm_seointernallinking/models/seointernallinkingcoreclass.php');
class seointernallinkinggroupclass extends ObjectModel
{
    public $id_group;
    public $name;
    public $category_type = 0;
    public $product_type = 0;
    public $manufacturer_type = 0;
    public $supplier_type = 0;
    public $cms_type = 0;
    public $group_type = 1;
    public $manufacturers = array();
    public $suppliers = array();
    public $products = array();
    public $categories = array();
    public $cms_pages = array();
    protected $tables = array('pm_seointernallinking_group', 'pm_seointernallinking_group_lang');
    protected $fieldsRequired     = array();
    protected $fieldsSize         = array('group_type' => 1, 'category_type'=> 1, 'product_type'=> 1, 'manufacturer_type'=> 1, 'supplier_type' => 1, 'cms_type' => 1);
    protected $fieldsValidate     = array(
                                        'group_type' => 'isUnsignedId',
                                        'category_type' => 'isUnsignedId',
                                        'product_type' => 'isUnsignedId',
                                        'manufacturer_type' => 'isUnsignedId',
                                        'supplier_type' => 'isUnsignedId',
                                        'cms_type' => 'isUnsignedId'
                                    );
    protected $table              =   'pm_seointernallinking_group';
    public $identifier         =   'id_group';
    protected $fieldsRequiredLang =   array('name');
    protected $fieldsSizeLang     =   array('name' => 255);
    protected $fieldsValidateLang =   array('name' => 'isGenericName');
    public static $valid_page       =   array();
    public static $definition = array(
        'table' => 'pm_seointernallinking_group',
        'primary' => 'id_group',
        'multilang' => true,
        'multilang_shop' => false,
        'fields' => array(
            'group_type' =>             array('type' => 1, 'validate' => 'isUnsignedInt'),
            'category_type' =>          array('type' => 1, 'validate' => 'isUnsignedInt'),
            'product_type' =>           array('type' => 1, 'validate' => 'isUnsignedInt'),
            'manufacturer_type' =>      array('type' => 1, 'validate' => 'isUnsignedInt'),
            'supplier_type' =>          array('type' => 1, 'validate' => 'isUnsignedInt'),
            'cms_type' =>               array('type' => 1, 'validate' => 'isUnsignedInt'),
        ),
    );
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (version_compare(_PS_VERSION_, '1.5.2.0', '<=') && class_exists("ShopPrestaModule")) {
            ShopPrestaModule::setAssoTable(self::$definition['table']);
        } else {
            Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        }
        parent::__construct($id, $id_lang, $id_shop);
    }
    public function getFields()
    {
        $fields = array();
        parent::validateFields();
        if (isset($this->id_group)) {
            $fields['id_group'] = (int)$this->id_group;
        }
        $fields['group_type']                   = (int)$this->group_type;
        $fields['category_type']                = (int)$this->category_type;
        $fields['product_type']                 = (int)$this->product_type;
        $fields['manufacturer_type']            = (int)$this->manufacturer_type;
        $fields['supplier_type']                = (int)$this->supplier_type;
        $fields['cms_type']                     = (int)$this->cms_type;
        return $fields;
    }
    public function getTranslationsFieldsChild()
    {
        parent::validateFieldsLang();
        return parent::getTranslationsFields(array('name'));
    }
    public function getManufacturers()
    {
        $manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT psmr.id_manufacturer, m.name
            FROM `'._DB_PREFIX_.'pm_seointernallinking_manufacturer_rules` psmr
            LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.id_manufacturer=psmr.id_manufacturer
            WHERE psmr.`'.$this->identifier.'` = '.(int)$this->id .
            ' ORDER BY m.name ASC');
        return $manufacturers;
    }
    public function getSuppliers()
    {
        $suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT pssr.id_supplier, s.name
            FROM `'._DB_PREFIX_.'pm_seointernallinking_supplier_rules` pssr
            LEFT JOIN `'._DB_PREFIX_.'supplier` s ON s.id_supplier=pssr.id_supplier
            WHERE pssr.`'.$this->identifier.'` = '.(int)$this->id .
            ' ORDER BY s.name ASC');
        return $suppliers;
    }
    public function getProducts()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT pspr.id_product, CONCAT(p.`id_product`, \' - \', IFNULL(CONCAT(NULLIF(TRIM(p.reference), \'\'), \' - \'), \'\'), pl.`name`) AS name
            FROM `'._DB_PREFIX_.'pm_seointernallinking_product_rules` pspr
            LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product=pspr.id_product
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON p.id_product=pl.id_product
            WHERE pspr.`'.$this->identifier.'` = '.(int)$this->id.
            ' AND pl.`id_lang`='.(int)Configuration::get('PS_LANG_DEFAULT').
            (Shop::isFeatureActive() ? Shop::addSqlRestrictionOnLang('pl'):'').
            ' ORDER BY pl.name ASC');
        return $products;
    }
    public function getCategories()
    {
        $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT pscr.id_category, cl.`name`
            FROM `'._DB_PREFIX_.'pm_seointernallinking_category_rules` pscr
            LEFT JOIN `'._DB_PREFIX_.'category` c ON c.id_category=pscr.id_category
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.id_category=cl.id_category
            WHERE pscr.`'.$this->identifier.'` = '.(int)$this->id.
            ' AND cl.`id_lang`='.(int)Configuration::get('PS_LANG_DEFAULT').
            (Shop::isFeatureActive() ? Shop::addSqlRestrictionOnLang('cl'):'').
            ' ORDER BY cl.name ASC');
        return $categories;
    }
    public function getCMSPages()
    {
        $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT pscr.id_cms, cl.`meta_title`
            FROM `'._DB_PREFIX_.'pm_seointernallinking_cms_rules` pscr
            LEFT JOIN `'._DB_PREFIX_.'cms` c ON c.id_cms=pscr.id_cms
            LEFT JOIN `'._DB_PREFIX_.'cms_lang` cl ON c.id_cms=cl.id_cms
            ' . SeoInternalLinkingCoreClass::addSqlAssociation('cms', 'c', 'id_cms', true, null, false).'
            WHERE pscr.`'.$this->identifier.'` = '.(int)$this->id.
            ' AND cl.`id_lang`='.(int)Configuration::get('PS_LANG_DEFAULT').
            ' GROUP BY pscr.id_cms'.
            ' ORDER BY cl.meta_title ASC');
        return $categories;
    }
    private function saveManufacturers()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_manufacturer_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
        if (is_array($this->manufacturers) && sizeof($this->manufacturers)) {
            foreach ($this->manufacturers as $id_manufacturer) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('INSERT INTO `'._DB_PREFIX_.'pm_seointernallinking_manufacturer_rules` (`'.$this->identifier.'`, id_manufacturer) VALUES ("'.(int)$this->id.'", "'.(int)$id_manufacturer.'")');
            }
        }
    }
    private function saveSuppliers()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_supplier_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
        if (is_array($this->suppliers) && sizeof($this->suppliers)) {
            foreach ($this->suppliers as $id_supplier) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('INSERT INTO `'._DB_PREFIX_.'pm_seointernallinking_supplier_rules` (`'.$this->identifier.'`, id_supplier) VALUES ("'.(int)$this->id.'", "'.(int)$id_supplier.'")');
            }
        }
    }
    private function saveProducts()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_product_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
        if (is_array($this->products) && sizeof($this->products)) {
            foreach ($this->products as $id_product) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('INSERT INTO `'._DB_PREFIX_.'pm_seointernallinking_product_rules` (`'.$this->identifier.'`, id_product) VALUES ("'.(int)$this->id.'", "'.(int)$id_product.'")');
            }
        }
    }
    private function saveCategories()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_category_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
        if (is_array($this->categories) && sizeof($this->categories)) {
            foreach ($this->categories as $id_category) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('INSERT INTO `'._DB_PREFIX_.'pm_seointernallinking_category_rules` (`'.$this->identifier.'`, id_category) VALUES ("'.(int)$this->id.'", "'.(int)$id_category.'")');
            }
        }
    }
    private function saveCMSPages()
    {
        Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_cms_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
        if (is_array($this->cms_pages) && sizeof($this->cms_pages)) {
            foreach ($this->cms_pages as $id_cms) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('INSERT INTO `'._DB_PREFIX_.'pm_seointernallinking_cms_rules` (`'.$this->identifier.'`, id_cms) VALUES ("'.(int)$this->id.'", "'.(int)$id_cms.'")');
            }
        }
    }
    public function save($nullValues = false, $autodate = true)
    {
        if (parent::save()) {
            $this->saveManufacturers();
            $this->saveSuppliers();
            $this->saveProducts();
            $this->saveCategories();
            $this->saveCMSPages();
            return true;
        }
        return false;
    }
    public function delete()
    {
        if (!$this->hasMultishopEntries()) {
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking` WHERE `'.$this->identifier.'` = '.(int)$this->id);
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_group_lang` WHERE `'.$this->identifier.'` = '.(int)$this->id);
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_category_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_product_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_supplier_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_manufacturer_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
            Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.'pm_seointernallinking_cms_rules` WHERE `'.$this->identifier.'` = '.(int)$this->id);
        }
        return parent::delete();
    }
}
