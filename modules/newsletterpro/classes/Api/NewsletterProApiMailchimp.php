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

class NewsletterProApiMailchimp extends NewsletterProApi
{
    public function call()
    {
        try {
            $process = NewsletterProMailChimpWebhooks::newInstance()->process();
            exit($process);
        } catch (Exception $e) {
            NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
            exit($e->getMessage());
        }

        return $this->output->render();
    }
}
