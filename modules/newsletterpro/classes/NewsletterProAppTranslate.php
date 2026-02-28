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

class NewsletterProAppTranslate
{
    public static function get($entry)
    {
        $translate = new NewsletterProTranslate(__CLASS__);

        switch ($entry) {
            case 'app':
                return [
                    'global' => [
                    ],
                    'CategoryTree' => [
                        'Expand All' => $translate->l('Expand All'),
                        'Check All' => $translate->l('Check All'),
                        'Uncheck All' => $translate->l('Uncheck All'),
                        'Collapse All' => $translate->l('Collapse All'),
                    ],
                    'AbandonedCartFilter' => [
                        'Date range' => $translate->l('Date range'),
                        'Start' => $translate->l('Start'),
                        'End' => $translate->l('End'),
                        'Clear' => $translate->l('Clear'),
                        'With products from category' => $translate->l('With products from category'),
                    ],
                    'CategoryTreeSearch' => [
                        'search...' => $translate->l('search...'),
                    ],
                ];

            case 'app_front':
                return [
                    'global' => [
                    ],
                    'ajax' => [
                        'Oops, an error has occurred.' => $translate->l('Oops, an error has occurred.'),
                        'Error: The AJAX response is not JSON type.' => $translate->l('Error: The AJAX response is not JSON type.'),
                    ],
                    'popup' => [
                        'Oops, an error has occurred.' => $translate->l('Oops, an error has occurred.'),
                        'close in %s seconds' => $translate->l('close in %s seconds'),
                    ],
                ];
        }

        throw new Exception(sprintf('The entry "%s" is not defined.', $entry));
    }
}
