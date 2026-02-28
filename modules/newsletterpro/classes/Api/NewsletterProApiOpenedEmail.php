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

class NewsletterProApiOpenedEmail extends NewsletterProApi
{
    public function call()
    {
        $module = NewsletterProTools::module();

        if (!$this->request->has('token')) {
            exit('Invalid token');
        }

        $idTplHistory = (int) $module->getHistoryIdByToken($this->request->get('token'));
        $email = $this->request->get('email');

        if (0 == $idTplHistory) {
            exit('Invalid token');
        }

        if (!$this->request->has('email')) {
            exit('Invalid email address');
        }

        $openedEmail = new NewsletterProOpenedEmail();
        if (!$openedEmail->isValid($idTplHistory, $email)) {
            exit('Invalid token or email address');
        }

        if ($openedEmail->wasOpened()) {
            exit('You already opened the template.');
        }

        $openedEmail->update();

        return $this->output->render();
    }
}
