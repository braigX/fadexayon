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

class NewsletterProUnsubscribeModuleFrontController extends ModuleFrontController
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
            $this->setTemplate('module:newsletterpro/views/templates/front/1.7/unsubscribe.tpl');
        } else {
            $this->setTemplate('unsubscribe.tpl');
        }
    }

    public function postProcess()
    {
        try {
            if (Tools::isSubmit('email')) {
                $email = trim(Tools::getValue('email'));

                if (Validate::isEmail($email)) {
                    $token_ok = false;

                    if (Tools::isSubmit('mc_token')) {
                        $token_ok = NewsletterProMailChimpToken::validateToken('mc_token');
                    }

                    if (!$token_ok) {
                        $token_ok = Tools::isSubmit('u_token') && Tools::getValue('u_token') === Tools::encrypt($email);
                    }

                    if (!$token_ok) {
                        $this->context->smarty->assign([
                            'token_not_valid' => true,
                        ]);
                    } else {
                        $result = NewsletterProSubscriptionManager::newInstance()->unsubscribe($email, (int) $this->context->shop->id, true);

                        if (in_array(true, $result)) {
                            $this->id_newsletter = $this->getHistoryIdByToken(Tools::getValue('token'));

                            if ($this->id_newsletter) {
                                if (self::isForwarder($result)) {
                                    // deleted the forwarders is he unsubscribe
                                    Db::getInstance()->delete('newsletter_pro_forward', '`from` = "'.pSQL($email).'"');
                                    if ($this->updateFwdUnsubscribed()) {
                                        $fwd_unsubscribed = new NewsletterProFwdUnsubscribed();
                                        $fwd_unsubscribed->id_newsletter_pro_tpl_history = (int) $this->id_newsletter;
                                        $fwd_unsubscribed->email = $email;
                                        $fwd_unsubscribed->add();
                                    }
                                } else {
                                    if ($this->updateUnsubscribed()) {
                                        $unsubscribed = new NewsletterProUnsubscribed();
                                        $unsubscribed->id_newsletter_pro_tpl_history = (int) $this->id_newsletter;
                                        $unsubscribed->email = $email;
                                        $unsubscribed->add();
                                    }
                                }
                            }

                            $this->context->smarty->assign([
                                'unsubscribe' => true,
                            ]);
                        } else {
                            $this->context->smarty->assign([
                                'email_not_found' => true,
                            ]);
                        }
                    }
                } else {
                    $this->context->smarty->assign([
                        'email_not_valid' => true,
                    ]);
                }
            } else {
                Tools::redirect('index.php');
            }

            if ('false' == Tools::getValue('msg') || '0' == Tools::getValue('msg')) {
                Tools::redirect('index.php');
            }
        } catch (Exception $e) {
            NewsletterProLog::writeStrip($e->__toString(), NewsletterProLog::ERROR_FILE);

            $this->context->smarty->assign([
                'pqnp_errors' => $this->translate->l('Oops, an error has occurred.'),
            ]);
        }
    }

    public static function isForwarder($result)
    {
        if (isset($result['newsletter_pro_forward']) && true == $result['newsletter_pro_forward']) {
            return true;
        }

        return false;
    }

    public function getHistoryIdByToken($token)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL($token).'"');
    }

    public function updateUnsubscribed()
    {
        if (!isset($this->id_newsletter)) {
            return false;
        }

        $sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_tpl_history`
				SET `unsubscribed` = unsubscribed + 1 
				WHERE `id_newsletter_pro_tpl_history` = '.(int) $this->id_newsletter.';';

        return Db::getInstance()->execute($sql);
    }

    public function updateFwdUnsubscribed()
    {
        if (!isset($this->id_newsletter)) {
            return false;
        }

        $sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_tpl_history`
				SET `fwd_unsubscribed` = fwd_unsubscribed + 1 
				WHERE `id_newsletter_pro_tpl_history` = '.(int) $this->id_newsletter.';';

        return Db::getInstance()->execute($sql);
    }
}
