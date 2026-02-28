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

class NewsletterProSubscriptionConsent extends ObjectModel
{
    const ENCRYPT_CONSENT = true;

    public $email;

    public $subscribed;

    public $ip_address;

    public $url;

    public $http_referer;

    public $consent_date;

    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => 'newsletter_pro_subscription_consent',
        'primary' => 'id_newsletter_pro_subscription_consent',
        'fields' => [
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 64, 'required' => true],
            'subscribed' => ['type' => self::TYPE_BOOL, 'validate' => 'isInt', 'required' => true],
            'ip_address' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 64, 'required' => true],
            'url' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'http_referer' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'consent_date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function newInstance($id = null)
    {
        return new self($id);
    }

    public function set($email, $subscribed, $consent = true)
    {
        $this->email = $email;
        $this->subscribed = $subscribed;
        $this->ip_address = Tools::getRemoteAddr();
        $this->url = Tools::getShopDomain(true, true).$_SERVER['REQUEST_URI'];
        $this->http_referer = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '';
        $this->consent_date = null;

        if ($consent) {
            $this->consent_date = date('Y-m-d H:i:s');
        }

        return $this;
    }

    public static function deleteByEmail($email)
    {
        return Db::getInstance()->delete('newsletter_pro_subscription_consent', '`email` = "'.pSQL($email).'"');
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_subscription_consent', $email);

        try {
            $row = Db::getInstance()->getRow('
                SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_subscription_consent`
                WHERE `email` = "'.pSQL($email).'"
                ORDER BY `id_newsletter_pro_subscription_consent` DESC
            ');

            if ($row) {
                $response->addToExport([
                    NewsletterPro::getInstance()->l('Subscripton consent') => '',
                    NewsletterPro::getInstance()->l('Email') => $row['email'],
                    NewsletterPro::getInstance()->l('Subscribed') => $row['subscribed'],
                    NewsletterPro::getInstance()->l('IP address') => $row['ip_address'],
                    NewsletterPro::getInstance()->l('Date') => $row['date_add'],
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_subscription_consent', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscription_consent`
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_subscription_consent', $email);

        try {
            if (self::ENCRYPT_CONSENT) {
                $results = Db::getInstance()->executeS('
                    SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_subscription_consent`
                    WHERE `email` = "'.pSQL($email).'"
                ');

                foreach ($results as $row) {
                    if (Db::getInstance()->update('newsletter_pro_subscription_consent', [
                        'email' => Tools::encrypt($email),
                        'ip_address' => '0.0.0.0',
                        'url' => pSQL(preg_replace('/'.preg_quote($email).'/', NewsletterProPrivacyData::$anonymous_email, $row['url'])),
                        'http_referer' => pSQL(preg_replace('/'.preg_quote($email).'/', NewsletterProPrivacyData::$anonymous_email, $row['http_referer'])),
                    ], '`email` = "'.pSQL($email).'"')) {
                        $response->addToCount(Db::getInstance()->Affected_Rows());
                    }
                }
            } else {
                if (Db::getInstance()->delete('newsletter_pro_subscription_consent', '`email` = "'.pSQL($email).'" OR `email` = "'.pSQL(Tools::encyrpt($email)).'"')) {
                    $response->addToCount(Db::getInstance()->Affected_Rows());
                }
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
