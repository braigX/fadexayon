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

class NewsletterProPopupAction extends NewsletterProAction
{
    private $error_message;

    private $translate;

    public function __construct()
    {
        parent::__construct();

        $this->translate = new NewsletterProTranslate(__CLASS__);

        $this->error_message = $this->translate->l('Oops, an error has occurred.');
    }

    public static function newInstance()
    {
        return new self();
    }

    public function call($action)
    {
        try {
            switch ($action) {
                case 'subscribe':
                    return $this->subscribe();
                case 'unsubscribe':
                    return $this->unsubscribe();
                case 'destroy':
                    return $this->destroy();
            }
        } catch (Exception $e) {
            NewsletterProLog::writeStrip($e->__toString(), NewsletterProLog::ERROR_FILE);

            if (_PS_MODE_DEV_ && (bool) pqnp_config('DEBUG_MODE')) {
                return $this->response->error($e->__toString())->output();
            } else {
                return $this->response->error($this->error_message)->output();
            }
        }

        parent::call();
    }

    private function getTemplate()
    {
        $template = new NewsletterProSubscriptionTpl((int) $this->request->get('templateId', 0));
        if (!Validate::isLoadedObject($template)) {
            $load_file = (string) $this->request->get('loadFile', '');
            if (0 == Tools::strlen($load_file)) {
                return $this->response->error($this->translate->l('Unable to load the template file.'))->output();
            }

            // this is for development
            $load_dirname = _NEWSLETTER_PRO_DIR_.'/install/tables/subscription_tpl/'.$load_file.'/';
            $template = NewsletterProSubscriptionTpl::loadFile($load_dirname, true, true);
        }

        return $template;
    }

    private function subscribe()
    {
        $self = $this;
        $errors = [];
        $form_errors = [];

        $context = Context::getContext();
        $template = $this->getTemplate();

        if (!$this->request->has('terms_and_conditions')) {
            $this->request->set('terms_and_conditions', 0, NewsletterProRequest::TYPE_POST);
        }

        // validate the fields
        $this->request->validate($errors, $form_errors, [
            'email' => ['type' => ObjectModel::TYPE_STRING, 'modifier' => ['trim'], 'validate' => [
                ['func' => 'NewsletterProValidate::isFilled', 'message' => $this->translate->l('The Email Address is required.')],
                ['func' => 'isEmail', 'message' => $this->translate->l('The Email Address is not valid.')],
            ]],
            'terms_and_conditions' => ['type' => ObjectModel::TYPE_BOOL, 'modifier' => ['intval'], 'validate' => [
                ['func' => function ($tac) use ($self) {
                    if (false == (bool) $tac) {
                        return $self->translate->l('You must agree with the terms and conditions.');
                    }
                }],
            ]],
        ]);

        if ((bool) $template->display_firstname && $this->request->has('firstname')) {
            if ($template->isMandatory('firstname')) {
                $this->request->validate($errors, $form_errors, [
                    'firstname' => ['type' => ObjectModel::TYPE_STRING, 'modifier' => ['trim'], 'validate' => [
                        ['func' => 'NewsletterProValidate::isFilled', 'message' => $this->translate->l('The First Name is required.')],
                        ['func' => 'isName', 'message' => $this->translate->l('The First Name is not valid.')],
                    ]],
                ]);
            } elseif ($this->request->has('firstname')) {
                $this->request->validate($errors, $form_errors, [
                    'firstname' => ['type' => ObjectModel::TYPE_STRING, 'modifier' => ['trim'], 'validate' => [
                        ['func' => 'isName', 'message' => $this->translate->l('The First Name is not valid.')],
                    ]],
                ]);
            }
        }

        if ((bool) $template->display_lastname && $this->request->has('lastname')) {
            if ($template->isMandatory('lastname')) {
                $this->request->validate($errors, $form_errors, [
                    'lastname' => ['type' => ObjectModel::TYPE_STRING, 'modifier' => ['trim'], 'validate' => [
                        ['func' => 'NewsletterProValidate::isFilled', 'message' => $this->translate->l('The Last Name is required.')],
                        ['func' => 'isName', 'message' => $this->translate->l('The Last Name is not valid.')],
                    ]],
                ]);
            } elseif ($this->request->has('lastname')) {
                $this->request->validate($errors, $form_errors, [
                    'lastname' => ['type' => ObjectModel::TYPE_STRING, 'modifier' => ['trim'], 'validate' => [
                        ['func' => 'isName', 'message' => $this->translate->l('The Last Name is not valid.')],
                    ]],
                ]);
            }
        }

        if ((bool) $template->display_gender && $this->request->has('gender')) {
            $this->request->validate($errors, $form_errors, [
                'gender' => ['type' => ObjectModel::TYPE_INT, 'modifier' => ['intval'], 'validate' => [
                    ['func' => function ($gender_id) use ($self) {
                        if (0 !== (int) $gender_id) {
                            if (!Validate::isLoadedObject(new Gender((int) $gender_id))) {
                                return $self->translate->l('The Gender is not valid.');
                            }
                        }
                    }],
                ]],
            ]);
        }

        if ((bool) $template->display_birthday && $this->request->has('birthday')) {
            $this->request->validate($errors, $form_errors, [
                'birthday' => ['type' => ObjectModel::TYPE_DATE, 'modifier' => ['trim'], 'validate' => [
                    ['func' => function ($birthday) use ($self) {
                        $message = $self->translate->l('The Birthday is not valid.');

                        if (Tools::strlen($birthday) > 0) {
                            if (!Validate::isDate($birthday)) {
                                return $message;
                            }

                            $date = explode('-', $birthday);

                            if (!array_key_exists(0, $date)) {
                                return $message;
                            }

                            $year = (int) $date[0];

                            if ($year < 1900 || $year > ((int) date('Y') - 14)) {
                                return $message;
                            }
                        }
                    }],
                ]],
            ]);
        }

        if ((bool) $template->display_birthday && $this->request->has('birthday_day') && $this->request->has('birthday_month') && $this->request->has('birthday_year')) {
            $birthday_year = $this->request->get('birthday_year', '0');
            $birthday_month = $this->request->get('birthday_month', '0');
            $birthday_day = $this->request->get('birthday_day', '0');

            $birthday = trim($this->request->get('birthday_year', '').'-'.$this->request->get('birthday_month').'-'.$this->request->get('birthday_day'));

            $message = $self->translate->l('The Birthday is not valid.');

            if ('0' !== $birthday_year || '0' !== $birthday_month || '0' !== $birthday_day) {
                $date = explode('-', $birthday);
                $year = (int) $date[0];

                if (!Validate::isDate($birthday)) {
                    $errors[] = $message;
                } elseif (!array_key_exists(0, $date)) {
                    $errors[] = $message;
                } elseif ($year < 1900 || $year > ((int) date('Y') - 14)) {
                    $errors[] = $message;
                }
            }
        }

        if ((bool) $template->display_list_of_interest && $this->request->has('list_of_interest')) {
            $list_of_interest = [];
            $loi = $this->request->get('list_of_interest', []);

            foreach ($loi as $loi_id) {
                if (NewsletterProListOfInterest::isAvaliable((int) $loi_id)) {
                    $list_of_interest[(int) $loi_id] = true;
                }
            }

            $list_of_interest = array_keys($list_of_interest);
            $this->request->set('list_of_interest', $list_of_interest, NewsletterProRequest::TYPE_POST);
        }

        // validate the custom fields
        $custom_variables = NewsletterProSubscribersCustomField::getVariablesDetails();
        $custom_fields = [];

        foreach ($custom_variables as $variable_name => $variable) {
            if ($this->request->has($variable_name)) {
                $variable_name_display = trim(implode(' ', array_map(function ($word) {
                    return Tools::ucfirst(trim($word));
                }, explode('_', preg_replace('/^np_/', '', $variable_name)))));

                switch ((int) $variable['type']) {
                    case NewsletterProSubscribersCustomField::TYPE_SELECT:
                    case NewsletterProSubscribersCustomField::TYPE_INPUT_TEXT:
                    case NewsletterProSubscribersCustomField::TYPE_RADIO:
                    case NewsletterProSubscribersCustomField::TYPE_TEXTAREA:
                        $variable_value = trim($this->request->get($variable_name, ''));
                        if ((bool) $variable['required'] && 0 == Tools::strlen($variable_value)) {
                            $errors[] = sprintf($this->translate->l('The field %s is required.'), $variable_name_display);
                        }

                        $custom_fields[$variable_name] = $variable_value;
                        break;

                    case NewsletterProSubscribersCustomField::TYPE_CHECKBOX:
                        $variable_value = $this->request->get($variable_name, []);

                        if ((bool) $variable['required'] && 0 == count($variable_value)) {
                            $errors[] = sprintf($this->translate->l('The field [%s] is required.'), $variable_name_display);
                        }
                        $custom_fields[$variable_name] = NewsletterProTools::jsonEncode($variable_value);
                        break;
                }
            }
        }

        if (!$template->allow_multiple_time_subscription) {
            $result = NewsletterProListManager::parse(function ($table_name, $fields) use ($self, $context) {
                return (bool) Db::getInstance()->getValue('
                    SELECT COUNT(*) FROM `'._DB_PREFIX_.pSQL($table_name).'`
                    WHERE `'.pSQL($fields['email']).'` = "'.$self->request->get('email', '').'"
                    AND `'.pSQL($fields['active']).'` = 1
                    AND `id_shop` = '.(int) $context->shop->id.'
                ');

                return true;
            });

            if (in_array(true, $result)) {
                $errors[] = $this->translate->l('The email address is already subscribed at our newsletter.');
            }
        }

        if (!empty($errors)) {
            return $this->response->error($errors)->output();
        }

        $email = $this->request->get('email');

        $subscriber = NewsletterProSubscribers::getInstanceByEmail($email, (int) $context->shop->id);
        $subscriber->email = $email;
        $subscriber->id_gender = $this->request->get('gender', 0);
        $subscriber->firstname = $this->request->get('firstname', '');
        $subscriber->lastname = $this->request->get('lastname', '');
        if ($this->request->has('birthday')) {
            $subscriber->birthday = $this->request->get('birthday', '');
        } elseif ($this->request->has('birthday_day') &&
                    $this->request->has('birthday_month') &&
                    $this->request->has('birthday_year')) {
            $birthday_date = trim($this->request->get('birthday_year', '').'-'.$this->request->get('birthday_month', '').'-'.$this->request->get('birthday_day', ''));

            if (Validate::isDate($birthday_date)) {
                $subscriber->birthday = $birthday_date;
            }
        }

        $subscriber->list_of_interest = $subscriber->buildListOfInterest($this->request->get('list_of_interest', []));
        $subscriber->id_shop = (int) $context->shop->id;
        $subscriber->id_shop_group = (int) $context->shop->id_shop_group;
        $subscriber->id_lang = (int) $context->language->id;
        $subscriber->ip_registration_newsletter = Tools::getRemoteAddr();
        $subscriber->date_add = date('Y-m-d H:i:s');
        $subscriber->active = true;

        foreach ($custom_fields as $field_name => $value) {
            $subscriber->{$field_name} = $value;
        }

        // secure subscription section
        if ((bool) pqnp_config('SUBSCRIPTION_SECURE_SUBSCRIBE')) {
            $subscriber_temp = new NewsletterProSubscribersTemp();
            $subscriber_temp->id_newsletter_pro_subscription_tpl = (int) $template->id;
            $subscriber_temp->load_file = $template->getLoadFileBasename();

            if (!$subscriber_temp->saveTemp($subscriber)) {
                return $this->response->error($this->error_message)->output();
            }

            $template->extendVars([
                'firstname' => htmlentities($subscriber->firstname),
                'lastname' => htmlentities($subscriber->lastname),
                'email_confirmation_link' => $subscriber_temp->getConfirmationLink(),
                'email_confirmation' => '<a href="'.$subscriber_temp->getConfirmationLink().'" style="color: blue;">'.$this->translate->l('here').'</a>',
            ]);

            $message = trim($template->renderEmailSubscribeConfirmationMessage((int) $context->language->id));
            $subject = NewsletterProHTMLRender::getTitle($message);

            if ((bool) pqnp_config('DEBUG_MODE')) {
                $send = NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $subscriber->email, [], [], false);
            } else {
                $send = @NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $subscriber->email, [], [], false);
            }

            if (is_array($send)) {
                return $this->response->error($this->translate->l('Oops, an error has occurred, we are unable to send the confirmation email.'))->output();
            }

            $message = sprintf($this->translate->l('A confirmation email has been sent to the email address "%s". To subscribe please click on the link from your email address.'), $subscriber->email);

            return $this->response->setData([
                'newWindow' => (bool) $template->display_subscribe_message ? true : false,
                'message' => (bool) $template->display_subscribe_message ? '<p>'.htmlentities(html_entity_decode($message), ENT_NOQUOTES).'</p>' : $message,
                'closeTimeout' => 8000,
            ])->output();
        }

        // no secure subscribe section
        if (!$subscriber->save()) {
            return $this->response->error($this->error_message)->output();
        }

        $consent = NewsletterProSubscriptionConsent::newInstance()->set($subscriber->email, (bool) $subscriber->active, $context->customer->isLogged());
        NewsletterProSubscriptionManager::newInstance()->subscribe($subscriber->email, (int) $context->shop->id, $consent);

        // copy the categoreis into the customer my account
        $customer_loi = NewsletterProCustomerListOfInterests::getInstanceByCustomerId((int) $context->customer->id);
        if (Validate::isLoadedObject($customer_loi)) {
            $customer_loi->setCategories($subscriber->getListOfInterest());
            $customer_loi->update();
        }

        $unsubscribe_link = urldecode($context->link->getModuleLink(NewsletterProTools::module()->name, 'unsubscribe', [
                'email' => $subscriber->email,
                'u_token' => Tools::encrypt($subscriber->email),
                'msg' => true,
            ], null, $context->language->id, $context->shop->id));

        $template->extendVars([
            'unsubscribe_link' => $unsubscribe_link,
            'unsubscribe' => '<a href="'.$unsubscribe_link.'" target="_blank">'.NewsletterProTools::module()->l('unsubscribe').'</a>',
        ]);

        if ($template->hasValidVoucher()) {
            // send the voucher to the subscriber email
            $message = trim($template->renderEmailSubscribeVoucherMessage((int) $context->language->id));
            $subject = NewsletterProHTMLRender::getTitle($message);

            if ((bool) pqnp_config('DEBUG_MODE')) {
                $send = NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $subscriber->email, [], [], false);
            } else {
                $send = @NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $subscriber->email, [], [], false);
            }

            // show the voucher to the customer
            $message = trim($template->renderSubscribeMessage((int) $context->language->id));
            $message_strip = trim(strip_tags($message));

            if ((bool) $template->display_subscribe_message) {
                return $this->response->setData([
                    'newWindow' => true,
                    'message' => $message,
                ])->output();
            }

            return $this->response->setData([
                'newWindow' => false,
                'message' => $message_strip,
                'closeTimeout' => 10000,
            ])->output();
        }

        return $this->response->setData([
            'newWindow' => false,
            'message' => $this->translate->l('You have been subscribed to our newsletter.'),
        ])->output();
    }

    private function unsubscribe()
    {
        $errors = [];
        $form_errors = [];
        $context = Context::getContext();
        $template = $this->getTemplate();

        $this->request->validate($errors, $form_errors, [
            'email' => ['type' => ObjectModel::TYPE_STRING, 'modifier' => ['trim'], 'validate' => [
                ['func' => 'NewsletterProValidate::isFilled', 'message' => $this->translate->l('The Email Address is required.')],
                ['func' => 'isEmail', 'message' => $this->translate->l('The Email Address is not valid.')],
            ]],
        ]);

        if (!empty($errors)) {
            return $this->response->error($errors)->output();
        }

        $email = $this->request->get('email');
        $unsubscribe_link = urldecode($context->link->getModuleLink('newsletterpro', 'unsubscribe', [
            'email' => $email,
            'u_token' => Tools::encrypt($email),
            'msg' => true,
        ], null, (int) $context->language->id, $context->shop->id));

        $template->extendVars([
            'unsubscribe_link' => $unsubscribe_link,
            'unsubscribe' => '<a href="'.$unsubscribe_link.'" style="color: blue;">'.$this->translate->l('here').'</a>',
        ]);

        $message = trim($template->renderEmailUnsubscribeConfirmationMessage((int) $context->language->id));
        $subject = NewsletterProHTMLRender::getTitle($message);

        if ((bool) pqnp_config('DEBUG_MODE')) {
            $send = NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $email, [], [], false);
        } else {
            $send = @NewsletterProSendManager::getInstance()->sendNewsletter($subject, $message, $email, [], [], false);
        }

        if (is_array($send)) {
            return $this->response->error($this->translate->l('Oops, an error has occurred, we are unable to send the confirmation email.'))->output();
        }

        $message = sprintf($this->translate->l('A confirmation email has been sent to the email address "%s". To unsubscribe please click on the link from your email address.'), $email);

        return $this->response->setData([
            'newWindow' => (bool) $template->display_subscribe_message ? true : false,
            'message' => (bool) $template->display_subscribe_message ? '<p>'.htmlentities(html_entity_decode($message), ENT_NOQUOTES).'</p>' : $message,
            'closeTimeout' => 8000,
        ])->output();
    }

    private function destroy()
    {
        $template = $this->getTemplate();

        $life_time_days = round((60 * 60 * 24) * (float) $template->cookie_lifetime);
        $cookie = new NewsletterProCookie('subscription_template_front', time() + $life_time_days);
        $cookie->set('popup_show', '0');

        if (true == (bool) $cookie->get('popup_show')) {
            return $this->response->error($this->error_message)->output();
        }

        return $this->response->setData([])->output();
    }
}
