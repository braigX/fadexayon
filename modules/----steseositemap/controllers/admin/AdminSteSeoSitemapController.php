<?php

require_once _PS_MODULE_DIR_ . 'steseositemap/src/Core/SteSitemapGenerator.php';
require_once _PS_MODULE_DIR_ . 'steseositemap/src/Core/SteRobotsManager.php';
require_once _PS_MODULE_DIR_ . 'steseositemap/src/Core/SteSeoAnalyzer.php';

class AdminSteSeoSitemapController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('STE SEO Master');
    }

    public function initContent()
    {
        parent::initContent();

        $shops = Shop::getShops();
        $sitemaps_info = [];
        $basePath = _PS_ROOT_DIR_;

        foreach ($shops as $shop) {
            $filename = 'sitemap-' . $shop['id_shop'] . '.xml';
            
            // Get robots Content
            $robotsContent = Db::getInstance()->getValue('SELECT content FROM ' . _DB_PREFIX_ . 'ste_robots WHERE id_shop = ' . (int)$shop['id_shop']);
            
            $sitemaps_info[] = [
                'id_shop' => $shop['id_shop'],
                'name' => $shop['name'],
                'url' => $this->getShopUrl($shop['id_shop']),
                'file_exists' => file_exists($basePath . '/' . $filename),
                'filename' => $filename,
                'last_mod' => file_exists($basePath . '/' . $filename) ? date("F d Y H:i:s", filemtime($basePath . '/' . $filename)) : 'Never',
                'robots_content' => $robotsContent
            ];
        }

        $robotManager = new SteRobotsManager();
        $robotIssues = $robotManager->analyze($basePath . '/robots.txt');

        // Full SEO Analysis
        $seoAnalyzer = new SteSeoAnalyzer();
        $seoIssues = $seoAnalyzer->analyzeGlobal();
        
        // Merge issues
        $allIssues = array_merge($robotIssues, $seoIssues);
        
        // Cron setup
        $cronToken = Configuration::get('STE_SEO_CRON_TOKEN');
        if (!$cronToken) {
            $cronToken = Tools::passwdGen(16);
            Configuration::updateValue('STE_SEO_CRON_TOKEN', $cronToken);
        }
        $cronUrl = $this->context->link->getBaseLink() . 'modules/' . $this->module->name . '/cron.php?token=' . $cronToken;

        $this->context->smarty->assign([
            'ste_shops' => $sitemaps_info,
            'ste_robot_issues' => $allIssues,
            'ste_cron_url' => $cronUrl,
            'ste_config' => [
                'skip_default' => Configuration::get('STE_SKIP_DEFAULT'),
                'manage_robots' => Configuration::get('STE_MANAGE_ROBOTS')
            ],
            'ste_generate_url' => $this->context->link->getAdminLink('AdminSteSeoSitemap') . '&action=generate',
            'ste_generate_shop_url' => $this->context->link->getAdminLink('AdminSteSeoSitemap') . '&action=generate_shop',
            'ste_update_robots_url' => $this->context->link->getAdminLink('AdminSteSeoSitemap') . '&action=update_robots',
            'ste_clean_robots_url' => $this->context->link->getAdminLink('AdminSteSeoSitemap') . '&action=clean_robots',
            'ste_submit_settings_url' => $this->context->link->getAdminLink('AdminSteSeoSitemap')
        ]);

        $this->setTemplate('dashboard.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitSteSettings')) {
            Configuration::updateValue('STE_SKIP_DEFAULT', (int)Tools::getValue('STE_SKIP_DEFAULT'));
            Configuration::updateValue('STE_MANAGE_ROBOTS', (int)Tools::getValue('STE_MANAGE_ROBOTS'));
            $this->confirmations[] = $this->l('Settings updated');
        }

        if (Tools::isSubmit('action')) {
            $action = Tools::getValue('action');
            $idShop = (int)Tools::getValue('id_shop');

            if ($action == 'generate') {
                $this->generateSitemaps();
            } elseif ($action == 'generate_shop' && $idShop) {
                $this->generateSingleShop($idShop);
            } elseif ($action == 'update_robots') {
                $this->updateRobots();
            } elseif ($action == 'clean_robots') {
                $this->cleanRobots();
            }
        }
        
        if (Tools::isSubmit('submitSteRobotsContent')) {
            $idShop = (int)Tools::getValue('id_shop_robots');
            $content = Tools::getValue('robots_content');
            
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ste_robots (id_shop, content) 
                    VALUES ('.(int)$idShop.', "'.pSQL($content).'") 
                    ON DUPLICATE KEY UPDATE content = "'.pSQL($content).'"';
            
            if (Db::getInstance()->execute($sql)) {
                $this->confirmations[] = "Robots.txt updated for Shop ID $idShop";
            } else {
                $this->errors[] = "Failed to save robots.txt";
            }
        }
    }

    private function generateSingleShop($idShop)
    {
        $generator = new SteSitemapGenerator();
        $shop = new Shop($idShop);
        $res = $generator->generateForShop($shop->id, _PS_ROOT_DIR_);
        
        if ($res['success']) {
            $this->confirmations[] = "Shop " . $shop->name . ": Success ({$res['count']} URLs)";
        } else {
            $this->errors[] = "Shop " . $shop->name . ": " . $res['message'];
        }
    }

    private function generateSitemaps()
    {
        $generator = new SteSitemapGenerator();
        $shops = Shop::getShops();
        $basePath = _PS_ROOT_DIR_;
        $results = [];

        foreach ($shops as $shop) {
             // Logic to skip Default Shop
             if (Configuration::get('STE_SKIP_DEFAULT') && $shop['id_shop'] == Configuration::get('PS_SHOP_DEFAULT')) {
                 $results[] = "Skipped Default Shop " . $shop['name'];
                 continue;
             }
             
            $res = $generator->generateForShop($shop['id_shop'], $basePath);
            $results[] = "Shop " . $shop['name'] . ": " . ($res['success'] ? "Success ({$res['count']} URLs)" : "Failed");
        }

        $this->confirmations[] = implode('<br>', $results);
    }
    
    private function cleanRobots()
    {
        $manager = new SteRobotsManager();
        if ($manager->cleanLegacySitemaps(_PS_ROOT_DIR_ . '/robots.txt')) {
             $this->confirmations[] = "Legacy sitemap entries removed from robots.txt";
        } else {
             $this->errors[] = "Could not clean robots.txt";
        }
    }

    private function updateRobots()
    {
        $manager = new SteRobotsManager();
        $shops = Shop::getShops();
        $links = [];
        
        foreach ($shops as $shop) {
             $filename = 'sitemap-' . $shop['id_shop'] . '.xml';
             // Construct absolute URL for sitemap
             $shopUrl = $this->getShopUrl($shop['id_shop']);
             // Ensure trailing slash
             $shopUrl = rtrim($shopUrl, '/') . '/';
             $links[] = $shopUrl . $filename;
        }

        if ($manager->updateRobotsTxt(_PS_ROOT_DIR_ . '/robots.txt', $links)) {
            $this->confirmations[] = "Robots.txt updated successfully.";
        } else {
            $this->errors[] = "Failed to update robots.txt via file write.";
        }
    }

    private function getShopUrl($idShop)
    {
        $shop = new Shop($idShop);
        return $shop->getBaseURL(true);
    }
    
    // Override createTemplate to look in module folder
    public function createTemplate($tpl_name) {
        if (file_exists(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/' . $tpl_name)) {
            return $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/' . $tpl_name, $this->context->smarty);
        }
        return parent::createTemplate($tpl_name);
    }
}
