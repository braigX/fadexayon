<?php

/**
 * Since 2013 Ovidiu Cimpean.
 *
 * Ovidiu Cimpean - Newsletter Pro © All rights reserved.
 *
 * DISCLAIMER
 *
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at addons4prestashop@gmail.com.
 *
 * @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
 * @copyright Since 2013 Ovidiu Cimpean
 * @license   Do not edit, modify or copy this file
 *
 * @version   Release: 4
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class NewsletterProNewsletterSubscriptionModuleFrontController extends ModuleFrontController
{
    /**
     * @var NewsletterPro
     */
    public $module;

    private $translate;

    public function __construct()
    {
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = true;
        }

        parent::__construct();

        $this->translate = new NewsletterProTranslate(pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['body_classes'] = array_merge($page['body_classes'], [
            'page-customer-account' => true,
        ]);

        return $page;
    }

    public function initContent()
    {
        $this->display_column_left = false;
        parent::initContent();

        $this->module = Module::getInstanceByName('newsletterpro');

        if (!Validate::isLoadedObject($this->module)) {
            Tools::redirect('index.php');
        }

        if (!$this->isFeatureActivated()) {
            Tools::redirect('index.php');
        }

        $this->context->smarty->assign([]);

        return $this->settemplate('module:newsletterpro/views/templates/front/newslettersubscription.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function isFeatureActivated()
    {
        return (bool) pqnp_config('ENABLE_NEWSLETTER_SUBSCRIPTION_PAGE');
    }

    public function postProcess()
    {
    }
}
