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

class NewsletterProFwdUnsubscribed extends ObjectModel
{
    public $id_newsletter_pro_tpl_history;

    public $email;

    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => 'newsletter_pro_fwd_unsibscribed',
        'primary' => 'id_newsletter_pro_fwd_unsibscribed',
        'fields' => [
            'id_newsletter_pro_tpl_history' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255, 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_fwd_unsibscribed', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_fwd_unsibscribed` WHERE `email` = "'.pSQL($email).'"
            ');

            if ($count > 0) {
                $response->addToExport([
                    NewsletterPro::getInstance()->l('Forward unsubscribed') => '',
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_fwd_unsibscribed', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_fwd_unsibscribed` WHERE `email` = "'.pSQL($email).'"
            ');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function clearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_fwd_unsibscribed', $email);

        try {
            if (Db::getInstance()->delete('newsletter_pro_fwd_unsibscribed', '`email` = "'.pSQL($email).'"')) {
                $response->addToCount(Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
