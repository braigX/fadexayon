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

class NewsletterProPostman
{
    public function __construct($action)
    {
        switch ($action) {
            case 'test':
                exit($this->test());
                break;
            default:
                exit('Invalid Request');
                break;
        }
    }

    public function test()
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;

        $link = Context::getContext()->link->getModuleLink('newsletterpro', 'newslettersubscription', []);

        pqd('adfads', $link);

        echo 'test';
        exit;
    }
}
