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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/DbQueriesFaq.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/CacheHelper.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/MessagesFaq.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/HelperFaq.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/FrontHelperFaq.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/DuplicateHelperFaq.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/ConfigsFaq.php';

class FaqOp extends Module
{
    public $rep;

    public $mes;

    public $helper;

    public $front_helper;

    public $install_helper;

    public $cache_helper;

    public $dup_helper;

    public function __construct()
    {
        $this->name = 'faqop';
        $this->tab = 'front_office_features';
        $this->version = '4.4.17';
        $this->author = 'Opossum Dev';
        $this->need_instance = 0;
        $this->secret_key = Tools::encrypt($this->name);
        $this->context = Context::getContext();
        $this->module_key = 'c0eb0be62567e0340d2a1a100824ce8c';

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SEO FAQ Blocks on Any Page (Products, Categories, etc.)');
        $this->description = $this->l('Add FAQ lists with Schema Markup to any pages (product, category, etc.) 
            to make them Google SEO friendly. Set rules of display by page, currency, language, customer group. 
            Shortcodes and hooks, unlimited, custom and standard. And a separate FAQ page.');

        $this->ps_versions_compliancy = ['min' => '1.7.1', 'max' => _PS_VERSION_];

        $this->rep = new DbQueriesFaq($this);
        $this->mes = new MessagesFaq($this);
        $this->helper = new HelperFaq($this);
        $this->front_helper = new FrontHelperFaq($this);
        $this->install_helper = null;
        $this->cache_helper = new CacheHelper($this);
        $this->dup_helper = new DuplicateHelperFaq($this);

        //        $this->logger = new FileLogger(0);
        //        $this->logger->setFilename(_PS_ROOT_DIR_ . '/var/logs/faqop_debug.log');
    }

    public function install()
    {
        $res = true;
        $res &= Configuration::updateGlobalValue('OP_FAQ_PAGE_ACTIVE', 1);
        $res &= parent::install();
        $res &= $this->registerHook('displayBackOfficeHeader');
        $res &= $this->registerHook('displayHeader');
        $res &= $this->registerHook('filterCmsContent');
        $res &= $this->registerHook('filterProductContent');
        $res &= $this->registerHook('filterCategoryContent');
        $res &= $this->registerHook('actionShopDataDuplication');
        $res &= $this->registerHook(ConfigsFaq::PAGE_HOOK);
        $res &= $this->rep->createTables();

        $ih = $this->helper->getInstallHelper($this->install_helper);
        $res &= $ih->installSamples();

        return $res;
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->unregisterHook('displayBackOfficeHeader')
            && $this->unregisterHook('displayHeader')
            && $this->unregisterHook('filterCmsContent')
            && $this->unregisterHook('filterProductContent')
            && $this->unregisterHook('filterCategoryContent')
            && $this->unregisterHook('actionShopDataDuplication')
            && $this->unregisterHook(ConfigsFaq::PAGE_HOOK)
            && $this->rep->deleteCustomHooks()
            && $this->rep->deleteTables()
            && Configuration::deleteByName('OP_FAQ_PAGE_ACTIVE')
        ;
    }

    public function enable($force_all = false)
    {
        $ih = $this->helper->getInstallHelper($this->install_helper);
        $res = parent::enable($force_all);
        $res &= $ih->installTabs();
        if (Configuration::getGlobalValue('OP_FAQ_PAGE_ACTIVE')) {
            $res &= $ih->createMetaPage();
        }

        return (bool) $res;
    }

    public function disable($force_all = false)
    {
        $id_profile = (int) $this->context->employee->id_profile;
        if (ConfigsFaq::DEMO_MODE && $id_profile !== _PS_ADMIN_PROFILE_) {
            return false;
        }

        $ih = $this->helper->getInstallHelper($this->install_helper);
        $res = true;
        $res &= parent::disable($force_all);
        $res &= $ih->uninstallTabs();
        $res &= $ih->deleteMetaPage();

        return (bool) $res;
    }

    public function hookActionShopDataDuplication($params)
    {
        $dup_helper = new DuplicateHelperFaq($this);
        $dup_helper->shopHookDataDuplication($params);
    }

    public function getContent()
    {
        try {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminFaqop'));
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }
    }

    public function hookdisplayBackOfficeHeader()
    {
        if (version_compare(_PS_VERSION_, '8.1.0', '>=')) {
            $this->context->controller->addCSS($this->_path . 'views/css/global-8.css');
        } else {
            $this->context->controller->addCSS($this->_path . 'views/css/global.css');
        }

        if (Tools::getValue('configure') == $this->name
            || $this->helper->startsWith(Tools::getValue('controller'), 'AdminFaqop')) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS('https://kit.fontawesome.com/c126316764.js');
            $this->context->controller->addJS($this->_path . 'views/js/functions.js');
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addJS($this->_path . 'views/js/grid.js');

            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            if (version_compare(_PS_VERSION_, '8.1.0', '>=')) {
                $this->context->controller->addCSS($this->_path . 'views/css/back-8.css');
            }

            if ($this->helper->startsWith(Tools::getValue('controller'), 'AdminFaqopHook')) {
                $this->context->controller->addJS($this->_path . 'views/js/hook_settings.js');
            }
        }
    }

    public function hookdisplayHeader($params)
    {
        $this->context->controller->registerStylesheet(
            'modules-faqop',
            'modules/' . $this->name . '/views/css/op-faq-front.css',
            ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->addJS($this->_path . 'views/js/op-faq-accordion.js');
    }

    public function displayNav($active_url)
    {
        try {
            $this->context->smarty->assign([
                'page_url' => $this->context->link->getAdminLink('AdminFaqop') .
                    $this->helper->createAnticacheString(),
                'list_url' => $this->context->link->getAdminLink('AdminFaqopListsList') .
                    $this->helper->createAnticacheString(),
                'items_url' => $this->context->link->getAdminLink('AdminFaqopItemsList') .
                    $this->helper->createAnticacheString(),
                'hook_url' => $this->context->link->getAdminLink('AdminFaqopCustomHook') .
                    $this->helper->createAnticacheString(),
                'help_url' => $this->context->link->getAdminLink('AdminFaqopHelp') .
                    $this->helper->createAnticacheString(),
                'active_url' => $active_url,
            ]);
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        try {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/nav.tpl');
        } catch (SmartyException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function displayNavTabs($active_url, $items_url, $general_url, $position_url, $styles_url)
    {
        try {
            $this->context->smarty->assign([
                'items_url' => $items_url,
                'general_url' => $general_url,
                'position_url' => $position_url,
                'styles_url' => $styles_url,
                'active_url' => $active_url,
            ]);
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        try {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/nav_tabs.tpl');
        } catch (SmartyException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function displayNavTabsPage($active_url, $items_url, $general_url, $styles_url)
    {
        try {
            $this->context->smarty->assign([
                'items_url' => $items_url,
                'general_url' => $general_url,
                'styles_url' => $styles_url,
                'active_url' => $active_url,
            ]);
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        try {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name .
                '/views/templates/hook/nav_tabs_page.tpl');
        } catch (SmartyException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function displayItemNavTabs($active_url, $general_url, $styles_url, $bindings_url)
    {
        try {
            $this->context->smarty->assign([
                'general_url' => $general_url,
                'styles_url' => $styles_url,
                'bindings_url' => $bindings_url,
                'active_url' => $active_url,
            ]);
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        try {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name .
                '/views/templates/hook/nav_tabs_item.tpl');
        } catch (SmartyException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function displayMessage()
    {
        return $this->display(__FILE__, 'message.tpl');
    }

    public function displayBlocks()
    {
        return $this->display(__FILE__, 'table_blocks.tpl');
    }

    public function displayItemListInsideBlock()
    {
        return $this->display(__FILE__, 'item_list_inside_block.tpl');
    }

    public function displayAddReady()
    {
        return $this->display(__FILE__, 'item_list_add_ready.tpl');
    }

    public function displayMain()
    {
        return $this->display(__FILE__, 'main.tpl');
    }

    // all items list
    public function displayItems()
    {
        return $this->display(__FILE__, 'table_items.tpl');
    }

    public function displayShortcodeInForm($shortcode)
    {
        $this->context->smarty->assign([
            'shortcode' => $shortcode,
        ]);

        try {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name .
                '/views/templates/hook/shortcode_form.tpl');
        } catch (SmartyException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function displayHooksTable()
    {
        return $this->display(__FILE__, 'table_hooks.tpl');
    }

    public function displayHelp()
    {
        return $this->display(__FILE__, 'help.tpl');
    }

    public function hookfilterCmsContent($params)
    {
        if (Tools::getValue('id_cms') > 0) {
            $cmshtml = $params['object']['content'];
            $params['object']['content'] = $this->front_helper->getContentByShortCode($cmshtml);

            return [
                'object' => $params['object'],
            ];
        }

        return false;
    }

    public function hookfilterProductContent($params)
    {
        $prhtml = $params['object']['description'];
        $params['object']['description'] = $this->front_helper->getContentByShortCode($prhtml);
        $prhtml1 = $params['object']['description_short'];
        $params['object']['description_short'] = $this->front_helper->getContentByShortCode($prhtml1);

        return [
            'object' => $params['object'],
        ];
    }

    public function hookfilterCategoryContent($params)
    {
        $cchtml = $params['object']['description'];
        $params['object']['description'] = $this->front_helper->getContentByShortCode($cchtml);

        return [
            'object' => $params['object'],
        ];
    }

    public function fetchBlockFront($block)
    {
        $block = $this->front_helper->processBlockForFront($block);

        $this->context->smarty->assign([
            'block' => $block,
        ]);

        try {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name .
                '/views/templates/hook/block_front.tpl');
        } catch (SmartyException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function __call($function, $args)
    {
        $html = '';
        $hookName = str_replace('hook', '', $function);

        $data = $this->rep->getBlocksInHook($hookName);
        if (sizeof($data) > 0) {
            foreach ($data as $block) {
                $html .= $this->front_helper->determineHookDisplayByCustomerGroup($block);
            }
        }

        return $html;
    }

    // here for correct translation
    public function getSaveCancelButtons($back)
    {
        $result = [
            'buttons' => [
                'cancelBlock' => [
                    'title' => $this->l('Cancel'),
                    'href' => $back,
                    'icon' => 'process-icon-cancel',
                ],
                'saveClose' => [
                    'title' => $this->l('Save & Stay'),
                    'icon' => 'process-icon-save',
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitStay',
                    'type' => 'submit',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return $result;
    }

    // here for correct translation
    public function getMultilangMessage()
    {
        return $this->l('Since multiple languages are activated on your shop, please mind to translate
             text for each one of them');
    }

    // here for correct translation
    public function getMultishopMessage()
    {
        return $this->l('You cannot manage faq items or lists from an All Shops 
            or a Group Shop context, select directly the shop you want to edit');
    }

    // here for correct translation
    public function getShopContextErrorMessageOne($shop_contextualized_name)
    {
        return sprintf($this->l(
            'You can only edit it from the shop(s) context: %s'
        ), $shop_contextualized_name);
    }

    // here for correct translation
    public function getShopContextErrorMessageTwo()
    {
        return $this->l('You cannot add items and lists from an All Shops or a Group Shop context');
    }

    // here for correct translation
    public function getCurrentShopInfoMsg()
    {
        return sprintf($this->l(
            'All created lists will belong to shop: %s. You can copy them to another shop in bulk actions'
        ), Context::getContext()->shop->name);
    }

    // here for correct translation
    public function getWarningMultishopAboutItem()
    {
        return $this->l('FAQ items are available for all stores. 
        But you should choose a shop to bind them to lists');
    }

    // here for correct translation
    public function getCurrentShopInfoMsgBlock($back)
    {
        return sprintf($this->l(
            'This list belongs to shop: %s. You can copy it to another shop in bulk actions'
        ), Context::getContext()->shop->name) .
        '&nbsp;<a href="' . $back . '">' . $this->l('here') . '</a>';
    }

    // here for correct translation
    public function getCurrentShopInfoMsgItemBlock()
    {
        return sprintf($this->l(
            'Here you bind items to page and lists for shop: %s.'), Context::getContext()->shop->name);
    }

    // here for correct translation
    public function getInfoMultishopAboutItem()
    {
        return $this->l('FAQ items are available for all stores.');
    }

    // here for correct translation
    public function getCurrentShopInfoMsgPage()
    {
        return sprintf($this->l(
            "This page's settings belong to shop: %s"
        ), Context::getContext()->shop->name);
    }

    // here for correct translation
    public function getCreateListInfoMessage()
    {
        return $this->l('First create list with basic settings, then you will be able to add FAQ items');
    }

    // here for correct translation
    public function getItemInfoMessage()
    {
        return $this->l('You are editing a separate FAQ item now. It is a part of FAQ list');
    }

    // added here to avoid duplication
    public function copyBulkBlocksShop()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->helper->throwError($this->l('Error: could not duplicate blocks in demo mode'));
        }

        $ids = json_decode(Tools::getValue('ids'), true);
        $oldShop = Tools::getValue('oldShop');
        $newShop = Tools::getValue('newShop');
        $result = true;
        foreach ($ids as $item) {
            $result &= $this->dup_helper->duplicateOneBlockPre(
                $item['id'],
                $item['type'],
                $oldShop,
                $newShop
            );
        }

        if ($result) {
            exit(1);
        } else {
            $this->helper->throwError($this->l('Error: could not duplicate blocks'));
        }
    }
}
