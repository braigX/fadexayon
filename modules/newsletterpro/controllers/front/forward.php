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

require_once dirname(__FILE__).'/../../classes/NewsletterProForward.php';

class NewsletterProForwardModuleFrontController extends ModuleFrontController
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

    /**
     * DEPRACTED.
     */
    public function npl($string)
    {
        return Translate::getModuleTranslation($this->module, $string, Tools::getValue('controller'));
    }

    public function initContent()
    {
        parent::initContent();

        if (NewsletterProTools::is17()) {
            $this->setTemplate('module:newsletterpro/views/templates/front/1.7/forward.tpl');
        } else {
            $this->setTemplate('forward.tpl');
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $css_path = $this->module->getCssPath();
        $this->addCss($css_path.'forward_front.css');
    }

    public function getEmailsFromPost()
    {
        $emails = [];
        $grep_keys = preg_grep('/^email_\d+$/', array_keys($_POST));

        foreach ($grep_keys as $key) {
            $email = trim(Tools::getValue($key));
            if (Validate::isEmail($email) && !in_array($email, $emails)) {
                $emails[] = $email;
            } else {
                $this->errors = sprintf($this->translate->l('The email %s is invalid.'), $email);
            }
        }

        return $emails;
    }

    public function getLink($params = [])
    {
        $email = (Tools::isSubmit('email') ? Tools::getValue('email') : '');
        $token = (Tools::isSubmit('token') ? Tools::getValue('token') : '');

        $params = array_merge($params, [
            'email' => $email,
            'token' => $token,
        ]);

        return urldecode($this->context->link->getModuleLink($this->module->name, 'forward', $params));

        // $email = (Tools::isSubmit('email') ? '&email='.Tools::getValue('email'):'');
        // $token = (Tools::isSubmit('token') ? '&token='.Tools::getValue('token'):'');
        // return 'index.php?fc=module&module=newsletterpro&controller=forward'.$email.$token.(!empty($params) ? '&'.http_build_query($params) : '');
    }

    public function postProcess()
    {
        try {
            if (pqnp_config('FWD_FEATURE_ACTIVE')) {
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
                        $this->context->smarty->assign([
                            'dispalyForm' => true,
                            'email' => $email,
                            'emails_js' => NewsletterProTools::jsonEncode(NewsletterProForward::getEmailsToByEmailFrom($email)),
                            'fwd_limit' => NewsletterProForward::FOREWORD_LIMIT - count(NewsletterProForward::getEmailsToByEmailFrom($email)),
                            'self_link' => $this->getLink(),
                            'ajax_link' => $this->getLink(),
                            'jsData' => pqnp_addcslashes(NewsletterProTools::jsonEncode([
                                'token' => Tools::getValue('token'),
                            ])),
                        ]);

                        // this is ajax request
                        if ('submitForward' == Tools::getValue('action')) {
                            $response = ['status' => false, 'errors' => [], 'emails' => []];

                            $to_email = Tools::getValue('to_email');

                            if (Validate::isEmail($to_email)) {
                                $forward = new NewsletterProForward();
                                $forward->from = $email;
                                $forward->to = $to_email;
                                if ($info = $this->getUserTableByEmail($forward->to)) {
                                    $subscribed = false;

                                    if (NewsletterProTools::tableExists($info['table'])) {
                                        $subscribed = (int) Db::getInstance()->getValue('SELECT `'.$info['newsletter'].'` FROM `'._DB_PREFIX_.$info['table'].'` WHERE `'.$info['email'].'` = "'.pSQL($forward->to).'"');
                                    }

                                    if ($subscribed) {
                                        $this->errors[] = sprintf($this->translate->l('The email %s is already subscribed at our newsletter.'), $forward->to);
                                    } else {
                                        $output = sprintf($this->translate->l('The email %s is already registered in our database, but is not subscribed at our newsletter. You can send him a subcription request by clicking '), $forward->to);
                                        $output .= '<a class="subscription" href="javascript:{}" style="color: blue;" onclick="NewsletterPro.modules.frontForward.requestFriendSubscription($(this), \''.$token.'\', \''.addcslashes($email, "'").'\', \''.addcslashes($forward->to, "'").'\');">'.$this->translate->l('here').'</a>';
                                        $output .= '.';
                                        $this->errors[] = $output;
                                    }
                                } else {
                                    if ($forward->from != $forward->to) {
                                        if (!$forward->add()) {
                                            $this->errors = array_merge($forward->getErrors(), $this->errors);
                                        }
                                    } else {
                                        $this->errors[] = $this->translate->l('You cannot add your own email address.');
                                    }
                                }
                            } else {
                                $this->errors[] = sprintf($this->translate->l('The email address %s is invalid.'), $to_email);
                            }

                            if (empty($this->errors)) {
                                $response['status'] = true;
                            } else {
                                $response['errors'] = array_merge($response['errors'], $this->errors);
                            }

                            $response['emails'] = NewsletterProForward::getEmailsToByEmailFrom($email);

                            exit(NewsletterProTools::jsonEncode($response));
                        } elseif ('deleteEmail' == Tools::getValue('action')) {
                            $response = ['status' => false, 'errors' => [], 'emails' => []];

                            $delete_email = Tools::getValue('delete_email');

                            if ($forward = NewsletterProForward::getInstanceByTo($delete_email)) {
                                if ($forward->from == $email) {
                                    if (!$forward->delete()) {
                                        $this->errors = array_merge($forward->getErrors(), $this->errors);
                                    }
                                } else {
                                    $this->errors[] = $this->translate->l('Permission denied. You cannot delete this email address.');
                                }
                            } else {
                                $this->errors[] = sprintf($this->translate->l('The email %s cannot be deleted.'), $delete_email);
                            }

                            if (empty($this->errors)) {
                                $response['status'] = true;
                            } else {
                                $response['errors'] = array_merge($response['errors'], $this->errors);
                            }

                            $response['emails'] = NewsletterProForward::getEmailsToByEmailFrom($email);
                            exit(NewsletterProTools::jsonEncode($response));
                        } elseif ('requestFriendSubscription' == Tools::getValue('action')) {
                            $response = ['status' => false, 'errors' => [], 'message' => ''];

                            $post_token = Tools::getValue('token');
                            $from_email = Tools::getValue('from_email');
                            $friend_email = Tools::getValue('friend_email');

                            if ($post_token == Tools::encrypt($from_email)) {
                                try {
                                    $file_tpl = dirname(__FILE__).'/../../views/templates/front/forward_subscribe.tpl';
                                    $this->context->smarty->assign([
                                        'from_email' => $from_email,
                                    ]);
                                    $content = $this->context->smarty->fetch($file_tpl);

                                    $template = NewsletterProTemplate::newString(['', $content], $friend_email)->load()->setVariables([
                                        'friend_email' => $from_email,
                                    ]);

                                    $message = $template->message();

                                    $send = NewsletterProSendManager::getInstance()->sendNewsletter(
                                        $this->translate->l('Your friend want to subscribe!'),
                                        $message['body'],
                                        $friend_email,
                                        ['user' => $template->user],
                                        [],
                                        false
                                    );

                                    if (!is_array($send) && true == $send) {
                                        $response['message'] = $this->translate->l('Your request was successfully sent.');
                                    } else {
                                        $this->errors = $this->translate->l('An error occurred when sending the email.');
                                    }
                                } catch (Exception $e) {
                                    $this->errors[] = $e->getMessage();
                                }
                            } else {
                                $this->errors[] = $this->translate->l('You cannot make this action because the token is not valid.');
                            }

                            if (empty($this->errors)) {
                                $response['status'] = true;
                            } else {
                                $response['errors'] = array_merge($response['errors'], $this->errors);
                            }

                            exit(NewsletterProTools::jsonEncode($response));
                        }
                    } else {
                        $this->errors[] = $this->translate->l('Invalid token.');
                    }
                } else {
                    $this->errors[] = sprintf($this->translate->l('The email %s is not valid.'), (string) Tools::getValue('email'));
                }
            } else {
                $this->errors[] = $this->translate->l('This feature is no longer active.');
            }
        } catch (Exception $e) {
            $this->errors[] = $this->translate->l('There is an error, please report this error to the website developer.');
            if (_PS_MODE_DEV_) {
                $this->errors[] = $e->getMessage();
            }

            NewsletterProLog::writeStrip($e->__toString(), NewsletterProLog::ERROR_FILE);
        }
    }

    public function getUserTableByEmail($email)
    {
        $definition = [
            'customer' => ['email' => 'email', 'newsletter' => 'newsletter'],
            'newsletter_pro_email' => ['email' => 'email', 'newsletter' => 'active'],
        ];

        if ((bool) pqnp_config('SUBSCRIPTION_ACTIVE')) {
            $definition['newsletter_pro_subscribers'] = ['email' => 'email', 'newsletter' => 'active'];
        } elseif (NewsletterProTools::tableExists('newsletter')) {
            $definition['newsletter'] = ['email' => 'email', 'newsletter' => 'active'];
        } elseif (NewsletterProTools::tableExists('emailsubscription')) {
            $definition['emailsubscription'] = ['email' => 'email', 'newsletter' => 'active'];
        }

        foreach ($definition as $table => $fields) {
            if (NewsletterProTools::tableExists($table)) {
                $sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.$table.'` WHERE `'.$fields['email'].'` = "'.pSQL($email).'"';

                if (Db::getInstance()->getValue($sql)) {
                    return [
                        'table' => $table,
                        'email' => $fields['email'],
                        'newsletter' => $fields['newsletter'],
                    ];
                }
            }
        }

        return false;
    }

    public function getHistoryIdByToken($token)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL($token).'"');
    }
}
