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

class NewsletterProForward extends ObjectModel
{
    public $from;

    public $to;

    public $date_add;

    /* defined vars */

    public $errors = [];

    public $context;

    public $module;

    const FOREWORD_LIMIT = 5;

    public static $static_errors = [];

    public static $definition = [
        'table' => 'newsletter_pro_forward',
        'primary' => 'id_newsletter_pro_forward',
        'fields' => [
            'from' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true],
            'to' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->context = Context::getContext();
        $this->module = $this->context->controller->module;
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return array_unique($this->errors);
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->limitExceeded()) {
            $this->addError(sprintf($this->module->l('You cannot add more emails. You exceeded the limit of %s emails.'), self::FOREWORD_LIMIT));

            return false;
        }

        if ($this->isToDuplicate()) {
            $this->addError(sprintf($this->module->l('Your friend with the email %s is already subscribed at our newsletter.'), $this->to));

            return false;
        }

        if ($info = $this->getUserTableByEmail($this->to)) {
            $is_subscribed = (int) Db::getInstance()->getValue('SELECT `'.$info['newsletter'].'` FROM `'._DB_PREFIX_.$info['table'].'` WHERE `email` = "'.pSQL($this->to).'"');
            if ($is_subscribed) {
                $this->addError(sprintf($this->module->l('Your friend with the email %s is already subscribed at our newsletter.'), $this->to));

                return false;
            }
        }

        try {
            $return = parent::add($autodate, $null_values);

            if (!$return) {
                $this->addError(sprintf($this->module->l('An error occurred when adding the email %s into the database.'), $this->to));
            }

            return $return;
        } catch (Exception $e) {
            $this->addError(sprintf($this->module->l('An error occurred when adding the email %s into the database.'), $this->to));
        }

        return false;
    }

    public function limitExceeded()
    {
        $limit = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE `from` = "'.pSQL($this->from).'"');
        if ($limit >= self::FOREWORD_LIMIT) {
            return true;
        }

        return false;
    }

    public static function addMultiple($from, $emails)
    {
        foreach ($emails as $email) {
            $instance = new NewsletterProForward();
            $instance->from = $from;
            $instance->to = $email;
            $instance->add();
            if ($instance->hasErrors()) {
                self::$static_errors = array_merge(self::$static_errors, $instance->getErrors());
            }
        }
    }

    public static function getStaticErrors()
    {
        return array_unique(self::$static_errors);
    }

    public static function addStaticError($error)
    {
        self::$static_errors[] = $error;
    }

    public static function hasStaticErrors()
    {
        return !empty(self::$static_errors);
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function delete()
    {
        try {
            $result = parent::delete();
            if (!$result) {
                $this->addError(sprintf($this->module->l('An error occurred when deleting the email %s.'), $this->to));
            }

            return $result;
        } catch (Exception $e) {
            $this->addError(sprintf($this->module->l('An error occurred when deleting the email %s.'), $this->to));
        }

        return false;
    }

    public function isToDuplicate()
    {
        if (self::getInstanceByTo($this->to)) {
            return true;
        }

        return false;
    }

    public static function getInstanceByTo($to)
    {
        $id = (int) Db::getInstance()->getValue('SELECT `id_newsletter_pro_forward` FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE `to` = "'.pSQL($to).'"');
        if ($id) {
            return new NewsletterProForward($id);
        }

        return false;
    }

    public function getUserTableByEmail($email)
    {
        $definition = [
            'customer' => ['email' => 'email', 'newsletter' => 'newsletter'],
            'newsletter' => ['email' => 'email', 'newsletter' => 'active'],
            'emailsubscription' => ['email' => 'email', 'newsletter' => 'active'],
            'newsletter_pro_email' => ['email' => 'email', 'newsletter' => 'active'],
        ];

        foreach ($definition as $table => $fields) {
            if (NewsletterProTools::tableExists($table)) {
                $sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.$table.'` WHERE `'.$fields['email'].'` = "'.pSQL($email).'"';
                if (Db::getInstance()->getValue($sql)) {
                    return [
                    'table' => $table,
                    'email' => $fields['email'],
                    'newsletter' => $fields['newsletter'],
                    ];
                }
            }
        }

        return false;
    }

    public static function getEmailsToByEmailFrom($from)
    {
        if (is_array($from)) {
            reset($from);
            $from = key($from);
        }

        $emails = [];
        $result = Db::getInstance()->executeS('SELECT `to` FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE `from` = "'.pSQL($from).'"');

        if ($result) {
            foreach ($result as $email) {
                $emails[] = $email['to'];
            }
        }

        return $emails;
    }

    public static function getForwarders($from)
    {
        $emails = self::getEmailsToByEmailFrom($from);
        $emails_join = '"'.trim(join('","', $emails)).'"';
        $definition = [
            'customer' => ['email' => 'email', 'newsletter' => 'newsletter'],
            'newsletter_pro_email' => ['email' => 'email', 'newsletter' => 'active'],
            'newsletter_pro_subscribers' => ['email' => 'email', 'newsletter' => 'active'],
        ];

        if (NewsletterProTools::tableExists('newsletter')) {
            $definition['newsletter'] = ['email' => 'email', 'newsletter' => 'active'];
        }

        if (NewsletterProTools::tableExists('emailsubscription')) {
            $definition['emailsubscription'] = ['email' => 'email', 'newsletter' => 'active'];
        }

        $valid_emails = [];
        $delete_emails = [];
        if (!empty($emails)) {
            foreach ($definition as $table => $fields) {
                $sql = 'SELECT `'.$fields['email'].'` FROM `'._DB_PREFIX_.$table.'` WHERE `'.$fields['email'].'` IN ('.$emails_join.')';
                if ($result = Db::getInstance()->executeS($sql)) {
                    foreach ($result as $value) {
                        $delete_emails[] = $value['email'];
                    }
                }
            }
        }

        $delete_emails = array_unique($delete_emails);

        foreach ($delete_emails as $to) {
            Db::getInstance()->delete('newsletter_pro_forward', '`to` = "'.pSQL($to).'"', 1);
        }

        $valid_emails = array_diff($emails, $delete_emails);

        return $valid_emails;
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_forward', $email);

        try {
            $from_count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_forward`
				WHERE `from` = "'.pSQL($email).'"
			');

            if ($from_count > 0) {
                $response->addToExport([
                    NewsletterPro::getInstance()->l('Send forward') => '',
                    NewsletterPro::getInstance()->l('Total Emails') => $from_count,
                ]);
            }

            $to_count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_forward`
				WHERE `to` = "'.pSQL($email).'"
			');

            if ($to_count > 0) {
                $response->addToExport([
                    NewsletterPro::getInstance()->l('Receive forward') => '',
                    NewsletterPro::getInstance()->l('Total emails') => $to_count,
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_forward', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_forward`
				WHERE `from` = "'.pSQL($email).'"
				OR `to` = "'.pSQL($email).'"
			');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function clearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_forward', $email);

        try {
            if (Db::getInstance()->delete('newsletter_pro_forward', '`from` = "'.pSQL($email).'" OR `to` = "'.pSQL($email).'"')) {
                $response->addToCount(Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
