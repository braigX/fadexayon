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

namespace PQNP\Upgrade;

if (!defined('_PS_VERSION_')) {
	exit;
}

use Configuration;
use Db;
use NewsletterProConfigurationShop;
use NewsletterProTools;
use NewsletterProTranslate;
use NewsletterProUpgrade;
use PQNP\Config;

class Upgrade500
{
    public function __construct()
    {
    }

    public function call()
    {
        $translate = new NewsletterProTranslate(pathinfo(__FILE__, PATHINFO_FILENAME));

        $configuration = Configuration::get(Config::NAME);

        // this is a serialization
        if (preg_match('/^a:(\d+):/', $configuration, $match)) {
            $data = NewsletterProTools::unSerialize($configuration);
            if ((int) $match[1] >= 0 && (int) $match[1] < Config::DECODE_ERROR_LIMIT) {
                $data = Config::defaultConfig();
            }
            $data['SHOW_CLEAR_CACHE'] = 1;
            NewsletterProUpgrade::showWarningMessage([
                 $translate->l('The configuration have been reseted.'),
            ]);

            Configuration::updateValue(Config::NAME, json_encode($data), false, 0, 0);
        }

        $rows = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'configuration`
            WHERE `name` = "'.NewsletterProConfigurationShop::$name.'"
        ');

        foreach ($rows as $row) {
            if (preg_match('/^a:\d+:/', $row['value'])) {
                Db::getInstance()->update('configuration', [
                    'value' => json_encode(NewsletterProTools::unSerialize($row['value'])),
                ], '`id_configuration` = '.(int) $row['id_configuration'], 1);
            }
        }
    }
}
