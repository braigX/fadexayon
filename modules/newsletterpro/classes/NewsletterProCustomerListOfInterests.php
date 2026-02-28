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

class NewsletterProCustomerListOfInterests extends ObjectModel
{
    public $id_customer;

    public $categories;

    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => 'newsletter_pro_customer_list_of_interests',
        'primary' => 'id_newsletter_pro_customer_list_of_interests',
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
            SELECT `id_newsletter_pro_customer_list_of_interests`
            FROM `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests`
            WHERE `id_customer` = '.(int) $id_customer.'
        ');

        $instance = new NewsletterProCustomerListOfInterests($id);
        $instance->id_customer = (int) $id_customer;

        return $instance;
    }

    public static function getInstanceByCustomerEmail($email, $id_shop = null)
    {
        if (!isset($id_shop)) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $row = Db::getInstance()->getRow('
            SELECT loi.`id_newsletter_pro_customer_list_of_interests`, loi.`id_customer`
            FROM `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests` loi
            WHERE loi.`id_customer` = (
                SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`
                WHERE `email` = "'.pSQL($email).'"
                AND `id_shop` = '.(int) $id_shop.'
                LIMIT 1
            )
        ');

        if (empty($row)) {
            return new NewsletterProCustomerListOfInterests();
        }

        $instance = new NewsletterProCustomerListOfInterests((int) $row['id_newsletter_pro_customer_list_of_interests']);
        $instance->id_customer = (int) $row['id_customer'];

        return $instance;
    }

    public function getCategories()
    {
        return explode(',', trim($this->categories, ','));
    }

    public function setCategories(array $categories)
    {
        $this->categories = trim(implode(',', $categories), ',');
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_customer_list_of_interests', $email);

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
                        SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests`
                        WHERE `id_customer` = '.(int) $id_customer.'
                    ');

                    foreach ($res as $valu) {
                        $count += count(explode(',', $valu['categories']));
                    }
                }

                $response->addToExport([
                    NewsletterPro::getInstance()->l('List of interests') => '',
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_customer_list_of_interests', $email);

        try {
            $results = Db::getInstance()->executeS('
                SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`
                WHERE `email` = "'.pSQL($email).'"
            ');

            foreach ($results as $row) {
                $id_customer = (int) $row['id_customer'];

                $count = (int) Db::getInstance()->getValue('
                    SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests`
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_customer_list_of_interests', $email);

        try {
            $results = Db::getInstance()->executeS('
                SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`
                WHERE `email` = "'.pSQL($email).'"
            ');

            foreach ($results as $row) {
                $id_customer = (int) $row['id_customer'];

                if (Db::getInstance()->delete('newsletter_pro_customer_list_of_interests', '`id_customer` = '.(int) $id_customer)) {
                    $response->addToCount(Db::getInstance()->Affected_Rows());
                }
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
