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

class NewsletterProSubscriptionManager
{
    public static function newInstance()
    {
        return new self();
    }

    public function unsubscribe($email, $id_shop = null, $consent = null, $flags = null)
    {
        $result = NewsletterProListManager::parse(function ($table_name, $fields) use ($email, $id_shop) {
            // for prestashop 1.5 the active value must be 0 not false
            Db::getInstance()->update($table_name, [
                $fields['active'] => 0,
            ], '`'.pSQL($fields['email']).'` = "'.pSQL($email).'" '.(isset($id_shop) ? (' AND `id_shop` = '.(int) $id_shop) : '').' ');

            return (bool) Db::getInstance()->Affected_Rows();
        }, $flags);

        $forward = NewsletterProForward::getInstanceByTo($email);

        if (Validate::isLoadedObject($forward)) {
            $result['newsletter_pro_forward'] = (bool) $forward->delete();
        }

        if (isset($consent)) {
            if (is_bool($consent) && $consent) {
                NewsletterProSubscriptionConsent::newInstance()->set($email, false)->add();
            } elseif ($consent instanceof NewsletterProSubscriptionConsent) {
                $consent->add();
            }
        }

        return $result;
    }

    public function subscribe($email, $id_shop = null, $consent = null, $flags = null)
    {
        $result = NewsletterProListManager::parse(function ($table_name, $fields) use ($email, $id_shop) {
            Db::getInstance()->update($table_name, [
                $fields['active'] => 1,
            ], '`'.pSQL($fields['email']).'` = "'.pSQL($email).'" '.(isset($id_shop) ? (' AND `id_shop` = '.(int) $id_shop) : '').' ');

            return (bool) Db::getInstance()->Affected_Rows();
        }, $flags);

        if (isset($consent)) {
            if (is_bool($consent) && $consent) {
                NewsletterProSubscriptionConsent::newInstance()->set($email, true)->add();
            } elseif ($consent instanceof NewsletterProSubscriptionConsent) {
                $consent->add();
            }
        }

        return $result;
    }
}
