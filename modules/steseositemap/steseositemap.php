<?php
/**
 * STE SEO Sitemap & Robots Module
 *
 * @author    Custom
 * @copyright 2026 Custom
 * @license   Commercial
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class SteSeoSitemap extends Module
{
    public function __construct()
    {
        $this->name = 'steseositemap';
        $this->tab = 'seo';
        $this->version = '1.0.0';
        $this->author = 'Custom';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('STE SEO Master: Multistore Sitemaps & Robots');
        $this->description = $this->l('Generates isolated sitemaps and robots.txt per shop. SEO Diagnostics included.');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install() &&
            $this->installDb() &&
            $this->installTab() &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('moduleRoutes') &&
            Configuration::updateValue('STE_SKIP_DEFAULT', 0) &&
            Configuration::updateValue('STE_MANAGE_ROBOTS', 1);
    }

    public function hookModuleRoutes($params)
    {
        return [
            'module-steseositemap-robots' => [
                'controller' => 'robots',
                'rule' => 'robots.txt',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'steseositemap',
                ],
            ],
            'module-steseositemap-sitemap' => [
                'controller' => 'sitemap',
                'rule' => 'sitemap.xml',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'steseositemap',
                ],
            ]
        ];
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');
    }
    
    public function uninstall()
    {
        return parent::uninstall() && 
            $this->uninstallDb() && 
            $this->uninstallTab();
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminSteSeoSitemap';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'STE SEO Master';
        }
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentThemes'); // SEO is often under Design/Themes or Shop Parameters
        $tab->module = $this->name;
        return $tab->add();
    }

    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminSteSeoSitemap');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    public function installDb()
    {
        $sql = [];
        // Table for tracking SEO status of individual URLs per shop
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ste_seo_index` (
            `id_seo_index` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shop` INT(11) UNSIGNED NOT NULL,
            `entity_type` VARCHAR(32) NOT NULL,
            `id_entity` INT(11) UNSIGNED NOT NULL,
            `loc` TEXT NOT NULL,
            `last_mod` DATETIME NULL,
            `changefreq` VARCHAR(10) DEFAULT "weekly",
            `priority` DECIMAL(2,1) DEFAULT 0.5,
            `is_indexable` TINYINT(1) DEFAULT 1,
            `image` TEXT NULL,
            PRIMARY KEY (`id_seo_index`),
            INDEX `idx_shop_entity` (`id_shop`, `entity_type`, `id_entity`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Table for dynamic robots.txt content per shop
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ste_robots` (
            `id_ste_robots` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shop` INT(11) UNSIGNED NOT NULL,
            `content` TEXT,
            PRIMARY KEY (`id_ste_robots`),
            UNIQUE KEY `idx_shop_robots` (`id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    public function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ste_seo_index`') &&
               Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ste_robots`');
    }

    public function getContent()
    {
        $this->checkTableSchema();
        // Redirect to our custom Admin Controller for a better UI experience
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminSteSeoSitemap')
        );
    }

    private function checkTableSchema()
    {
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'ste_seo_index`');
        $hasImage = false;
        foreach ($columns as $column) {
            if ($column['Field'] == 'image') {
                $hasImage = true;
                break;
            }
        }

        if (!$hasImage) {
            Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ste_seo_index` ADD `image` TEXT NULL AFTER `is_indexable`');
        }
    }
}
