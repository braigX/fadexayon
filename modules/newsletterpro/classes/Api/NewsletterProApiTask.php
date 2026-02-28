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

class NewsletterProApiTask extends NewsletterProApi
{
    public function call()
    {
        header('Access-Control-Allow-Origin: *');

        pqnp_log()->writeString('------------------------ TASK START ------------------------', NewsletterProLog::SEND_FILE);

        $module = NewsletterProTools::module();
        $today = new DateTime('now');

        echo '<pre>';
        echo 'Date : '.$today->format('Y-m-d H:i:s')."\n\n";

        try {
            if (NewsletterProTask::taskInProgress()) {
                $task = NewsletterProTask::getTaskInProgress();

                $taskExit = true;
                $msg = "\n".$module->l('The task is in progress');

                if ($task) {
                    if ($task->isTaskPaused()) {
                        $task->displayLog("\n")->emptyLog();
                        $msg = "\n".$module->l('The task is in paused');
                    } elseif ((strtotime($task->date_modified) + 120) <= time()) {
                        // start the task again after 2 minutes (300 seconds) if the date has not changes and the task status is showing in progress
                        echo $module->l('Task was forced to continue.');
                        $task->emptyLog();
                        $taskExit = false;

                        $task->displayLog("\n")->emptyLog();
                        $numSent = $task->send();
                        echo $msg;
                        exit;
                    } else {
                        $task->displayLog("\n")->emptyLog();
                    }
                }

                if ($taskExit) {
                    echo $msg;
                    exit;
                }
            }

            $task = NewsletterProTask::getTask($today);

            if ($task) {
                $task->displayLog("\n")->emptyLog();
                $numSent = $task->send();
                echo "\n".sprintf($module->l('This script execution has sent %s emails.'), $numSent);
            } else {
                echo $module->l('There are no active task scheduled for today.');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
