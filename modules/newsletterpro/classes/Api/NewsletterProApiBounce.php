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

class NewsletterProApiBounce extends NewsletterProApi
{
    public function call()
    {
        $email = trim($this->request->get('email'));

        if (!$email) {
            exit($this->l('The email address field is empty.'));
        }

        $module = NewsletterProTools::module();

        $bouceAction = $this->request->get('bounceAction');

        $bouceActionActions = ['delete', 'unsubscribe'];

        if (!in_array($bouceAction, $bouceActionActions)) {
            exit($this->l(sprintf('The bounceAction should be [%s].', join(', ', $bouceActionActions))));
        }

        $bouceAction = (('delete' != $bouceAction && 'unsubscribe' != $bouceAction) ? 'delete' : $bouceAction);
        $bouceMethod = 'delete' == $bouceAction ? (int) ('-1') : 0;
        $actionMsg = (-1 == $bouceMethod ? $this->l('removed') : $this->l('unsubscribed'));

        if (NewsletterProBounce::execute($email, [], $bouceMethod)) {
            exit($this->l(sprintf('The bounced email %s has been %s from the database.', $email, $actionMsg)));
        } else {
            exit($this->l(sprintf('The bounced email %s has not been %s from the database. Maybe the email does not exists into database.', $email, $actionMsg)));
        }

        return $this->output->render();
    }
}
