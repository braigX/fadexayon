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

class NewsletterProEmail extends ObjectModel
{
    public $id_shop;

    public $id_shop_group;

    public $id_lang;

    public $firstname;

    public $lastname;

    public $email;

    public $ip_registration_newsletter;

    public $filter_name;

    public $date_add;

    public $active;

    /* defined */
    public $context;

    public $module;

    public $errors = [];

    public static $definition = [
        'table' => 'newsletter_pro_email',
        'primary' => 'id_newsletter_pro_email',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true],
            'ip_registration_newsletter' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'filter_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    public function __construct($id = null)
    {
        $this->context = Context::getContext();
        $this->module = NewsletterPro::getInstance();

        parent::__construct($id);

        $this->id_shop = $this->context->shop->id;
        $this->id_shop_group = $this->context->shop->id_shop_group;
        $this->id_lang = $this->context->language->id;
        $this->firstname = '';
        $this->lastname = '';
        $this->ip_registration_newsletter = Tools::getRemoteAddr();
        $this->date_add = date('Y-m-d H:i:s');
        $this->active = 1;
    }

    public static function newInstance($id = null)
    {
        return new self($id);
    }

    public function add($autodate = true, $null_values = false)
    {
        try {
            if (!Validate::isName($this->firstname)) {
                $this->addError(sprintf(NewsletterPro::getInstance()->l('The "%s" is not a valid name.'), $this->firstname));
            }

            if (!Validate::isName($this->lastname)) {
                $this->addError(sprintf(NewsletterPro::getInstance()->l('The "%s" is not a valid name.'), $this->lastname));
            }

            if (!Validate::isEmail($this->email)) {
                $this->addError(sprintf(NewsletterPro::getInstance()->l('The email "%s" is not a valid email address.'), $this->email));
            }

            if ($this->isDuplicateEmail()) {
                $this->addError(sprintf(NewsletterPro::getInstance()->l('The email "%s" already exists in our database.'), $this->email));
            }

            if (!$this->hasErrors()) {
                return parent::add($autodate, $null_values);
            }
        } catch (Exception $e) {
            $this->addError(NewsletterPro::getInstance()->l('An error occurred when inserting the record into database!'));
        }

        return false;
    }

    public function isDuplicateEmail()
    {
        return Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE `email` = "'.pSQL($this->email).'"
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_email', $email);

        try {
            $results = Db::getInstance()->executeS('
				SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_email`
				WHERE `email` = "'.pSQL($email).'"
			');

            if (count($results) > 0) {
                $data = null;
                foreach ($results as $row) {
                    if (true == (bool) $row['active']) {
                        $data = $row;
                        break;
                    }
                }

                if (!isset($data)) {
                    $data = $row;
                }

                $response->addToExport([
                    NewsletterPro::getInstance()->l('Personal list') => '',
                    NewsletterPro::getInstance()->l('Subscribed') => ((int) $data['active'] ? NewsletterPro::getInstance()->l('Yes') : NewsletterPro::getInstance()->l('No')),
                    NewsletterPro::getInstance()->l('Firstname') => (string) $data['firstname'],
                    NewsletterPro::getInstance()->l('Lastname') => (string) $data['lastname'],
                    NewsletterPro::getInstance()->l('Email') => (string) $data['email'],
                    NewsletterPro::getInstance()->l('IP address') => (string) $data['ip_registration_newsletter'],
                    NewsletterPro::getInstance()->l('Date add') => (string) $data['date_add'],
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_email', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE `email` = "'.pSQL($email).'"
			');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function clearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_email', $email);

        try {
            if (Db::getInstance()->delete('newsletter_pro_email', '`email` = "'.pSQL($email).'"')) {
                $response->addToCount((int) Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
