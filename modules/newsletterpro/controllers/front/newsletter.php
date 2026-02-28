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

class NewsletterProNewsletterModuleFrontController extends ModuleFrontController
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

    public function setMedia()
    {
        parent::setMedia();
    }

    public function initContent()
    {
        $module = NewsletterProTools::module();
        $context = Context::getContext();

        if (!$this->request->has('token_tpl')) {
            exit($this->l('Invalid template token'));
        }

        if (!$this->request->has('email')) {
            exit($this->l('Invalid email address'));
        }

        $idTplHistory = (int) $this->getTplHistoryIdByToken($this->request->get('token_tpl'));
        $email = $this->request->get('email');

        if (0 == (int) $idTplHistory) {
            exit($this->l('Invalid template'));
        }

        $template = new NewsletterProTemplateHistory((int) $idTplHistory, $email);

        $idLang = (int) $context->language->id;

        $template = $template->load((int) $idLang);
        $message = $template->message(null, false, (int) $idLang);

        @ob_end_clean();

        $context->smarty->assign([
            'template' => $message['body'],
            'page_title' => $message['title'],
            'jquery_url' => $module->url_location.'views/js/jquery-1.7.2.min.js',
            'jquery_url_exists' => file_exists($module->dir_location.'views/js/jquery-1.7.2.min.js'),
            'jquery_no_conflict' => (int) $this->request->has('jQueryNoConflict'),
        ]);

        exit($context->smarty->display(pqnp_template_path($module->dir_location.'views/templates/front/newsletter.tpl')));
    }

    private function getTplHistoryIdByToken($token)
    {
        return (int) Db::getInstance()->getValue('
            SELECT `id_newsletter_pro_tpl_history`
            FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history`
            WHERE `token` = "'.pSQL($token).'"
        ');
    }
}
