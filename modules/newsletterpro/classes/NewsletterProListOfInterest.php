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

class NewsletterProListOfInterest extends ObjectModel
{
    public $active;

    public $name;

    public $position;

    /* defined variables */

    public $errors = [];

    public static $definition = [
        'table' => 'newsletter_pro_list_of_interest',
        'primary' => 'id_newsletter_pro_list_of_interest',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255],

            /* Shop fields */
            'active' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'shop' => true],
            'position' => ['type' => self::TYPE_INT, 'shop' => true],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::initAssoTables();

        parent::__construct($id, $id_lang, $id_shop);

        $this->context = Context::getContext();
        $this->module = NewsletterPro::getInstance();
    }

    public static function initAssoTables()
    {
        NewsletterProTools::addTableAssociationArray(self::getAssoTables());
    }

    public static function isAvaliable($loi_id)
    {
        return (bool) Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_list_of_interest`
			WHERE `id_newsletter_pro_list_of_interest` = '.(int) $loi_id.'
			AND `active` = 1
		');
    }

    public static function getAssoTables()
    {
        return [
            'newsletter_pro_list_of_interest' => ['type' => 'shop'],
            // if it si liltiland multishop the fk_shop is requered, all the values will be availalbe in all the shop
            'newsletter_pro_list_of_interest_lang' => ['type' => 'shop'],
        ];
    }

    public function add($autodate = true, $null_values = false)
    {
        try {
            $position = (int) Db::getInstance()->getValue('SELECT MAX(`position`) FROM `'._DB_PREFIX_.'newsletter_pro_list_of_interest` WHERE 1');
            $this->position = ++$position;

            $return = parent::add($autodate, $null_values);

            if (!$return) {
                $this->addError($this->module->l('An error occurred when adding the record into database.'));
            }

            return $return;
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError($this->module->l('An error occurred when adding the record into database.'));
            }
        }

        return false;
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

    public static function getListSql($cfg = [])
    {
        $context = Context::getContext();

        if (!isset($cfg['id_lang'])) {
            $cfg['id_lang'] = $context->language->id;
        }

        if (!isset($cfg['id_shop'])) {
            $cfg['id_shop'] = $context->shop->id;
        }

        $sql = [];

        $sql[] = 'SELECT i.`id_newsletter_pro_list_of_interest`, il.`name`, iss.`id_shop`, iss.`active`, iss.`position` 
			FROM `'._DB_PREFIX_.'newsletter_pro_list_of_interest` i
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_list_of_interest_lang` il 
				ON (i.`id_newsletter_pro_list_of_interest` = il.`id_newsletter_pro_list_of_interest`)
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_list_of_interest_shop` iss
				ON (i.`id_newsletter_pro_list_of_interest` = iss.`id_newsletter_pro_list_of_interest`
					AND il.`id_shop` = iss.`id_shop`)
			WHERE il.`id_lang` = '.(int) $cfg['id_lang'].'
			AND il.`id_shop` = '.(int) $cfg['id_shop'];

        if (isset($cfg['and'])) {
            $sql[] = $cfg['and'];
        }

        $sql[] = ' ORDER BY iss.`position`';

        return implode(' ', $sql);
    }

    public static function getList($id_lang = null, $id_shop = null)
    {
        $id_lang = (isset($id_lang) ? $id_lang : Context::getContext()->language->id);
        $id_shop = (isset($id_shop) ? $id_shop : Context::getContext()->shop->id);

        $sql = self::getListSql([
            'id_lang' => (int) $id_lang,
            'id_shop' => (int) $id_shop,
        ]);

        return Db::getInstance()->executeS($sql);
    }

    public static function getListActive($id_lang = null, $id_shop = null)
    {
        $id_lang = (!isset($id_lang) ? Context::getContext()->language->id : $id_lang);
        $id_shop = (!isset($id_shop) ? Context::getContext()->shop->id : $id_shop);

        $sql = self::getListSql([
            'id_lang' => (int) $id_lang,
            'id_shop' => (int) $id_shop,
            'and' => ' AND iss.`active` = 1 ',
        ]);

        return Db::getInstance()->executeS($sql);
    }

    public static function getListActiveCustomer($id_customer, $id_lang = null, $id_shop = null)
    {
        $list = self::getListActive($id_lang, $id_shop);

        $customer_loi = NewsletterProCustomerListOfInterests::getInstanceByCustomerId((int) $id_customer);

        if (Validate::isLoadedObject($customer_loi) && count($customer_loi->getCategories()) > 0) {
            $categories = $customer_loi->getCategories();

            foreach ($list as $key => $value) {
                $id = $value['id_newsletter_pro_list_of_interest'];
                $list[$key]['checked'] = false;

                if (in_array($id, $categories)) {
                    $list[$key]['checked'] = true;
                }
            }
        } else {
            foreach ($list as $key => $value) {
                $list[$key]['checked'] = false;
            }
        }

        return $list;
    }

    public static function getListActiveSubscriber($email, $id_lang = null, $id_shop = null)
    {
        $list = self::getListActive($id_lang, $id_shop);

        $loi = [];
        $subscriber = NewsletterProSubscribers::getInstanceByEmail($email);
        if (Validate::isLoadedObject($subscriber)) {
            $loi = $subscriber->getListOfInterest();
        }

        if (count($loi) > 0) {
            foreach ($list as $key => $value) {
                $id = $value['id_newsletter_pro_list_of_interest'];
                $list[$key]['checked'] = false;

                if (in_array($id, $loi)) {
                    $list[$key]['checked'] = true;
                }
            }
        } else {
            foreach ($list as $key => $value) {
                $list[$key]['checked'] = false;
            }
        }

        return $list;
    }

    public function fillField($field_name, $values)
    {
        $default_lang = pqnp_config('PS_SHOP_DEFAULT');

        foreach (Language::getLanguages(true) as $lang) {
            $id_lang = $lang['id_lang'];
            $this->{$field_name}[$id_lang] = (isset($values[$id_lang]) ? $values[$id_lang] : $values[$default_lang]);
        }
    }
}
