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

class NewsletterProBounce
{
    /**
     * Bounce email from the four list.
     *
     * @param string $email
     * @param array  $lists  [customers, visitors, visitors_np, added]
     * @param int    $method
     *
     * @return bool
     */
    public static function execute($email, $lists = [], $method = -1)
    {
        $module = NewsletterProTools::module();

        $intersect = array_intersect($lists, ['customers', 'visitors', 'visitors_np', 'added']);

        if (!empty($lists) && empty($intersect)) {
            throw new InvalidArgumentException(sprintf('Invalid parameter %s values (%s).', '$lists', implode(', ', $lists)));
        }

        $success = [];

        if (empty($lists) || in_array('visitors', $lists)) {
            $tableName = NewsletterProDefaultNewsletterTable::getTableName();

            if (NewsletterProTools::tableExists($tableName)) {
                $id_visitor = (int) Db::getInstance()->getValue('
					SELECT `id` FROM `'._DB_PREFIX_.$tableName.'` WHERE `email` = "'.pSQL($email).'"
				');

                if ($id_visitor) {
                    if (-1 == $method) {
                        $success[] = $module->deleteVisitor($id_visitor);
                    } else {
                        $success[] = Db::getInstance()->update('newsletter', [
                            'active' => 0,
                        ], '`id` = '.(int) $id_visitor);
                    }
                }
            }
        }

        if (empty($lists) || in_array('customers', $lists)) {
            $id_customer = (int) Db::getInstance()->getValue('SELECT `id_customer` FROM `'._DB_PREFIX_.'customer` WHERE `email` = "'.pSQL($email).'"');

            if ($id_customer) {
                if (-1 == $method) {
                    $success[] = $module->deleteCustomer($id_customer);
                } else {
                    $success[] = Db::getInstance()->update('customer', [
                        'newsletter' => 0,
                    ], '`id_customer` = '.(int) $id_customer);
                }
            }
        }

        if (empty($lists) || in_array('added', $lists)) {
            $id_added = (int) Db::getInstance()->getValue('SELECT `id_newsletter_pro_email` FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE `email` = "'.pSQL($email).'"');

            if ($id_added) {
                if (-1 == $method) {
                    $success[] = $module->deleteAdded($id_added);
                } else {
                    $success[] = Db::getInstance()->update('newsletter_pro_email', [
                        'active' => 0,
                    ], '`id_newsletter_pro_email` = '.(int) $id_added);
                }
            }
        }

        if (empty($lists) || in_array('visitors_np', $lists)) {
            $id_visitor_np = (int) Db::getInstance()->getValue('SELECT `id_newsletter_pro_subscribers` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE `email` = "'.pSQL($email).'"');

            if ($id_visitor_np) {
                if (-1 == $method) {
                    $success[] = $module->deleteVisitorNP($id_visitor_np);
                } else {
                    $success[] = Db::getInstance()->update('newsletter_pro_subscribers', [
                        'active' => 0,
                    ], '`id_newsletter_pro_subscribers` = '.(int) $id_visitor_np);
                }
            }
        }

        return !empty($success) ? !in_array(false, $success) : false;
    }
}
