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

class NewsletterProTaskStep extends ObjectModel
{
    public $id_newsletter_pro_task;

    public $step;

    public $step_active;

    public $emails_to_send;

    public $emails_to_send_unserialized;

    public $emails_sent;

    public $emails_sent_unserialized;

    public $date;

    public static $definition = [
        'table' => 'newsletter_pro_task_step',
        'primary' => 'id_newsletter_pro_task_step',
        'fields' => [
            'id_newsletter_pro_task' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'step' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'step_active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'emails_to_send' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'emails_sent' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public function __construct($id = null)
    {
        // set defaults values
        $this->emails_to_send = serialize([]);
        $this->emails_sent = serialize([]);

        parent::__construct($id);

        if (!isset($this->emails_to_send_unserialized)) {
            $this->emails_to_send_unserialized = NewsletterProTools::unSerialize($this->emails_to_send);
        }

        if (!isset($this->emails_sent_unserialized)) {
            $this->emails_sent_unserialized = NewsletterProTools::unSerialize($this->emails_sent);
        }
    }

    public static function newInstance($id = null)
    {
        return new self($id);
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->emails_to_send = serialize($this->emails_to_send_unserialized);
        $this->emails_sent = serialize($this->emails_sent_unserialized);

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->emails_to_send = serialize($this->emails_to_send_unserialized);
        $this->emails_sent = serialize($this->emails_sent_unserialized);

        return parent::update($null_values);
    }

    public function updateFields($fields = [], $override_values = true)
    {
        if ($override_values) {
            foreach ($fields as $field => $value) {
                $this->{$field} = $value;
            }
        }

        return Db::getInstance()->update('newsletter_pro_task_step', $fields, '`id_newsletter_pro_task_step` = '.(int) $this->id);
    }

    public function delete()
    {
        return parent::delete();
    }

    public function setEmailsToSend($value)
    {
        $this->emails_to_send_unserialized = $value;
        $this->emails_to_send = serialize($value);
    }

    public function getEmailsToSend()
    {
        return $this->emails_to_send_unserialized;
    }

    public function setEmailsSent($value)
    {
        $this->emails_sent_unserialized = $value;
        $this->emails_sent = serialize($value);
    }

    public function getEmailsSent()
    {
        return $this->emails_sent_unserialized;
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_task_step', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_task_step`
				WHERE `emails_sent` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
			');

            if ($count > 0) {
                $response->addToExport([
                    NewsletterPro::getInstance()->l('Newsletter received') => '',
                    NewsletterPro::getInstance()->l('Total task send') => sprintf(NewsletterPro::getInstance()->l(sprintf('at least %s', $count))),
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_task_step', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_task_step`
				WHERE `emails_to_send` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
				OR `emails_sent` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
			');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function clearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_task_step', $email);

        try {
            $results = Db::getInstance()->executeS('
				SELECT `id_newsletter_pro_task_step`, `emails_to_send`, `emails_sent` FROM `'._DB_PREFIX_.'newsletter_pro_task_step`
				WHERE `emails_to_send` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
				OR `emails_sent` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
			');

            foreach ($results as $row) {
                $emails_to_send = NewsletterProTools::unSerialize($row['emails_to_send']);
                $emails_sent = NewsletterProTools::unSerialize($row['emails_sent']);

                if (is_array($emails_to_send)) {
                    while (($index = array_search($email, $emails_to_send)) !== false) {
                        unset($emails_to_send[$index]);
                    }
                    $emails_to_send = array_values($emails_to_send);
                } else {
                    $emails_to_send = [];
                }

                if (is_array($emails_sent)) {
                    foreach ($emails_sent as $key => $value) {
                        if (array_key_exists('email', $value) && trim($value['email']) === trim($email)) {
                            unset($emails_sent[$key]);
                        }
                    }
                    $emails_sent = array_values($emails_sent);
                } else {
                    $emails_sent = [];
                }

                if (Db::getInstance()->update('newsletter_pro_task_step', [
                    'emails_to_send' => pSQL(serialize($emails_to_send)),
                    'emails_sent' => pSQL(serialize($emails_sent)),
                ], '`id_newsletter_pro_task_step` = '.(int) $row['id_newsletter_pro_task_step'].'', 1)) {
                    $response->addToCount(Db::getInstance()->Affected_Rows());
                }
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
