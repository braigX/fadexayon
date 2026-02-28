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

class NewsletterProSubscribeConfirmationModuleFrontController extends ModuleFrontController
{
    public $id_newsletter;

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
    }

    public function initContent()
    {
        parent::initContent();
        if (NewsletterProTools::is17()) {
            $this->setTemplate('module:newsletterpro/views/templates/front/1.7/subscribeconfirmation.tpl');
        } else {
            $this->setTemplate('subscribeconfirmation.tpl');
        }
    }

    public function getLink($params = [])
    {
        $params = array_merge($params, [
            'token' => Tools::getValue('token'),
        ]);

        return urldecode($this->context->link->getModuleLink($this->module->name, 'subscribeconfirmation', $params));
    }

    public function postProcess()
    {
        try {
            $token = Tools::getValue('token');
            $id = NewsletterProSubscribersTemp::getIdByToken($token);
            $subscriber_temp = new NewsletterProSubscribersTemp($id);

            if (Validate::isLoadedObject($subscriber_temp)) {
                $subscriber_id = (int) $subscriber_temp->moveToSubscribers();
                if (0 == $subscriber_id) {
                    $this->errors = array_merge($this->errors, $subscriber_temp->getErrors());
                } else {
                    NewsletterProSubscriptionManager::newInstance()->subscribe($subscriber_temp->email, (int) $this->context->shop->id, true);

                    $subscriber = new NewsletterProSubscribers((int) $subscriber_id, (int) $this->context->shop->id);
                    if (Validate::isLoadedObject($subscriber)) {
                        // update the customer lif of interest
                        $customer_loi = NewsletterProCustomerListOfInterests::getInstanceByCustomerEmail($subscriber->email);
                        if (Validate::isLoadedObject($customer_loi)) {
                            $customer_loi->setCategories($subscriber->getListOfInterest());
                            $customer_loi->update();
                        }
                    }
                }
            } else {
                $this->errors[] = $this->translate->l('This link has expired or the token in invalid.');
            }

            if (empty($this->errors)) {
                $success_message = [];

                $success_message[] = $this->translate->l('You have successfully subscribed at our newsletter.');

                $subscrbtion_template = $subscriber_temp->getSubscriptionTemplateInstance();

                if (is_object($subscrbtion_template)) {
                    if ($subscrbtion_template->hasValidVoucher()) {
                        $unsubscribe_link = urldecode($this->context->link->getModuleLink(NewsletterProTools::module()->name, 'unsubscribe', [
                            'email' => $subscriber_temp->email,
                            'u_token' => Tools::encrypt($subscriber_temp->email),
                            'msg' => true,
                        ], null, $this->context->language->id, $this->context->shop->id));

                        $subscrbtion_template->extendVars([
                            'unsubscribe_link' => $unsubscribe_link,
                            'unsubscribe' => '<a href="'.$unsubscribe_link.'" target="_blank">'.NewsletterProTools::module()->l('unsubscribe').'</a>',
                        ]);

                        $message = trim($subscrbtion_template->renderEmailSubscribeVoucherMessage((int) $this->context->language->id));
                        $subject = NewsletterProHTMLRender::getTitle($message);

                        $success_message[] = sprintf($this->translate->l('You can use this voucher %s.'), $subscrbtion_template->getVoucherCode());

                        if ((bool) pqnp_config('DEBUG_MODE')) {
                            NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $subscriber_temp->email, [], [], false);
                        } else {
                            @NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $subscriber_temp->email, [], [], false);
                        }
                    }
                }

                $this->context->smarty->assign([
                    'success_message' => $success_message,
                ]);
            }
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->errors[] = $e->getMessage();
            } else {
                $this->errors[] = $this->translate->l('There is an error, please report this error to the website developer.');
            }

            NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
        }
    }
}
