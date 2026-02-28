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

use PQNP\Version;

class NewsletterProCategoryTree
{
    private $home_id;

    private $customer_id;

    public function __construct($customer_id = 0)
    {
        $this->home_id = (Version::isLower('1.5.0.5') ? 1 : 2);
        $this->customer_id = $customer_id;
    }

    public static function newInstance($customer_id = 0)
    {
        return new self($customer_id);
    }

    public function home($id_lang = null, $id_shop = null)
    {
        return $this->getCategoryById($this->home_id, $id_lang, $id_shop);
    }

    public function category($id_category, $id_lang = null, $id_shop = null)
    {
        if (is_array($id_category) && !empty($id_category)) {
            $id_category = $id_category['id_category'];
        }

        return $this->getCategoryById($id_category, $id_lang, $id_shop);
    }

    public function childrens($id_category, $id_lang = null, $id_shop = null)
    {
        if (is_array($id_category) && !empty($id_category)) {
            $id_category = $id_category['id_category'];
        }

        $categores = $this->getCategoryChildrensById($id_category, $id_lang, $id_shop);

        return array_values(array_filter($categores, function ($category) {
            $id = $category['id_category'];
            $name = $category['name'];

            return true;
        }));
    }

    private function getQuery($id_category, $id_lang, $id_shop, $where)
    {
        return '
			SELECT 
				c.`id_category`, c.`id_parent`, c.`active`, c.`level_depth`, cl.`id_lang`, cl.`name`,
				(SELECT COUNT(*) FROM `'._DB_PREFIX_.'category`
					WHERE `id_parent` = c.`id_category`
				) AS `childrens_count`,
				(SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_customer_category`
					WHERE `id_customer` = '.(int) $this->customer_id.' 
					AND FIND_IN_SET(c.`id_category`, `categories`)
				) AS `checked`
			FROM `'._DB_PREFIX_.'category` c
			INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (
				c.`id_category` = cl.`id_category` AND
				cl.`id_lang` = '.(int) $id_lang.'
			)
			INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (
				c.`id_category` = cs.`id_category` AND
				cs.`id_shop` = '.(int) $id_shop.'
			)
			WHERE '.pSQL($where).'
            AND c.`active` = 1
			'
        ;
    }

    private function getCategoryById($id_category, $id_lang = null, $id_shop = null)
    {
        $context = Context::getContext();

        if (!isset($id_lang)) {
            $id_lang = (int) $context->language->id;
        }
        if (!isset($id_shop)) {
            $id_shop = (int) $context->shop->id;
        }

        $category = Db::getInstance()->getRow($this->getQuery($id_category, $id_lang, $id_shop, 'c.`id_category` = '.(int) $id_category));

        return $category;
    }

    private function getCategoryChildrensById($id_category, $id_lang = null, $id_shop = null)
    {
        $context = Context::getContext();

        if (!isset($id_lang)) {
            $id_lang = (int) $context->language->id;
        }
        if (!isset($id_shop)) {
            $id_shop = (int) $context->shop->id;
        }

        $childrens = Db::getInstance()->executeS($this->getQuery($id_category, $id_lang, $id_shop, 'c.`id_parent` = '.(int) $id_category));

        return $childrens;
    }
}
