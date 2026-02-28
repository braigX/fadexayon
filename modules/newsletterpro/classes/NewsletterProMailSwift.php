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

class NewsletterProMailSwift extends ObjectModel
{
    /* database variables */
    public $method;

    public $name;

    public $from_name;

    public $from_email;

    public $reply_to;

    public $domain;

    public $server;

    public $user;

    public $passwd;

    public $encryption;

    public $port;

    public $list_unsubscribe_active;

    public $list_unsubscribe_email;

    /* defined variables */

    public $context;

    public $errors = [];

    public $fwd_success_emails = [];

    const METHOD_MAIL = 1;

    const METHOD_SMTP = 2;

    public static $definition = [
        'table' => 'newsletter_pro_smtp',
        'primary' => 'id_newsletter_pro_smtp',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'from_email' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'from_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'reply_to' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'domain' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'server' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'user' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'passwd' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'encryption' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'port' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'method' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'list_unsubscribe_active' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'list_unsubscribe_email' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];

    public static function newInstance($id = null)
    {
        return new NewsletterProMail($id);
    }

    public function setFromName($name)
    {
        $this->from_name = $name;
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

    public function addSuccessFwd($email)
    {
        $this->fwd_success_emails[] = $email;
    }

    public function getSuccessFwdCount()
    {
        return count($this->fwd_success_emails);
    }

    public function getTemplate($email, $id_history, $type)
    {
        $template = false;

        switch ($type) {
            case 'history':
                $template = NewsletterProTemplate::newHistory((int) $id_history, $email)->load();
                break;
        }

        return $template;
    }

    /**
     * Get an instance of the class.
     *
     * @param array $smtp Define de SMTP connection
     *
     * @return array SMTP connection
     */
    public static function getInstance($connection = [])
    {
        $shop_email = Configuration::get('PS_SHOP_EMAIL');

        $mail = NewsletterProMail::newInstance();
        $mail->name = isset($connection['name']) ? $connection['name'] : uniqid();
        $mail->from_name = isset($connection['from_name']) ? $connection['from_name'] : (string) $mail->context->shop->name;
        $mail->from_email = isset($connection['from_email']) ? $connection['from_email'] : $shop_email;
        $mail->reply_to = isset($connection['from_email']) ? $connection['from_email'] : $shop_email;
        $mail->domain = isset($connection['domain']) ? $connection['domain'] : '';
        $mail->server = isset($connection['server']) ? $connection['server'] : '';
        $mail->user = isset($connection['user']) ? $connection['user'] : '';
        $mail->passwd = isset($connection['passwd']) ? $connection['passwd'] : '';
        $mail->encryption = isset($connection['encryption']) ? $connection['encryption'] : $mail->encryption;
        $mail->port = isset($connection['port']) ? $connection['port'] : 'default';
        $mail->method = isset($connection['method']) ? $connection['method'] : self::METHOD_MAIL;
        $mail->list_unsubscribe_active = isset($connection['list_unsubscribe_active']) ? $connection['list_unsubscribe_active'] : 0;
        $mail->list_unsubscribe_email = isset($connection['list_unsubscribe_email']) ? $connection['list_unsubscribe_email'] : '';

        return $mail;
    }

    /**
     * Get the prestashp default SMTP connection.
     *
     * @param array $smtp Override the default SMTP values
     *
     * @return array/boolean  SMTP connection or false
     */
    public static function getDefaultSMTP()
    {
        $context = Context::getContext();
        $connection = Configuration::getMultiple([
            'PS_SHOP_EMAIL',
            'PS_MAIL_SERVER',
            'PS_MAIL_USER',
            'PS_MAIL_PASSWD',
            'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_SMTP_PORT',
            'PS_MAIL_DOMAIN',
        ]);

        if ($connection) {
            return [
                'from_name' => (string) $context->shop->name,
                'from_email' => $connection['PS_SHOP_EMAIL'],
                'reply_to' => $connection['PS_SHOP_EMAIL'],
                'domain' => $connection['PS_MAIL_DOMAIN'],
                'server' => $connection['PS_MAIL_SERVER'],
                'user' => $connection['PS_MAIL_USER'],
                'passwd' => $connection['PS_MAIL_PASSWD'],
                'encryption' => $connection['PS_MAIL_SMTP_ENCRYPTION'],
                'port' => $connection['PS_MAIL_SMTP_PORT'],
                'method' => self::METHOD_SMTP,
                'list_unsubscribe_active' => 0,
                'list_unsubscribe_email' => '',
            ];
        }

        return false;
    }

    /**
     * Get the default mail() connection.
     *
     * @return array/boolean Mail connection or false
     */
    public static function getDefaultMail()
    {
        $context = Context::getContext();
        $connection = Configuration::getMultiple([
            'PS_SHOP_EMAIL',
        ]);

        if ($connection) {
            return [
                'from_name' => (string) $context->shop->name,
                'from_email' => $connection['PS_SHOP_EMAIL'],
                'reply_to' => $connection['PS_SHOP_EMAIL'],
                'method' => self::METHOD_MAIL,
                'list_unsubscribe_active' => 0,
                'list_unsubscribe_email' => '',
            ];
        }

        return false;
    }

    /**
     * Get default connection.
     *
     * @return array/boolean Return the default connection of false
     */
    public static function getDefaultConnection()
    {
        $method = (int) Configuration::get('PS_MAIL_METHOD');

        if (self::METHOD_MAIL == $method) {
            return self::getDefaultMail();
        } elseif (self::METHOD_SMTP == $method) {
            return self::getDefaultSMTP();
        }

        return false;
    }

    /**
     * Get the active instance [SMTP, function mail(), or the default prestashop method].
     *
     * @return object/false return an instance or false
     */
    public static function getInstanceByContext()
    {
        if ((int) pqnp_config('SMTP_ACTIVE')) {
            if (!(int) pqnp_config('SMTP')) {
                throw new Exception(sprintf(NewsletterPro::getInstance()->l('You have actived the connection from the "%s" tab, but you forget to configure one.'), NewsletterPro::getInstance()->l('E-mail Configuration')));
            }

            $mail = NewsletterProMail::newInstance((int) pqnp_config('SMTP'));

            if (Validate::isLoadedObject($mail)) {
                return $mail;
            }
        } else {
            return NewsletterProMail::getInstance(self::getDefaultConnection());
        }

        return false;
    }

    public static function getAllMails()
    {
        return Db::getInstance()->executeS(
            'SELECT `id_newsletter_pro_smtp`, 
					`method`, 
					`name`, 
					`domain`, 
					`server`, 
					`user`, 
					`from_name`, 
					`from_email`, 
					`reply_to`, 
					`encryption`, 
					`port`,
					`list_unsubscribe_active`,
					`list_unsubscribe_email`,
			CASE WHEN `passwd` = 0 THEN "" ELSE "" END AS `passwd` 
			FROM `'._DB_PREFIX_.'newsletter_pro_smtp` 
			WHERE 1;'
        );
    }

    public function getEncryption()
    {
        $encryption = Tools::strtolower($this->encryption);
        if (!in_array($encryption, ['tls', 'ssl'])) {
            return false;
        }

        return $encryption;
    }
}
