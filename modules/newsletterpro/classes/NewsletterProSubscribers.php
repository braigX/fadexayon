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

class NewsletterProSubscribers extends ObjectModel
{
    public $id_shop;

    public $id_shop_group;

    public $id_lang;

    public $id_gender;

    public $firstname;

    public $lastname;

    public $email;

    public $birthday;

    public $ip_registration_newsletter;

    public $list_of_interest;

    public $date_add;

    public $active;

    /* defined */
    public $context;

    public $module;

    public $errors = [];

    public static $definition = [
        'table' => 'newsletter_pro_subscribers',
        'primary' => 'id_newsletter_pro_subscribers',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_gender' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true],
            'birthday' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'ip_registration_newsletter' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'list_of_interest' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    public function __construct($id = null, $id_shop = null)
    {
        $this->context = Context::getContext();
        $this->module = NewsletterPro::getInstance();

        // add the new fields to the ObjectModel
        $variables_name = NewsletterProSubscribersCustomField::getVariables();

        if (!empty($variables_name)) {
            foreach ($variables_name as $variable_name) {
                $this->{$variable_name} = null;
                self::$definition['fields'][$variable_name] = ['type' => self::TYPE_STRING, 'validate' => 'isString'];
            }
        }

        parent::__construct($id, null, $id_shop);

        if (!isset($this->id_shop)) {
            $this->id_shop = (int) $this->context->shop->id;
        }

        if (!isset($this->id_shop_group)) {
            $this->id_shop_group = (int) $this->context->shop->id_shop_group;
        }

        if (!isset($this->id_lang)) {
            $this->id_lang = (int) $this->context->language->id_lang;
        }
    }

    public static function newInstance($id = null, $id_shop = null)
    {
        return new self($id, $id_shop);
    }

    public static function getDefinitionFields()
    {
        $custom_fields = [];
        $custom_variables = NewsletterProSubscribersCustomField::getVariables();

        foreach ($custom_variables as $variable_name) {
            $custom_fields[$variable_name] = ['type' => self::TYPE_STRING, 'validate' => 'isString'];
        }

        return array_merge(self::$definition['fields'], $custom_fields);
    }

    public function add($autodate = true, $null_values = true)
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

            $id_duplicate = (int) $this->isDuplicateEmail();
            if ($id_duplicate) {
                $this->addError(sprintf(NewsletterPro::getInstance()->l('The email "%s" already exists in our database.'), $this->email));
            }

            if ('string' != gettype($this->list_of_interest)) {
                $this->list_of_interest = (string) $this->list_of_interest;
            }

            if (!$this->hasErrors()) {
                return parent::add($autodate, $null_values);
            }
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError(NewsletterPro::getInstance()->l('An error occurred when inserting the record into database!'));
            }
        }

        return false;
    }

    public function update($null_values = true)
    {
        try {
            if ('string' != gettype($this->list_of_interest)) {
                $this->list_of_interest = (string) $this->list_of_interest;
            }

            if (!$this->hasErrors()) {
                return parent::update($null_values);
            }
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError(NewsletterPro::getInstance()->l('An error occurred when inserting the record into database!'));
            }
        }

        return false;
    }

    public function save($null_values = false, $autodate = true)
    {
        try {
            if ('string' != gettype($this->list_of_interest)) {
                $this->list_of_interest = (string) $this->list_of_interest;
            }

            if (!$this->hasErrors()) {
                return parent::save($null_values, $autodate);
            }
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError(NewsletterPro::getInstance()->l('An error occurred when inserting the record into database!'));
            }
        }

        return false;
    }

    public function isDuplicateEmail()
    {
        return Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_subscribers` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE `email` = "'.pSQL($this->email).'"
			');
    }

    public static function getIdByEmail($email, $id_shop = null)
    {
        if (!isset($id_shop)) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        return (int) Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_subscribers` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE `email` = "'.pSQL($email).'" and `id_shop` = '.(int) $id_shop.'
			');
    }

    public static function getInstanceByEmail($email, $id_shop = null)
    {
        return new NewsletterProSubscribers((int) self::getIdByEmail($email, $id_shop));
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

    public function getListOfInterest()
    {
        if (!isset($this->list_of_interest)) {
            return [];
        }

        return explode(',', trim($this->list_of_interest));
    }

    public function setListOfInterest(array $data)
    {
        $this->list_of_interest = $this->buildListOfInterest($data);
    }

    public function buildListOfInterest(array $data)
    {
        return rtrim(implode(',', $data), ',');
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_subscribers', $email);

        try {
            $results = Db::getInstance()->executeS('
				SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_subscribers`
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
                    NewsletterPro::getInstance()->l('Pop-Up') => '',
                    NewsletterPro::getInstance()->l('Subscribed') => ((int) $data['active'] ? NewsletterPro::getInstance()->l('Yes') : NewsletterPro::getInstance()->l('No')),
                    NewsletterPro::getInstance()->l('Firstname') => (string) $data['firstname'],
                    NewsletterPro::getInstance()->l('Lastname') => (string) $data['lastname'],
                    NewsletterPro::getInstance()->l('Email') => (string) $data['email'],
                    NewsletterPro::getInstance()->l('Birthday') => (string) $data['birthday'],
                    NewsletterPro::getInstance()->l('List of interest') => (string) count(explode(',', $data['list_of_interest'])),
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_subscribers', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscribers`
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_subscribers', $email);

        try {
            if (Db::getInstance()->delete('newsletter_pro_subscribers', '`email` = "'.pSQL($email).'"')) {
                $response->addToCount(Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
