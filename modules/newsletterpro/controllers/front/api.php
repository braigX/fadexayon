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

class NewsletterProApiModuleFrontController extends ModuleFrontController
{
    /**
     * @var false
     */
    public $auth = false;

    /**
     * @var true
     */
    public $ssl = false;

    /**
     * @var NewsletterProResponse
     */
    protected $response;

    /**
     * @var NewsletterProRequest
     */
    protected $request;

    public function __construct()
    {
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = true;
        }

        parent::__construct();

        $this->response = new NewsletterProResponse();
        $this->request = new NewsletterProRequest();
    }

    public function initContent()
    {
        switch ($this->request->get('action', '')) {
            case 'bounce':
                $api = new NewsletterProApiBounce(true);
                exit($api->call());
            case 'css':
                $api = new NewsletterProApiCss(false, 'text/css');
                exit($api->call());
            case 'mailchimp':
                $api = new NewsletterProApiMailchimp(true);
                exit($api->call());
            case 'openedEmail':
                $api = new NewsletterProApiOpenedEmail(false);
                exit($api->call());
            case 'syncChimp':
                $api = new NewsletterProApiSyncChimp(true, 'text/html');
                exit($api->call());
            case 'syncNewsletterBlock':
                $api = new NewsletterProApiSyncNewsletterBlock(true, 'text/html');
                exit($api->call());
            case 'task':
                $api = new NewsletterProApiTask(true, 'text/html');
                exit($api->call());
        }

        exit('Invalid action');
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function postProcess()
    {
        parent::postProcess();
    }
}
