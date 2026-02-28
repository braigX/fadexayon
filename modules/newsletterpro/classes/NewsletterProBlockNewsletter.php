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

class NewsletterProBlockNewsletter extends ObjectModel
{
    public $id_shop;

    public $id_shop_group;

    public $email;

    public $ip_registration_newsletter;

    public $http_referer;

    public $active;

    public static $definition = [
        'table' => 'newsletter',
        'primary' => 'id',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true],
            'ip_registration_newsletter' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'http_referer' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    public function __construct($id = null)
    {
        self::$definition['table'] = NewsletterProDefaultNewsletterTable::getTableName();
        parent::__construct($id);
    }

    public function newInstance($id = null)
    {
        return new self($id);
    }
}
