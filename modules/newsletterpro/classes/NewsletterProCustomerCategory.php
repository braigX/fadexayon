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

class NewsletterProCustomerCategory extends ObjectModel
{
    public $id_customer;

    public $categories;

    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => 'newsletter_pro_customer_category',
        'primary' => 'id_newsletter_pro_customer_category',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'categories' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getInstanceByCustomerId($id_customer)
    {
        $id = (int) Db::getInstance()->getValue('
            SELECT `id_newsletter_pro_customer_category`
            FROM `'._DB_PREFIX_.'newsletter_pro_customer_category`
            WHERE `id_customer` = '.(int) $id_customer.'
        ');

        return new NewsletterProCustomerCategory($id);
    }

    public function getCategories()
    {
        return explode(',', trim($this->categories, ','));
    }

    public function setCategories(array $categories)
    {
        $this->categories = trim(implode(',', $categories), ',');
    }

    public static function getCategoriesByIdCustomer($id_customer)
    {
        return Db::getInstance()->getValue('
			SELECT `categories` FROM `'._DB_PREFIX_.'newsletter_pro_customer_category`
			WHERE `id_customer` = '.(int) $id_customer.'
		');
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_customer_category', $email);

        try {
            $results = Db::getInstance()->executeS('
                SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`
                WHERE `email` = "'.pSQL($email).'"
            ');

            if (count($results) > 0) {
                $count = 0;
                foreach ($results as $row) {
                    $id_customer = (int) $row['id_customer'];

                    $res = Db::getInstance()->executeS('
                        SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_customer_category`
                        WHERE `id_customer` = '.(int) $id_customer.'
                    ');

                    foreach ($res as $valu) {
                        $count += count(explode(',', $valu['categories']));
                    }
                }

                $response->addToExport([
                    NewsletterPro::getInstance()->l('Category subscription') => '',
                    NewsletterPro::getInstance()->l('Total') => $count,
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_customer_category', $email);

        try {
            $results = Db::getInstance()->executeS('
                SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`
                WHERE `email` = "'.pSQL($email).'"
            ');

            foreach ($results as $row) {
                $id_customer = (int) $row['id_customer'];

                $count = (int) Db::getInstance()->getValue('
                    SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_customer_category`
                    WHERE `id_customer` = '.(int) $id_customer.'
                ');
                $response->addToCount($count);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function clearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_customer_category', $email);

        try {
            $results = Db::getInstance()->executeS('
                SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`
                WHERE `email` = "'.pSQL($email).'"
            ');

            foreach ($results as $row) {
                $id_customer = (int) $row['id_customer'];

                if (Db::getInstance()->delete('newsletter_pro_customer_category', '`id_customer` = '.(int) $id_customer)) {
                    $response->addToCount(Db::getInstance()->Affected_Rows());
                }
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
