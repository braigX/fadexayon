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

class NewsletterProTask extends ObjectModel
{
    public $id_newsletter_pro_smtp;

    public $id_newsletter_pro_tpl_history;

    public $date_start;

    public $date_modified;

    public $active;

    public $template;

    public $send_method;

    public $started;

    public $status;

    public $sleep;

    public $pause;

    public $emails_count;

    public $emails_error;

    public $emails_success;

    public $emails_completed;

    public $done;

    public $error_msg;

    /**
     * variables.
     */
    private $log;

    private $num_sent = 0;

    /**
     * This variable is used into the function send proccess.
     *
     * @var object
     */
    private $task_step;

    /**
     * 24000.
     */
    const MAX_EXECUTION_TIME = 24000;

    const STATUS_DEFAULT = 0;

    const WRITE_LOG = true;

    const STATUS_IN_PROGRESS = 1;

    public static $definition = [
        'table' => 'newsletter_pro_task',
        'primary' => 'id_newsletter_pro_task',
        'fields' => [
            'id_newsletter_pro_smtp' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_newsletter_pro_tpl_history' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'date_start' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_modified' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'template' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'send_method' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'started' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'status' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'sleep' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'pause' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'emails_count' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'emails_error' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'emails_success' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'emails_completed' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'done' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'error_msg' => ['type' => self::TYPE_HTML, 'validate' => 'isString'],
        ],
    ];

    public function __construct($id = null)
    {
        // set defaults values
        $this->active = 1;
        $this->error_msg = serialize([]);
        $this->log = [];

        $this->one_mb = 1048576;
        $this->memory_limit = (int) ini_get('memory_limit') * $this->one_mb;

        $current_mem = memory_get_usage(true);
        if ((int) ini_get('memory_limit') <= 0) {
            $this->memory_limit = $current_mem + $this->one_mb * 24 + 1;
        }

        parent::__construct($id);

        if (Validate::isLoadedObject($this)) {
            $this->log(sprintf(NewsletterPro::getInstance()->l('Task id : %s'), $this->id));
            $this->log(sprintf(NewsletterPro::getInstance()->l('Send method : %s'), $this->send_method));
            $this->log(sprintf(NewsletterPro::getInstance()->l('Total emails : %s'), $this->emails_count));
            $this->log(sprintf(NewsletterPro::getInstance()->l('Sent success : %s'), $this->emails_success));
            $this->log(sprintf(NewsletterPro::getInstance()->l('Sent errors : %s'), $this->emails_error));
            $this->log(sprintf(NewsletterPro::getInstance()->l('Sent completed : %s')."\n", $this->emails_completed));
        } else {
            $this->log(NewsletterPro::getInstance()->l('No task has been loaded.'));
        }
    }

    public static function newInstance($id = null)
    {
        return new self($id);
    }

    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->date_modified = date('Y-m-d H:i:s');

        return parent::update($null_values);
    }

    public function updateFields($fields = [])
    {
        $fields_default = [
            'date_modified' => pSQL(date('Y-m-d H:i:s')),
        ];

        return Db::getInstance()->update('newsletter_pro_task', array_merge($fields_default, $fields), '`id_newsletter_pro_task` = '.(int) $this->id);
    }

    public function delete()
    {
        $evaluate = NewsletterProEvaluate::newInstance();

        foreach ($this->getStepsIds() as $id) {
            $task_step = NewsletterProTaskStep::newInstance($id);
            $evaluate->add($task_step->delete());
        }

        return parent::delete() && $evaluate->success();
    }

    public function log($value)
    {
        $this->log[] = $value;
    }

    public function displayLog($separator = "\n")
    {
        echo '<pre>';
        echo join($separator, $this->log).$separator;
        echo '</pre>';

        return $this;
    }

    public function uniqueLog()
    {
        $this->log = array_unique($this->log);

        return $this;
    }

    public function emptyLog()
    {
        $this->log = [];

        return $this;
    }

    public function getStepsIds($step_active = false, $limit = 0)
    {
        $results = Db::getInstance()->executeS('
			SELECT `id_newsletter_pro_task_step` FROM `'._DB_PREFIX_.'newsletter_pro_task_step`
			WHERE `id_newsletter_pro_task` = '.(int) $this->id.'
			'.($step_active ? ' AND `step_active` = 1' : '').'
			ORDER BY `id_newsletter_pro_task_step`
			'.($limit > 0 ? ' LIMIT '.(int) $limit : '').'
		');

        return array_map([$this, 'getStepsIdsCallback'], $results);
    }

    private function getStepsIdsCallback($row)
    {
        return !empty($row) ? $row['id_newsletter_pro_task_step'] : false;
    }

    /**
     * @param DateTime|string|null $dateTime
     *
     * @return NewsletterProTask|false
     */
    public static function getTask($dateTime = null)
    {
        if (is_null($dateTime)) {
            $dateTime = new DateTime('now');
        } else {
            if (is_string($dateTime)) {
                $dateTime = new DateTime($dateTime);
            } elseif (!($dateTime instanceof DateTime)) {
                throw new Exception('Invalid task date.');
            }
        }

        $id = (int) Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_task` FROM `'._DB_PREFIX_.'newsletter_pro_task`
			WHERE `date_start` <= "'.pSQL($dateTime->format('Y-m-d H:i:s')).'"
			AND (
				DATE(`date_start`) = "'.pSQL($dateTime->format('Y-m-d')).'" OR
				`started` = 1
			)
			AND `active` = 1
			AND `done` = 0
			ORDER BY `date_start` ASC;
		');

        $task = new self($id);

        if (Validate::isLoadedObject($task)) {
            return $task;
        }

        return false;
    }

    /**
     * Sent the task.
     *
     * @return int Number of newsletters sent
     */
    public function send()
    {
        if (0 == (int) $this->started) {
            $this->started = 1;
            $this->update();
        }

        ignore_user_abort(true);
        set_time_limit(0);
        @ini_set('max_execution_time', self::MAX_EXECUTION_TIME);

        register_shutdown_function([$this, 'sendShutdownFunctionCallback']);

        $tlog = _NEWSLETTER_PRO_DIR_.'/logs/task.log';

        // update task status
        $this->status = self::STATUS_IN_PROGRESS;
        $this->update();

        $this->task_step = $this->getCurrentStep();

        if ($this->task_step) {
            $emails_to_send = $this->task_step->getEmailsToSend();
            $emails_sent = $this->task_step->getEmailsSent();

            pqnp_log()->writeString('emails_to_send: '.json_encode($emails_to_send), NewsletterProLog::SEND_FILE);
            pqnp_log()->writeString('emails_sent: '.json_encode($emails_sent), NewsletterProLog::SEND_FILE);

            foreach ($emails_to_send as $index => $email) {
                if ((bool) pqnp_config('TASK_MEMORY_CHECK_ENABLED')) {
                    $current_mem = memory_get_usage(true);

                    if ($current_mem + $this->one_mb * 24 >= $this->memory_limit) {
                        exit('Exit during the memory limt. If the tasks don\'t start sending try to put this command "configuration -set TASK_MEMORY_CHECK_ENABLED 0" into the Terminal tab and press enter.');
                        exit;
                    }
                }

                if (!$this->taskExists()) {
                    exit;
                }

                if ($this->isTaskPaused()) {
                    exit;
                }

                $template = NewsletterProTemplate::newHistory($this->id_newsletter_pro_tpl_history, $email)->load();

                if ($template->user) {
                    // exclude emails
                    $exclude_email = false;
                    $to_info = NewsletterProMail::getEmailInfo($template->user->to());
                    $to_email = $to_info['email'];

                    if (NewsletterProEmailExclusion::newInstance()->emailExists($to_email)) {
                        $exclude_email = true;
                    }

                    // build forwarders
                    $forward = NewsletterProSendManager::buildForward($this->id_newsletter_pro_tpl_history, 'history', $template->user->to());

                    $message = $template->message();
                    $title = $message['title'];
                    $body = $message['body'];

                    $send_manager = NewsletterProSendManager::getInstance();
                    $send_manager->setTemplateNameForAttachment($template->name);

                    try {
                        pqnp_log()->writeString(sprintf('TASK ID: %s, EMAIL: %s', $this->id, json_encode($template->user->to())), NewsletterProLog::SEND_FILE);
                        $send = $send_manager->sendNewsletter($title, $body, $template->user->to(), [
                                'user' => $template->user,
                                'id_smtp' => (int) $this->id_newsletter_pro_smtp,
                                'send_method' => $this->send_method,
                            ], $forward, false, $exclude_email);
                    } catch (Exception $e) {
                        $send = [$e->getMessage()];
                        // $send = false;
                    }

                    if (!is_array($send) && true == $send) {
                        $emails_sent[] = [
                            'email' => $template->user->email,
                            'status' => true,
                        ];
                        ++$this->emails_success;
                        ++$this->num_sent;

                        $this->log(NewsletterPro::getInstance()->l('Success').' : '.(string) $template->user->email);

                        if (self::WRITE_LOG) {
                            $msgSucc = NewsletterPro::getInstance()->l('Success').' : '.(string) $template->user->email;

                            if ($h = fopen($tlog, 'a+')) {
                                fwrite($h, date('Y-m-d H:i:s').' '.$msgSucc."\n");
                                fclose($h);
                            }
                        }
                    } else {
                        $emails_sent[] = [
                            'email' => $template->user->email,
                            'status' => false,
                        ];

                        ++$this->emails_error;

                        $this->appendError([
                            'smtp' => join('<br>', $send),
                        ]);

                        $this->log(NewsletterPro::getInstance()->l('Error').' : '.join("\n", $send).' : '.(string) $template->user->email);

                        if (self::WRITE_LOG) {
                            $msgError = NewsletterPro::getInstance()->l('Error').' : '.join("\n", $send).' : '.(string) $template->user->email;

                            if ($h = fopen($tlog, 'a+')) {
                                fwrite($h, date('Y-m-d H:i:s').' '.$msgError."\n");
                                fclose($h);
                            }
                        }
                    }

                    unset($emails_to_send[$index]);
                    $this->task_step->setEmailsToSend($emails_to_send);
                    $this->task_step->setEmailsSent($emails_sent);
                    $this->task_step->step_active = (count($emails_to_send) > 0 ? 1 : 0);
                // $this->task_step->update();
                } else {
                    $this->appendError([
                        'email' => pSQL(sprintf(NewsletterPro::getInstance()->l('The email %s does not exists in the database.'), $email)),
                    ]);
                }

                ++$this->emails_completed;

                pqnp_log()->writeString('emails_completed', $this->emails_completed, $this->emails_completed % (int) NewsletterPro::getInstance()->ini_config['task_write_db_limit']);

                if (0 == $this->emails_completed % (int) NewsletterPro::getInstance()->ini_config['task_write_db_limit']) {
                    $this->task_step->update();
                    $this->updateFields([
                        'emails_completed' => (int) $this->emails_completed,
                        'emails_success' => (int) $this->emails_success,
                        'emails_error' => (int) $this->emails_error,
                    ]);
                }

                if (!$exclude_email && (int) $this->sleep > 0) {
                    sleep((int) $this->sleep);
                }
            }

            // end the script if it's the case
            if ($this->emails_completed > $this->emails_count) {
                return $this->endSend();
            }

            // update when the step is done
            $this->task_step->update();
            $this->updateFields([
                'emails_completed' => (int) $this->emails_completed,
                'emails_success' => (int) $this->emails_success,
                'emails_error' => (int) $this->emails_error,
            ]);

            // get next step emails
            return $this->send();
        }

        return $this->endSend();
    }

    private function endSend()
    {
        $this->done = 1;
        $this->update();

        return $this->num_sent;
    }

    public function sendShutdownFunctionCallback()
    {
        pqnp_log()->writeString('------------------------ TASK EXIT ------------------------', NewsletterProLog::SEND_FILE);

        $ob_content = ob_get_contents();
        @ob_end_clean();

        if (preg_match('/Fatal error/', $ob_content) && preg_match('/Maximum execution time/', $ob_content)) {
            $this->displayLog()->emptyLog();
            echo '<pre>';
            echo "\n";
            echo NewsletterPro::getInstance()->l('PHP max execution time exceeded. Access the script again to continue the sending process.');
            echo '</pre>';
        } else {
            echo $ob_content;
        }

        if (Validate::isLoadedObject($this)) {
            $this->status = self::STATUS_DEFAULT;
            $this->update();
        }

        if (isset($this->task_step) && Validate::isLoadedObject($this->task_step)) {
            $this->task_step->update();
        }
    }

    public function sendTaskAjax()
    {
        $errors = [];

        if (NewsletterProTask::taskInProgress()) {
            $errors[] = NewsletterPro::getInstance()->l('Cannot send multiple tasks in the same time.');
        }

        @ob_end_clean();
        @ob_start();
        echo NewsletterProTools::jsonEncode([
            'status' => empty($errors),
            'errors' => $errors,
        ]);
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Connection: close');
        @ob_end_flush();
        @ob_flush();
        flush();
        if (session_id()) {
            session_write_close();
        }

        if (empty($errors)) {
            // run in background
            $this->send();
        }
    }

    public function isInProgress()
    {
        return $this->status;
    }

    /**
     * The the current task step or the next step if the current step does not have emails addresses.
     *
     * @return object/boolean
     */
    public function getCurrentStep()
    {
        $steps_id = $this->getStepsIds(true, 1);

        if ($steps_id) {
            $task_step = NewsletterProTaskStep::newInstance($steps_id[0]);

            $emails_to_send = $task_step->getEmailsToSend();
            if (empty($emails_to_send)) {
                $task_step->step_active = 0;
                // if there are no step emails, write step_active = 0
                if ($task_step->update()) {
                    return $this->getCurrentStep();
                }
            } else {
                return $task_step;
            }
        } else {
            // if there are no steps, siable the task
            $this->activ = 0;
            $this->update();
        }

        return false;
    }

    /**
     * Task exist.
     *
     * @param int $id
     *
     * @return int
     */
    public function taskExists()
    {
        return Db::getInstance()->getValue(
            '
			SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_task` WHERE `id_newsletter_pro_task`='.(int) $this->id
        );
    }

    /**
     * Is task paused.
     *
     * @param int $id
     *
     * @return bool
     */
    public function isTaskPaused()
    {
        $this->pause = (int) Db::getInstance()->getValue(
            '
			SELECT `pause` FROM `'._DB_PREFIX_.'newsletter_pro_task` WHERE `id_newsletter_pro_task`='.(int) $this->id
        );

        return $this->pause;
    }

    private function appendError($error_msg)
    {
        $error_msg_db = NewsletterProTools::unSerialize($this->error_msg);
        $error_msg_write = array_merge($error_msg_db, $error_msg);
        $this->error_msg = serialize($error_msg_write);

        return $this->update();
    }

    /**
     * Task in progress.
     *
     * @return bool
     */
    public static function taskInProgress()
    {
        return Db::getInstance()->getValue('
			SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_task`
			WHERE `status` = '.(int) self::STATUS_IN_PROGRESS.'
			AND `done` = 0
		') ? true : false;
    }

    public static function getTaskInProgress()
    {
        $task_id = (int) Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_task` FROM `'._DB_PREFIX_.'newsletter_pro_task`
			WHERE `status` = '.(int) self::STATUS_IN_PROGRESS.'
			AND `done` = 0
		');

        $task = NewsletterProTask::newInstance($task_id);
        if (Validate::isLoadedObject($task)) {
            if ($task->getCurrentStep()) {
                return $task;
            }
        }

        return false;
    }

    public function pauseTask()
    {
        return (int) Db::getInstance()->update('newsletter_pro_task', [
            'pause' => 1,
        ], '`id_newsletter_pro_task` = '.(int) $this->id, 1);
    }

    public static function exportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter_pro_task', $email);

        try {
            // nothing to export
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function privacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter_pro_task', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_task`
				WHERE `error_msg` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"
			');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    public static function clearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter_pro_task', $email);

        try {
            if (Db::getInstance()->update('newsletter_pro_task', [
                'error_msg' => serialize([]),
            ], '`error_msg` REGEXP "'.pSQL(preg_quote($email)).'[^A-Za-z0-9]"')) {
                $response->addToCount(Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
