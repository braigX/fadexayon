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

class NewsletterProSendStep extends ObjectModel
{
    public $id_newsletter_pro_send;

    public $id_newsletter_pro_send_connection;

    public $step;

    public $step_active;

    public $emails_to_send;

    public $emails_sent;

    public $error_msg;

    public $date;

    public $date_modified;

    /**
     * Constants.
     */
    const ERROR_NO_USER = 100;

    const ERROR_TEMPLATE = 101;

    const ERROR_SMTP = 102;

    const ERROR_EXCEPTION = 103;

    /**
     * Variables.
     */
    public $emails_to_send_unserialized;

    public $emails_sent_unserialized;

    public $connection;

    public $process;

    // public $process_emails = array();

    // private $process_send = array();

    // private $process_sent = array();

    public static $definition = [
        'table' => 'newsletter_pro_send_step',
        'primary' => 'id_newsletter_pro_send_step',
        'fields' => [
            'id_newsletter_pro_send' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_newsletter_pro_send_connection' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'step' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'step_active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'emails_to_send' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'emails_sent' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'error_msg' => ['type' => self::TYPE_HTML, 'validate' => 'isString'],
            'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_modified' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public static function newInstance($id = null)
    {
        return new self($id);
    }

    public function __construct($id = null)
    {
        // set defaults values
        $this->emails_to_send = serialize([]);
        $this->emails_sent = serialize([]);
        $this->date_modified = date('Y-m-d H:i:s');

        $this->error_msg = serialize([]);

        parent::__construct($id);

        $this->initConnection();
        $this->initEmailsToSend();
        $this->initEmailsSent();
    }

    private function initConnection()
    {
        if ((int) $this->id_newsletter_pro_send_connection) {
            $this->connection = NewsletterProSendConnection::newInstance($this->id_newsletter_pro_send_connection);
            if (!Validate::isLoadedObject($this->connection)) {
                $this->connection = null;
            }
        }
    }

    private function initEmailsToSend()
    {
        if (!isset($this->emails_to_send_unserialized)) {
            $this->emails_to_send_unserialized = NewsletterProTools::unSerialize($this->emails_to_send);
        }
    }

    private function initEmailsSent()
    {
        if (!isset($this->emails_sent_unserialized)) {
            $this->emails_sent_unserialized = NewsletterProTools::unSerialize($this->emails_sent);
        }
    }

    public function hasProcess()
    {
        return isset($this->process);
    }
    /*
        public function initProcess($limit = null)
        {

            try {
                $limit = !isset($limit) ? 100 : $limit;

                $table_name = '`'._DB_PREFIX_.self::$definition['table'].'`';
                $primary_key = '`'.self::$definition['primary'].'`';
                $db = Db::getInstance()->connect();
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->exec('LOCK TABLES '.$table_name.' WRITE');
                $db->beginTransaction();

                $query = $db->query('
                    SELECT *
                    FROM '.$table_name.'
                    WHERE '.$primary_key.' = '.(int)$this->id.'
                    FOR UPDATE
                ');

                $result = $query->fetch(PDO::FETCH_ASSOC);

                $emails_to_send = NewsletterProTools::unSerialize($result['emails_to_send']);
                $count = count($emails_to_send);
                $emails = array();
                $limit = $count <= $limit ? $count : $limit;

                for ($i = 0; $i < $limit; $i++)
                    $emails[] = array_shift($emails_to_send);

                $emails_to_send_serialized = serialize($emails_to_send);

                $db->exec('
                    UPDATE '.$table_name.'
                    SET `emails_to_send` = "' . pSQL($emails_to_send_serialized) . '"
                    WHERE '.$primary_key.' = '.(int)$this->id.'
                ');

                // $this = NewsletterProSendStep::newInstance();
                // $this->id = (int)$result['id_newsletter_pro_send_step'];
                // $this->id_newsletter_pro_send = (int)$result['id_newsletter_pro_send'];
                // $this->step = (int)$result['step'];
                $this->step_active = (int)$result['step_active'];
                $this->emails_to_send = $emails_to_send_serialized;
                $this->emails_to_send_unserialized = $emails_to_send;
                $this->emails_sent = $result['emails_sent'];
                $this->error_msg = $result['error_msg'];
                $this->date = $result['date'];
                $this->date_modified = $result['date_modified'];


                $this->process = NewsletterProSendProcess::newInstance($emails);

                 // $this->process_emails = $emails;

                $this->initConnection();
                $this->initEmailsSent();

                $db->commit();
                $db->exec('UNLOCK TABLES');

            } catch (Exception $e) {
                Db::getInstance()->execute('UNLOCK TABLES');
                throw $e;
            }
        }
    */

    public function initProcess($limit = null)
    {
        try {
            $limit = !isset($limit) ? 100 : $limit;

            $table_name = '`'._DB_PREFIX_.self::$definition['table'].'`';
            $primary_key = '`'.self::$definition['primary'].'`';

            $transaction = NewsletterProDbTransaction::newInstance();

            $transaction->exec('LOCK TABLES '.$table_name.' WRITE');
            $transaction->begin();

            $result = $transaction->query('
				SELECT *
				FROM '.$table_name.'
				WHERE '.$primary_key.' = '.(int) $this->id.'
				FOR UPDATE
			');

            $emails_to_send = NewsletterProTools::unSerialize($result['emails_to_send']);
            $count = count($emails_to_send);
            $emails = [];
            $limit = $count <= $limit ? $count : $limit;

            for ($i = 0; $i < $limit; ++$i) {
                $emails[] = array_shift($emails_to_send);
            }

            $emails_to_send_serialized = serialize($emails_to_send);

            $transaction->exec('
				UPDATE '.$table_name.'
				SET `emails_to_send` = "'.pSQL($emails_to_send_serialized).'"
				WHERE '.$primary_key.' = '.(int) $this->id.'
			');

            $this->step_active = (int) $result['step_active'];
            $this->emails_to_send = $emails_to_send_serialized;
            $this->emails_to_send_unserialized = $emails_to_send;
            $this->emails_sent = $result['emails_sent'];
            $this->error_msg = $result['error_msg'];
            $this->date = $result['date'];
            $this->date_modified = $result['date_modified'];

            $this->process = NewsletterProSendProcess::newInstance($emails);

            $this->initConnection();
            $this->initEmailsSent();

            $transaction->commit();
            $transaction->exec('UNLOCK TABLES');
        } catch (Exception $e) {
            Db::getInstance()->execute('UNLOCK TABLES');
            throw $e;
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->emails_to_send = serialize($this->emails_to_send_unserialized);
        $this->emails_sent = serialize($this->emails_sent_unserialized);

        $this->date_modified = date('Y-m-d H:i:s');
        $this->date = date('Y-m-d H:i:s');

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->emails_to_send = serialize($this->emails_to_send_unserialized);
        $this->emails_sent = serialize($this->emails_sent_unserialized);

        $this->date_modified = date('Y-m-d H:i:s');

        return parent::update($null_values);
    }

    public function updateTransaction($write_to_send = false)
    {
        try {
            $table_name = '`'._DB_PREFIX_.self::$definition['table'].'`';
            $primary_key = '`'.self::$definition['primary'].'`';

            $transaction = NewsletterProDbTransaction::newInstance();

            $transaction->exec('LOCK TABLES '.$table_name.' WRITE');
            $transaction->begin();

            $result = $transaction->query('
				SELECT * FROM '.$table_name.'
				WHERE '.$primary_key.' = '.(int) $this->id.' FOR UPDATE;
			');

            $emails_to_send = NewsletterProTools::unSerialize($result['emails_to_send']);
            $emails_sent = NewsletterProTools::unSerialize($result['emails_sent']);

            if ($this->hasProcess()) {
                foreach ($this->process->emails_to_send as $email) {
                    array_unshift($emails_to_send, $email);
                }

                foreach ($this->process->emails_sent as $data) {
                    $emails_sent[] = $data;
                }

                $this->process->emptySent();
            }

            $count = count($emails_to_send);
            $emails_to_send_serialized = serialize($emails_to_send);
            $emails_sent_serialized = serialize($emails_sent);

            $this->step_active = $count > 0 ? 1 : 0;
            $this->emails_to_send = $emails_to_send_serialized;
            $this->emails_sent = $emails_sent_serialized;
            $this->emails_to_send_unserialized = $emails_to_send;
            $this->emails_sent_unserialized = $emails_sent;
            $this->date_modified = date('Y-m-d H:i:s');

            $transaction->exec('
				UPDATE '.$table_name.'
				SET
					`step_active` = '.(int) $this->step_active.',
					'.($write_to_send ? '`emails_to_send` = "'.pSQL($this->emails_to_send).'",' : '').'
					`emails_sent` = "'.pSQL($this->emails_sent).'",
					`date_modified` = "'.pSQL($this->date_modified).'"
				WHERE '.$primary_key.' = '.(int) $this->id.'
				LIMIT 1
			');

            $transaction->commit();
            $transaction->exec('UNLOCK TABLES');
        } catch (Exception $e) {
            Db::getInstance()->execute('UNLOCK TABLES');
            throw $e;
        }
    }

    public function setEmailsToSend($value)
    {
        $this->emails_to_send_unserialized = $value;
        $this->emails_to_send = serialize($value);
    }

    public function getEmailsToSend($limit = 0)
    {
        if ($limit) {
            return array_slice($this->emails_to_send_unserialized, 0, 10);
        }

        return $this->emails_to_send_unserialized;
    }

    public function getEmailsToSendDb()
    {
        $result = Db::getInstance()->getValue('
			SELECT `emails_to_send` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step` 
			WHERE `id_newsletter_pro_send_step` = '.(int) $this->id.'
		');

        if (!$result) {
            $result = serialize([]);
        }

        return NewsletterProTools::unSerialize($result);
    }

    public function getEmailsSent($limit = 0, $reverse = false)
    {
        if ($reverse) {
            $result = array_reverse($this->emails_sent_unserialized);
        } else {
            $result = $this->emails_sent_unserialized;
        }

        if ($limit) {
            return array_slice($result, 0, 10);
        }

        return $result;
    }

    public function getEmailsSentDb()
    {
        $result = Db::getInstance()->getValue('
			SELECT `emails_sent` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step` 
			WHERE `id_newsletter_pro_send_step` = '.(int) $this->id.'
		');

        if (!$result) {
            $result = serialize([]);
        }

        return NewsletterProTools::unSerialize($result);
    }

    public function setEmailsSent($value)
    {
        $this->emails_sent_unserialized = $value;
        $this->emails_sent = serialize($value);
    }

    public function getErrorMsg()
    {
        return NewsletterProTools::unSerialize($this->error_msg);
    }

    /**
     * Add error to database.
     *
     * @param string $email
     * @param  array/string $errors_array
     * @param int $code
     *
     * @return bool
     */
    public function appendError($email, $errors_array, $code, $write_db_limit = true)
    {
        $error_msg_db = NewsletterProTools::unSerialize($this->error_msg);

        $errors_join = is_array($errors_array) ? join('<br>', $errors_array) : $errors_array;

        if (!isset($error_msg_db[$code])) {
            $error_msg_db[$code] = [];
        }

        $error_msg_db[$code][$errors_join][] = $email;
        $error_msg_db[$code][$errors_join] = array_unique($error_msg_db[$code][$errors_join]);
        $this->error_msg = serialize($error_msg_db);

        if ($write_db_limit) {
            return $this->updateFields([
                'error_msg' => serialize($error_msg_db),
            ]);
        }
    }

    public function updateFields($fields = [], $override_values = true)
    {
        if ($override_values) {
            foreach ($fields as $field => $value) {
                $this->{$field} = $value;
            }
        }

        $fields['date_modified'] = date('Y-m-d H:i:s');

        return Db::getInstance()->update('newsletter_pro_send_step', $fields, '`id_newsletter_pro_send_step` = '.(int) $this->id);
    }

    public function hasConnection()
    {
        return isset($this->connection);
    }

    public function shutdown()
    {
        NewsletterProShutdown::register([$this, 'registerShutdown']);

        return $this;
    }

    public function registerShutdown()
    {
        $this->step_active = (count($this->getEmailsToSendDb()) > 0 ? 1 : 0);

        $this->updateTransaction(true);

        // $this->update();
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_send_step', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_send_step`
				WHERE `emails_sent` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
			');

            if ($count > 0) {
                $response->addToExport([
                    NewsletterPro::getInstance()->l('Newsletter received') => '',
                    NewsletterPro::getInstance()->l('Total send') => sprintf(NewsletterPro::getInstance()->l(sprintf('at least %s', $count))),
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_send_step', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_send_step`
				WHERE `error_msg` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
				OR `emails_to_send` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
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
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_send_step', $email);

        try {
            $results = Db::getInstance()->executeS('
				SELECT `id_newsletter_pro_send_step`, `emails_to_send`, `emails_sent`, `error_msg` FROM `'._DB_PREFIX_.'newsletter_pro_send_step`
				WHERE `error_msg` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
				OR `emails_to_send` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
				OR `emails_sent` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
			');

            foreach ($results as $row) {
                $emails_to_send = NewsletterProTools::unSerialize($row['emails_to_send']);
                $emails_sent = NewsletterProTools::unSerialize($row['emails_sent']);
                $error_msg = NewsletterProTools::unSerialize($row['error_msg']);

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

                if (is_array($error_msg)) {
                    foreach ($error_msg as $key => &$value) {
                        if (is_array($value)) {
                            foreach ($value as $ke => &$valu) {
                                while (($index = array_search($email, $valu)) !== false) {
                                    unset($valu[$index]);
                                }
                                $valu = array_values($valu);
                                if (empty($valu)) {
                                    unset($error_msg[$key]);
                                }
                            }
                        }
                    }
                } else {
                    $error_msg = [];
                }

                if (Db::getInstance()->update('newsletter_pro_send_step', [
                    'emails_to_send' => pSQL(serialize($emails_to_send)),
                    'emails_sent' => pSQL(serialize($emails_sent)),
                    'error_msg' => pSQL(serialize($error_msg)),
                ], '`id_newsletter_pro_send_step` = '.(int) $row['id_newsletter_pro_send_step'].'', 1)) {
                    $response->addToCount(Db::getInstance()->Affected_Rows());
                }
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
