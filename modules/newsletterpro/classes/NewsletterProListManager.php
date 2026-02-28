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

abstract class NewsletterProListManager
{
    const TABLE_ALL = 0x0F;

    const TABLE_CUSTOMER = 0x01;

    const TABLE_NEWSLETTER = 0x02;

    const TABLE_EMAIL = 0x04;

    const TABLE_SUBSCRIBER = 0x08;

    public static $tables = [
        'emailsubscription' => [
            'flag' => self::TABLE_NEWSLETTER,
            'fields' => [
                'email' => 'email',
                'active' => 'active',
            ],
        ],
        'newsletter' => [
            'flag' => self::TABLE_NEWSLETTER,
            'fields' => [
                'email' => 'email',
                'active' => 'active',
            ],
        ],
        'newsletter_pro_subscribers' => [
            'flag' => self::TABLE_SUBSCRIBER,
            'fields' => [
                'email' => 'email',
                'active' => 'active',
            ],
        ],
        'newsletter_pro_email' => [
            'flag' => self::TABLE_EMAIL,
            'fields' => [
                'email' => 'email',
                'active' => 'active',
            ],
        ],
        'customer' => [
            'flag' => self::TABLE_CUSTOMER,
            'fields' => [
                'email' => 'email',
                'active' => 'newsletter',
            ],
        ],
    ];

    public static function parse($callback, $flags = null)
    {
        if (!isset($flags)) {
            $flags = self::TABLE_ALL;
        }

        $data = [];
        foreach (self::$tables as $table_name => $info) {
            if (NewsletterProTools::tableExists($table_name) && ($flags & $info['flag']) > 0) {
                $data[$table_name] = $callback($table_name, [
                    'email' => $info['fields']['email'],
                    'active' => $info['fields']['active'],
                ]);
            }
        }

        return $data;
    }
}
