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

class NewsletterProMailChimpFields
{
    public $errors = [];
    public $data = [];
    public $context;
    public $list_vars;

    public static $fields = [];

    public function __construct($list_vars)
    {
        $this->context = Context::getContext();
        $this->list_vars = $list_vars;

        $this->defineData();
        $this->defineFields();
    }

    public function defineFields()
    {
        $this->addField('FNAME', [
            'name' => 'First Name',
            'options' => [
                'field_type' => 'text',
                'req' => false,
            ],
        ]);

        $this->addField('LNAME', [
            'name' => 'Last Name',
            'options' => [
                'field_type' => 'text',
                'req' => false,
            ],
        ]);

        $this->addField('SHOP', [
            'name' => 'Shop',
            'options' => [
                'field_type' => 'dropdown',
                'default_value' => $this->getData('default_shop_name'),
                'choices' => ($this->getData('shops') ? $this->getData('shops_name') : $this->getData('default_shop_name')),
                'req' => false,
            ],
        ]);

        $this->addField('SUBSCRIBED', [
            'name' => 'Subscribed',
            'options' => [
                'field_type' => 'dropdown',
                'default_value' => 'yes',
                'choices' => ['yes', 'no'],
                'req' => false,
            ],
        ]);

        $this->addField('USER_TYPE', [
            'name' => 'User Type',
            'options' => [
                'field_type' => 'dropdown',
                'default_value' => 'Added',
                'choices' => ['Customer', 'Visitor', 'Added'],
                'req' => false,
            ],
        ]);

        $this->addField('LANGUAGE', [
            'name' => 'Language',
            'options' => [
                'field_type' => 'text',
                'default_value' => $this->getData('default_language_name'),
                'req' => false,
            ],
        ]);

        $this->addField('LAST_ORDER', [
            'name' => 'Last Order',
            'options' => [
                'field_type' => 'date',
                'dateformat' => 'MM/DD/YYYY',
                'req' => false,
            ],
        ]);

        $this->addField('OPTIN_IP', [
            'name' => 'Ip',
            'options' => [
                'field_type' => 'text',
                'req' => false,
            ],
        ]);

        $this->addField('BIRTHDAY', [
            'name' => 'Birthday',
            'options' => [
                'field_type' => 'birthday',
                'dateformat' => 'MM/DD',
                'req' => false,
            ],
        ]);

        $this->addField('ADDRESS', [
            'name' => 'Address',
            'options' => [
                'field_type' => 'address',
                'req' => false,
            ],
        ]);

        $this->addField('COMPANY', [
            'name' => 'Company',
            'options' => [
                'field_type' => 'text',
                'req' => false,
            ],
        ]);

        $this->addField('DATE_ADD', [
            'name' => 'Registration Date',
            'options' => [
                'field_type' => 'date',
                'dateformat' => 'MM/DD/YYYY',
                'req' => false,
            ],
        ]);

        $this->addField('PHONE', [
            'name' => 'Phone',
            'options' => [
                'field_type' => 'phone',
                'req' => false,
            ],
        ]);

        $this->addField('PHONE_MOB', [
            'name' => 'Phone Mobile',
            'options' => [
                'field_type' => 'phone',
                'req' => false,
            ],
        ]);
    }

    public function addField($name, $field)
    {
        self::$fields[$name] = $field;
    }

    public function defineData()
    {
        $id_lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->addData('id_lang_default', $id_lang_default);
        $this->addData('locale_country', Tools::strtoupper(Configuration::get('PS_LOCALE_COUNTRY')));
        $this->addData('id_shop_default', (int) Configuration::get('PS_SHOP_DEFAULT'));

        $groups = Group::getGroups($id_lang_default);
        $this->addData('groups', $groups);
        $this->addData('groups_name', self::grep($groups, 'name'));

        $shops = Shop::getShops(false);
        $this->addData('shops', $shops);
        $this->addData('shops_name', self::grep($shops, 'name'));
        $this->addData('default_shop_name', Configuration::get('PS_SHOP_NAME'));

        $default_language = Language::getLanguage($id_lang_default);
        $this->addData('default_language', $default_language);
        $this->addData('default_language_name', $default_language['name']);

        $languages = Language::getLanguages(false);
        $this->addData('languages', $languages);
        $this->addData('languages_name', self::grep($languages, 'name'));
    }

    public function getData($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return false;
    }

    public function addData($name, $data)
    {
        $this->data[$name] = $data;
    }

    public function getSyncVars()
    {
        $save_tags = [];

        $fields = $this->getFields();
        $vars_tags = self::grep($this->list_vars, 'tag');

        foreach ($fields as $tag => $value) {
            if (in_array($tag, $vars_tags)) {
                $save_tags['update'][$tag] = $value;
            } else {
                $save_tags['add'][$tag] = $value;
            }
        }

        return $save_tags;
    }

    public function getRestVars()
    {
        $fields = $this->getFields();
        $vars_tags = self::grep($this->list_vars, 'tag');
        $vars_tags_rest = array_diff($vars_tags, array_keys($fields));

        $searched_key = array_search('EMAIL', $vars_tags_rest);
        if (false !== $searched_key) {
            unset($vars_tags_rest[$searched_key]);
        }

        return $vars_tags_rest;
    }

    public static function grep($array, $name)
    {
        $return_array = [];
        foreach ($array as $value) {
            if (isset($value[$name])) {
                $return_array[] = $value[$name];
            }
        }

        return $return_array;
    }

    public function getFields()
    {
        return self::$fields;
    }

    public function addError($error, $code = null)
    {
        $add_error = [
            'code' => $code,
            'error' => Tools::displayError($error),
        ];

        $this->errors[] = $add_error;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors($only_errors = false, $collapse_same_code = false)
    {
        $errors = $this->errors;

        if ($collapse_same_code) {
            $errors_collapse = [];
            $errors_coldes = [];

            foreach ($errors as $error) {
                if (!in_array($error['code'], $errors_coldes)) {
                    $errors_collapse[] = $error;
                    $errors_coldes[] = $error['code'];
                }
            }

            $errors = $errors_collapse;
        }

        $return_errors = [];
        if ($only_errors) {
            foreach ($errors as $error) {
                $return_errors[] = $error['error'];
            }
        } else {
            $return_errors = $this->errors;
        }

        return $return_errors;
    }
}
