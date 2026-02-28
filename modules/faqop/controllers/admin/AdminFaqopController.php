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

class AdminFaqopController extends ModuleAdminController
{
    protected $page_id;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->page_header_toolbar_title = $this->l('FAQ page');
        $this->page_id = $this->module->rep->getPageIdByShop($this->context->shop->id);
    }

    public function initContent()
    {
        if (!$this->module->helper->isCurrentShopChosen()) {
            $this->content .= $this->module->mes->getWarningMultishopHtml();
        } else {
            $this->content .= $this->module->displayNav('page');
            $this->content .= $this->module->mes->getCurrentShopInfoMsgPage();
            $this->content .= $this->renderInstructions();
        }

        parent::initContent();
    }

    public function renderInstructions()
    {
        if (version_compare(_PS_VERSION_, '1.7.6.0', '>=')) {
            $layout_link = _PS_BASE_URL_ . $this->context->link->getAdminLink(
                'AdminThemes',
                true,
                ['route' => 'admin_theme_customize_layouts']
            );
        } else {
            $layout_link = $this->context->link->getAdminLink(
                'AdminThemes',
                true,
                [],
                ['display' => 'configureLayouts']
            );
        }

        if (version_compare(_PS_VERSION_, '1.7.5.0', '>=')) {
            $seo_link = _PS_BASE_URL_ . $this->context->link->getAdminLink(
                'AdminMeta',
                true,
                ['route' => 'admin_metas_edit',
                    'metaId' => (int) $this->module->rep->getMetaPageId()]
            );
        } else {
            $seo_link = $this->context->link->getAdminLink(
                'AdminMeta',
                true,
                [],
                ['updatemeta' => '0',
                    'id_meta' => (int) $this->module->rep->getMetaPageId()]
            );
        }

        try {
            $newPageUrl = $this->context->link->getAdminLink('AdminFaqopPage') .
                '&create=1' .
                '&op_type=page' .
                $this->module->helper->createAnticacheString();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        $this->context->smarty->assign(
            [
                'multistore_active' => $this->module->helper->getIsMultistoreActive(),
                'edit_page_href' => $this->module->helper->getAddItemsToPageUrl($this->page_id),
                'create_page_href' => $newPageUrl,
                'page_status' => Configuration::getGlobalValue('OP_FAQ_PAGE_ACTIVE'),
                'link_page' => $this->context->link->getModuleLink('faqop', 'page'),
                'link_seo' => $seo_link,
                'link_layout' => $layout_link,
                'shops_list' => $this->module->helper->getShopsWithoutCurrent(),
                'shops_list_with_settings' => $this->module->helper->getShopsListWithSettings(),
                'old_shop' => Context::getContext()->shop->id,
                'includeTpl' => _PS_MODULE_DIR_ . 'faqop/views/templates/hook/copy_shop.tpl',
                'page_id' => $this->page_id,
            ]
        );

        return $this->module->displayMain();
    }

    public function ajaxProcessUpdatePageStatus()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('This action is prohibited in demo mode'));
        }

        require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/PageStatusHelper.php';
        $sh = new PageStatusHelper($this->module);
        if ($sh->togglePageStatus(Tools::getValue('pageStatus'))) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not update page status'));
        }
    }

    public function ajaxProcessCopyBulkBlocksShop()
    {
        $this->module->copyBulkBlocksShop();
    }
}
