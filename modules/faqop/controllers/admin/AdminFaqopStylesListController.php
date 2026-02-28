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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBasicStylesController.php';

class AdminFaqopStylesListController extends AdminFaqopBasicStylesController
{
    public function initContent()
    {
        $this->content .= $this->module->displayNav('list');
        parent::initContent();
    }

    public function renderBlockNav($active_url = 'styles')
    {
        return $this->module->helper->renderBlockNavTabs($active_url, $this->id_list);
    }
}
