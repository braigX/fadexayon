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

class NewsletterProSubscribersTemp extends ObjectModel
{
    public $email;

    public $id_newsletter_pro_subscription_tpl;

    public $load_file;

    public $token;

    public $data;

    public $date_add;

    /* defined */
    public $context;

    public $module;

    public $errors = [];

    public static $definition = [
        'table' => 'newsletter_pro_subscribers_temp',
        'primary' => 'id_newsletter_pro_subscribers_temp',
        'fields' => [
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true],
            'id_newsletter_pro_subscription_tpl' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'load_file' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'token' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'data' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public function __construct($id = null)
    {
        $this->context = Context::getContext();
        $this->module = NewsletterPro::getInstance();

        parent::__construct($id);
    }

    public function copyFromSubscribe(NewsletterProSubscribers $subscribe)
    {
        $result = [];
        foreach (array_keys(NewsletterProSubscribers::getDefinitionFields()) as $field_name) {
            $result[$field_name] = $subscribe->{$field_name};
        }

        return $result;
    }

    public function saveTemp(NewsletterProSubscribers $subscribe)
    {
        $data = $this->copyFromSubscribe($subscribe);

        $this->email = $subscribe->email;
        $this->token = Tools::encrypt($this->email);
        $this->data = serialize($data);
        $this->date_add = date('Y-m-d H:i:s');

        if ($this->isDuplicateEmail()) {
            $id = self::getIdByEmail($this->email);
            $obj = new NewsletterProSubscribersTemp($id);

            if (Validate::isLoadedObject($obj)) {
                foreach (array_keys(self::$definition['fields']) as $field_name) {
                    $obj->{$field_name} = $this->{$field_name};
                }

                return $obj->save();
            }
        }

        return $this->add();
    }

    public function add($autodate = true, $null_values = false)
    {
        try {
            if (!Validate::isEmail($this->email)) {
                $this->addError(sprintf('The email "%s" is not a valid email address.', $this->email));
            }

            if (!$this->hasErrors()) {
                return parent::add($autodate, $null_values);
            }
        } catch (Exception $e) {
            NewsletterProLog::writeStrip($e->__toString(), NewsletterProLog::ERROR_FILE);

            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError('An error occurred when inserting the record into database!');
            }
        }

        return false;
    }

    public function update($null_values = false)
    {
        try {
            return parent::update($null_values);
        } catch (Exception $e) {
            NewsletterProLog::writeStrip($e->__toString(), NewsletterProLog::ERROR_FILE);

            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError('An error occurred when updateing the record into database!');
            }
        }

        return false;
    }

    public function getConfirmationLink()
    {
        return Context::getContext()->link->getModuleLink('newsletterpro', 'subscribeconfirmation', ['token' => $this->token]);
    }

    public function moveToSubscribers()
    {
        if ($subscribe = $this->buildSubscribersObj()) {
            if (!$subscribe->save()) {
                foreach ($subscribe->getErrors() as $error) {
                    $this->addError($error);
                }
            } else {
                $this->delete();
            }

            return (int) $subscribe->id;
        }

        return 0;
    }

    public static function isSerialized($str)
    {
        return is_array(@unserialize($str));
    }

    public function buildSubscribersObj()
    {
        $data = [];

        if (self::isSerialized($this->data)) {
            $data = unserialize($this->data);
        } else {
            $this->addError($this->module->l('Invalid serielized data.'));

            return false;
        }

        $id = NewsletterProSubscribers::getIdByEmail($data['email']);
        $subscribe = new NewsletterProSubscribers($id);

        $subscribe->id_shop = (int) $data['id_shop'];
        $subscribe->id_shop_group = (int) $data['id_shop_group'];
        $subscribe->id_lang = (int) $data['id_lang'];
        $subscribe->id_gender = $data['id_gender'];
        $subscribe->firstname = $data['firstname'];
        $subscribe->lastname = $data['lastname'];
        $subscribe->email = $data['email'];
        $subscribe->birthday = $data['birthday'];
        $subscribe->list_of_interest = $data['list_of_interest'];
        $subscribe->ip_registration_newsletter = $data['ip_registration_newsletter'];
        $subscribe->date_add = $data['date_add'];
        $subscribe->active = (int) $data['active'];

        $custom_fields = NewsletterProSubscribersCustomField::getVariables();

        foreach ($custom_fields as $variable) {
            if (array_key_exists($variable, $data)) {
                $subscribe->{$variable} = $data[$variable];
            }
        }

        return $subscribe;
    }

    public function isDuplicateEmail()
    {
        return (int) Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` WHERE `email` = "'.pSQL($this->email).'"
		');
    }

    public static function getIdByEmail($email)
    {
        return (int) Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_subscribers_temp` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` WHERE `email` = "'.pSQL($email).'"
		');
    }

    public static function getIdByToken($token)
    {
        return (int) Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_subscribers_temp` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` WHERE `token` = "'.pSQL($token).'"
		');
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_subscribers_temp', $email);

        try {
            // no data to export
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_subscribers_temp', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp`
				WHERE `email` = "'.pSQL($email).'"
			');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function clearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_subscribers_temp', $email);

        try {
            if (Db::getInstance()->delete('newsletter_pro_subscribers_temp', '`email` = "'.pSQL($email).'"')) {
                $response->addToCount(Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public function getSubscriptionTemplateInstance()
    {
        $template = new NewsletterProSubscriptionTpl((int) $this->id_newsletter_pro_subscription_tpl);

        if (!Validate::isLoadedObject($template)) {
            if (isset($this->load_file) && 0 == Tools::strlen($this->load_file)) {
                return false;
            }

            // this is for development
            $load_dirname = _NEWSLETTER_PRO_DIR_.'/install/tables/subscription_tpl/'.$this->load_file.'/';
            $template = NewsletterProSubscriptionTpl::loadFile($load_dirname, true, true);
        }

        return $template;
    }
}
