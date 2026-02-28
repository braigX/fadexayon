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

class NewsletterProSubscribeModuleFrontController extends ModuleFrontController
{
    public $id_newsletter;

    private $translate;

    public function __construct()
    {
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = true;
        }

        parent::__construct();

        $this->translate = new NewsletterProTranslate(pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function initContent()
    {
        parent::initContent();
        if (NewsletterProTools::is17()) {
            $this->setTemplate('module:newsletterpro/views/templates/front/1.7/subscribe.tpl');
        } else {
            $this->setTemplate('subscribe.tpl');
        }
    }

    public function postProcess()
    {
        try {
            if (Tools::isSubmit('email') && ($email = Tools::getValue('email'))) {
                $token = Tools::getValue('token');
                $token_ok = false;

                if (Tools::isSubmit('mc_token')) {
                    $token_ok = NewsletterProMailChimpToken::validateToken('mc_token');
                }

                if (!$token_ok) {
                    $token_ok = $token == Tools::encrypt($email);
                }

                if ($token_ok) {
                    $errors = $this->module->subscribe($email);
                    $this->errors = array_merge($this->errors, $errors);
                } else {
                    $this->errors[] = $this->translate->l('Invalid token for subscription.');
                }
            } else {
                $this->errors[] = sprintf($this->translate->l('The email %s is not valid.'), (string) Tools::getValue('email'));
            }

            if (empty($this->errors)) {
                $this->context->smarty->assign([
                    'success_message' => $this->translate->l('You have successfully subscribed at our newsletter.'),
                ]);
            }
        } catch (Exception $e) {
            $this->errors[] = $this->translate->l('There is an error, please report this error to the website developer.');
            if (_PS_MODE_DEV_) {
                $this->errors[] = $e->getMessage();
            }

            NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
        }
    }

    public function getHistoryIdByToken($token)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL($token).'"');
    }
}
