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

class NewsletterProSubscriptionModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public $ssl = true;

    private $translate;

    public function __construct()
    {
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = true;
        }

        parent::__construct();

        $this->translate = new NewsletterProTranslate(pathinfo(__FILE__, PATHINFO_FILENAME));

        if (!(bool) pqnp_config_get('SUBSCRIPTION_CONTROLLER_ENABLED', false)) {
            return $this->displayPageNotFound();
        }

        $template = new NewsletterProSubscriptionTpl((int) pqnp_config_get('SUBSCRIPTION_CONTROLLER_TEMPLATE_ID', 0));
        if (!Validate::isLoadedObject($template)) {
            return $this->displayPageNotFound();
        }
    }

    private function displayPageNotFound()
    {
        Controller::getController('PageNotFoundController')->run();
        exit;
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['body_classes'] = array_merge($page['body_classes'], [
            'page-customer-account' => true,
        ]);

        return $page;
    }

    public function getLink($params = [])
    {
        $params = array_merge($params, []);

        return urldecode($this->context->link->getModuleLink($this->module->name, 'subscription', $params));
    }

    public function initContent()
    {
        parent::initContent();

        if (NewsletterProTools::is17()) {
            $this->setTemplate('module:newsletterpro/views/templates/front/1.7/subscription.tpl');
        } else {
            $this->setTemplate('subscription.tpl');
        }
    }

    public function postProcess()
    {
    }
}
