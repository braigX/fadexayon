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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopAddItemsController.php';

class AdminFaqopAddItemsPageController extends AdminFaqopAddItemsController
{
    public function initContent()
    {
        $this->module->helper->saveToCookieItemsParent('page');
        $this->content .= $this->module->displayNav('page');
        parent::initContent();
    }

    public function renderBlockNav($active_url = 'items')
    {
        return $this->module->helper->renderPageNavTabs($active_url, $this->id_list);
    }
}
