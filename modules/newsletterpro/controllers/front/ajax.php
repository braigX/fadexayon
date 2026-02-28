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

class NewsletterProAjaxModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public $ssl = false;

    /**
     * @var NewsletterPro
     */
    public $module;

    public $response;

    private $request;

    public function __construct()
    {
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = true;
        }

        parent::__construct();

        $this->response = new NewsletterProResponse();
        $this->request = new NewsletterProRequest();
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function postProcess()
    {
        parent::postProcess();

        if ($this->request->has('pqnpAction')) {
            $action = $this->request->get('pqnpAction');

            switch ($action) {
                case 'popup':
                    return NewsletterProPopupAction::newInstance()->call($this->request->get('target'));
            }
        }

        // content from scripts/ajax_newsletterpro_front.php
        header('Access-Control-Allow-Origin: *');

        if (_PS_MAGIC_QUOTES_GPC_ && class_exists('NewsletterPro')) {
            $_POST = NewsletterPro::strip($_POST);
            $_GET = NewsletterPro::strip($_GET);
        }

        $this->module->ajaxProcess();

        exit('Invalid Action');
    }
}
