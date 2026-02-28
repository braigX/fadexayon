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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/ConfigsFaq.php';
class FaqOpPageModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(
            [
                'pageHook' => ConfigsFaq::PAGE_HOOK,
            ]
        );

        $this->setTemplate('module:faqop/views/templates/front/page.tpl');
    }

    public function getCanonicalUrl()
    {
        return $this->context->link->getModuleLink('faqop', 'page');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->module->front_helper->getTitleText(),
            'url' => $this->context->link->getPageLink(ConfigsFaq::PAGE, true),
        ];

        return $breadcrumb;
    }
}
