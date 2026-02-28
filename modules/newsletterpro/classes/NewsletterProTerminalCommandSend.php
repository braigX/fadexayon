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

class NewsletterProTerminalCommandSend extends NewsletterProTerminalCommand implements NewsletterProTerminalCommandInterface
{
    public function help(&$output)
    {
        $this->out($this->outCommand('send').' [flags...] '.$this->outDescription('- Manage the newsletter send.'), 0, false);

        $this->out($this->outFlag('-log').' [options...]', 1, false);
        $this->out($this->outOption('status').'	 '.$this->outDescription('- Find if the log is enabled or disabled.'), 2, false);
        $this->out($this->outOption('enable').'   '.$this->outDescription('- Enable the sending log.'), 2, false);
        $this->out($this->outOption('disable').'  '.$this->outDescription('- Disable the sending log.'), 2, false);
        $this->out($this->outOption('empty').'    '.$this->outDescription('- Clear the sending log file.'), 2, false);
        $this->out($this->outOption('pwd').'      '.$this->outDescription('- Display the path of the log file.'), 2, false);
        $this->out($this->outOption('cat').'      '.$this->outDescription('- Output the log file.'), 2, false);
        $this->out($this->outOption('info').'     '.$this->outDescription('- Display informations about the log.'), 2, false);
        $this->out($this->outOption('display').' [options...] | ['.$this->outCommand('grep').' \'*.com\' email | '.$this->outCommand('resend').' | '.$this->outCommand('count').'] '.$this->outDescription('- Display the log file.'), 2, false);
        $this->out($this->outOption('all').'            '.$this->outDescription('- Display all the emails.'), 3, false);
        $this->out($this->outOption('success').'        '.$this->outDescription('- Display sent succeeded emails.'), 3, false);
        $this->out($this->outOption('failed').'         '.$this->outDescription('- Display the sent failed emails.'), 3, false);
        $this->out($this->outOption('duplicate').'      '.$this->outDescription('- Display the duplicate emails.'), 3, false);
        $this->out($this->outOption('not-duplicate').'  '.$this->outDescription('- Display the not duplicate emails.'), 3, false);

        $this->out($this->outFlag('-template').' [options...]', 1, false);
        $this->out($this->outOption('name').'                       '.$this->outDescription('- Display the active template name.'), 2, false);
        $this->out($this->outOption('set').' \'template_name\'        '.$this->outDescription('- Change the template.'), 2, false);
        $this->out($this->outOption('list').' [lang_iso]            '.$this->outDescription('- List the templates.'), 2, false);
        $this->out($this->outOption('view').' [flags...] [lang_iso] '.$this->outDescription('- View the current tempalte.'), 2, false);
        $this->out($this->outFlag('-name').' \'template_name\' '.$this->outDescription('- View the template.'), 3, false);

        $this->out($this->outFlag('-connection').' [options...]', 1, false);
        $this->out($this->outOption('info').'    '.$this->outDescription('- Display the current connection info.'), 2, false);
        $this->out($this->outOption('enable').'  '.$this->outDescription('- Enable the connection.'), 2, false);
        $this->out($this->outOption('disable').' '.$this->outDescription('- Disable the connection.'), 2, false);
        $this->out($this->outOption('set').' [flags...] '.$this->outDescription('- Chnage the connection.'), 2, false);
        $this->out($this->outFlag('-name').' \'Name\' '.$this->outDescription('- Change the connection using the name.'), 3, false);
        $this->out($this->outFlag('-id').' [number] '.$this->outDescription('- Change the connection using the id.'), 3, false);
        $this->out($this->outOption('list').' [flags...] '.$this->outDescription('- List the available connections.'), 2, false);
        $this->out($this->outFlag('-long').' '.$this->outDescription('- List the available connection with details.'), 3, false);
        $this->out($this->outOption('test').' \'example@domain.com\' [flags...] '.$this->outDescription('- Test the current connection.'), 2, false);
        $this->out($this->outFlag('-name').' \'Name\' '.$this->outDescription('- Test a specified connection using the name.'), 3, false);
        $this->out($this->outFlag('-id').' [number] '.$this->outDescription('- Test a specified connection using the id.')."\n", 3, false);

        foreach ($this->output as $line) {
            $output[] = $line;
        }
    }

    public function run()
    {
        if (!$this->hasFlags()) {
            throw self::throwError(self::ERROR_MISSING_FLAGS);
        }

        if ($this->hasFlag('log')) {
            $this->log();
        } elseif ($this->hasFlag('template')) {
            $this->tempalte();
        } elseif ($this->hasFlag('connection')) {
            $this->connection();
        } else {
            throw self::throwError(self::ERROR_INVALID_FLAG);
        }
    }

    private function log()
    {
        $log_filename = _NEWSLETTER_PRO_DIR_.'/logs/send.log';
        $values = $this->getFlagOptions('log');

        // d('sss');

        // npd('dafafsd', 'RAMS LA SEND STATUS');

        if (in_array('status', $values)) {
            $content = NewsletterProTerminalCommand::readConfigFile();

            if ((int) $content['write_send_log'] > 0) {
                $this->out('The send log is enabled.');
            } else {
                $this->out('The send log is disabled.');
            }
        } elseif (in_array('enable', $values)) {
            NewsletterProTerminalCommand::setConfig('write_send_log', 1);
            $this->out('Log enabled.');
        } elseif (in_array('disable', $values)) {
            NewsletterProTerminalCommand::setConfig('write_send_log', 0);
            $this->out('Log disabled.');
        } elseif (in_array('empty', $values)) {
            if (false === file_put_contents($log_filename, '')) {
                throw new Exception('Unable to empty the log file.');
            } else {
                $this->out('The log file is not empty.');
            }
        } elseif (in_array('pwd', $values)) {
            $this->out($log_filename);
        } elseif (in_array('cat', $values)) {
            if (($content = Tools::file_get_contents($log_filename)) === false) {
                throw new Exception(sprintf('Unable to read the log file "%s".'), $log_filename);
            }
            $this->out($content);
        } elseif (in_array('info', $values)) {
            if (($file = fopen($log_filename, 'r')) == false) {
                throw new Exception(sprintf('Unable to read to log file "%s".'), $log_filename);
            }

            $total = 0;
            $failed = 0;
            $succeeded = 0;
            $dup_count = 0;
            $not_duplicate = [];
            $not_dup_count = 0;

            while (!feof($file)) {
                $row = trim(fgets($file));
                if ($row = $this->getSendLogMatchRow($row)) {
                    ++$total;
                    if ($row['status'] > 0) {
                        ++$succeeded;
                    } else {
                        ++$failed;
                    }

                    if (!array_key_exists($row['email'], $not_duplicate)) {
                        ++$not_dup_count;
                        $not_duplicate[$row['email']] = 1;
                    } else {
                        ++$dup_count;
                    }
                }
            }
            fclose($file);

            $this->out(sprintf('Total: %d', $total));
            $this->out(sprintf('Succeeded: %d', $succeeded));
            $this->out(sprintf('Failed: %d', $failed));
            $this->out(sprintf('Not Duplicate: %d', $not_dup_count));
            $this->out(sprintf('Duplicate: %d', $dup_count));
        } elseif (in_array('display', $values)) {
            $intersect = array_intersect(['all', 'success', 'failed', 'duplicate', 'not-duplicate'], $values);

            if (!empty($intersect)) {
                if (($file = fopen($log_filename, 'r')) == false) {
                    throw new Exception(sprintf('Unable to read to log file "%s".'), $log_filename);
                }

                $count_modifier = $this->hasModifier('log', 'count') ? true : false;
                $all = [];
                $failed = [];
                $success = [];
                $duplicate = [];
                $duplicate_display = [];
                $not_duplicate = [];
                $not_duplicate_display = [];

                $max_email_len = 0;
                $rows = [];
                while (!feof($file)) {
                    $row = trim(fgets($file));
                    if ($row = $this->getSendLogMatchRow($row)) {
                        if ($this->hasModifier('log', 'grep')) {
                            $grep = $this->getModifier('log', 'grep');

                            if (!empty($grep)) {
                                $grep_regex = str_replace('\\.*', '.*', '^'.str_replace('*', '.*', preg_quote($grep[0])).'$');

                                $column_name = 'row';
                                if (array_key_exists(1, $grep)) {
                                    $column_name = $grep[1];
                                    if (!array_key_exists($column_name, $row)) {
                                        throw new Exception(sprintf('Invalid grep colum name "%s". The available columns are [row, date, email, email_len, status, error].', $column_name));
                                    }
                                }

                                $data = trim($row[$column_name]);

                                if (preg_match('/'.$grep_regex.'/i', $data)) {
                                    $rows[] = $row;
                                    if ($row['email_len'] > $max_email_len) {
                                        $max_email_len = $row['email_len'];
                                    }
                                }
                            } else {
                                $rows[] = $row;
                                if ($row['email_len'] > $max_email_len) {
                                    $max_email_len = $row['email_len'];
                                }
                            }
                        } else {
                            $rows[] = $row;
                            if ($row['email_len'] > $max_email_len) {
                                $max_email_len = $row['email_len'];
                            }
                        }
                    }
                }

                fclose($file);

                // send -log display failed | grep '*j@demo.com' email | resend

                foreach ($rows as $row) {
                    if (in_array('all', $values)) {
                        $all[] = $this->sendLogRowToDisplay($row, $max_email_len);
                    }

                    if ($row['status'] > 0 && in_array('success', $values)) {
                        $success[] = $this->sendLogRowToDisplay($row, $max_email_len);
                    } elseif (0 == $row['status'] && in_array('failed', $values)) {
                        $failed[] = $this->sendLogRowToDisplay($row, $max_email_len);
                    }

                    if (in_array('duplicate', $values) || in_array('not-duplicate', $values)) {
                        if (!array_key_exists($row['email'], $not_duplicate)) {
                            $not_duplicate[$row['email']] = 1;
                            if (in_array('not-duplicate', $values)) {
                                $not_duplicate_display[] = $this->sendLogRowToDisplay($row, $max_email_len);
                            }
                        } else {
                            if (!array_key_exists($row['email'], $duplicate)) {
                                $duplicate[$row['email']] = 1;
                            } else {
                                ++$duplicate[$row['email']];
                            }

                            if (in_array('duplicate', $values)) {
                                $duplicate_display[] = $this->sendLogRowToDisplay($row, $max_email_len);
                            }
                        }
                    }
                }

                if (!empty($rows)) {
                    if (in_array('all', $values)) {
                        if ($count_modifier) {
                            $this->out(count($all).' emails', 0, false);
                        } else {
                            $this->out(implode("\n", $all), 0, false);
                        }
                    } elseif (in_array('success', $values)) {
                        if ($count_modifier) {
                            $this->out(count($success).' emails', 0, false);
                        } else {
                            $this->out(implode("\n", $success), 0, false);
                        }
                    } elseif (in_array('failed', $values)) {
                        if ($count_modifier) {
                            $this->out(count($failed).' emails', 0, false);
                        } else {
                            $this->out(implode("\n", $failed), 0, false);
                        }
                    } elseif (in_array('duplicate', $values)) {
                        if ($count_modifier) {
                            $this->out(count($duplicate_display).' emails', 0, false);
                        } else {
                            $this->out(implode("\n", $duplicate_display), 0, false);
                        }
                    } elseif (in_array('not-duplicate', $values)) {
                        if ($count_modifier) {
                            $this->out(count($not_duplicate_display).' emails', 0, false);
                        } else {
                            $this->out(implode("\n", $not_duplicate_display), 0, false);
                        }
                    }
                } elseif ($count_modifier) {
                    $this->out(count($rows).' emails', 0, false);
                }
            } else {
                throw self::throwError(self::ERROR_INVALID_OPTION);
            }
        } else {
            throw self::throwError(self::ERROR_INVALID_OPTION);
        }
    }

    private function tempalte()
    {
        $options = $this->getFlagOptions('template');
        $mail_templates_path = NewsletterPro::getInstance()->dir_location.'mail_templates/newsletter/';

        if (empty($options)) {
            throw self::throwError(self::ERROR_NO_OPTIONS);
        }

        $option = $options[0];

        switch ($option) {
            case 'view':
                $id_lang = Context::getContext()->language->id;
                $name = pqnp_config('NEWSLETTER_TEMPLATE');

                $name_options = $this->getFlagOptions('name');
                if ($this->hasFlag('name')) {
                    if (empty($name_options)) {
                        throw self::throwError(self::ERROR_NO_OPTIONS);
                    }
                    // $this->throwNoOptionsAvailable();

                    $template_name = $name_options[0];

                    if (!file_exists($mail_templates_path.$template_name) || !is_dir($mail_templates_path.$template_name)) {
                        throw new Exception(sprintf('The template "%s" does not exists.', $template_name));
                    }
                    $name = $template_name.'.html';
                    if (array_key_exists(1, $name_options)) {
                        $id_lang = $this->getIdLnagByIsoCode($name_options[1]);
                    }
                } else {
                    if (array_key_exists(1, $options)) {
                        $id_lang = $this->getIdLnagByIsoCode($options[1]);
                    }
                }

                $link = AdminNewsletterPro::getLink([
                    'submit_template_controller' => 'viewTemplate',
                    'name' => $name,
                    'id_lang' => (int) $id_lang,
                ]);

                $http_link = Tools::getHttpHost(true).'/'.basename(_PS_ADMIN_DIR_).'/'.$link;
                $this->action('view_template', $link);
                $this->out("<a href=\"{$link}\" target=\"_blank\">{$http_link}</a>", 0, false);

                break;
            case 'list':
                $list = [];
                $files = NewsletterProTools::getDirectoryIterator($mail_templates_path, '/^[a-zA-Z0-9_-]+$/');

                $id_lang = Context::getContext()->language->id;
                if (array_key_exists(1, $options)) {
                    $id_lang = $this->getIdLnagByIsoCode($options[1]);
                }

                foreach ($files as $file) {
                    if ($file->isDir()) {
                        $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $list[] = $name;

                        // d($name);

                        $link = AdminNewsletterPro::getLink([
                            'submit_template_controller' => 'viewTemplate',
                            'name' => $name.'.html',
                            'id_lang' => (int) $id_lang,
                        ]);

                        $this->out("<a href=\"{$link}\" target=\"_blank\">{$name}</a>", 0, false);
                    }
                }
                break;

            case 'name':
                $this->out(pathinfo(pqnp_config('NEWSLETTER_TEMPLATE'), PATHINFO_FILENAME));
                break;

            case 'set':
                if (!array_key_exists(1, $options)) {
                    throw new Exception('The tempalte name is not specified.');
                }
                $template_name = $options[1];
                $template_filename = $mail_templates_path.$template_name;

                if (!file_exists($template_filename) || !is_dir($template_filename)) {
                    throw new Exception(sprintf('The template "%s" does not exists.', $template_name));
                }

                $template_name = pathinfo($template_name, PATHINFO_FILENAME);

                if (!pqnp_config('NEWSLETTER_TEMPLATE', $template_name.'.html')) {
                    throw new Exception('Unable to write the template configuration.');
                }

                $this->out(sprintf('The template was set to "%s".', $template_name));

                $this->action('set_template', [
                    'name' => $template_name,
                    'fullname' => $template_name.'.html',
                ]);

                break;
            default:
                throw self::throwError(self::ERROR_INVALID_OPTION);
                break;
        }
    }

    private function connection()
    {
        $options = $this->getFlagOptions('connection');

        if (empty($options)) {
            throw self::throwError(self::ERROR_NO_OPTIONS);
        }

        $option_name = $options[0];

        switch ($option_name) {
            case 'info':
                if ((int) pqnp_config('SMTP_ACTIVE')) {
                    $this->out('Newsletter Pro connection:'."\n");

                    if (0 == (int) pqnp_config('SMTP')) {
                        throw new Exception('The connection is active, but is not configurated.');
                    }

                    $mail = NewsletterProMail::newInstance((int) pqnp_config('SMTP'));

                    if (!Validate::isLoadedObject($mail)) {
                        throw new Exception('Cannot get the connection from the database.');
                    }

                    $this->out(implode("\n", $mail->toTerminalInfo()));
                } else {
                    $this->out('Prestashop connection:'."\n");

                    /** @var NewsletterProMail */
                    $mail = NewsletterProMail::getInstance(NewsletterProMail::getDefaultConnection());
                    $this->out(implode("\n", $mail->toTerminalInfo(true)));
                }
                break;

            case 'enable':
                if (!(int) pqnp_config('SMTP_ACTIVE', 1)) {
                    throw new Exception('Unable to write the configuration.');
                }

                $this->out('Connection was enabled.');

                $this->action('enable_disable_connection', [
                    'value' => 1,
                ]);

                break;

            case 'disable':
                if (!(int) pqnp_config('SMTP_ACTIVE', 0)) {
                    throw new Exception('Unable to write the configuration.');
                }

                $this->out('Connection was disabled.');

                $this->action('enable_disable_connection', [
                    'value' => 0,
                ]);

                break;

            case 'list':
                $result = NewsletterProMail::getAllMails();

                $view = NewsletterProTerminalView::newInstance([
                    'id_newsletter_pro_smtp' => 'ID',
                    'name' => 'Name',
                    'method' => 'Method',
                    'from_name' => 'From Name',
                    'from_email' => 'From Email',
                    'reply_to' => 'Reply To',
                    'domain' => 'Domain',
                    'server' => 'Server',
                    'user' => 'User',
                    'encryption' => 'Encryption',
                    'port' => 'Port',
                    'list_unsubscribe_active' => 'LU Active',
                    'list_unsubscribe_email' => 'LU Email',
                ], [
                    'table_view' => true,
                ]);

                $view->addMultiple($result, function ($row) {
                    $method = (int) $row['method'];
                    $row['method'] = NewsletterProMail::METHOD_MAIL == $method ? 'MAIL' : 'SMTP';
                    $row['encryption'] = Tools::strtoupper($row['encryption']);

                    if (NewsletterProMail::METHOD_MAIL == $method) {
                        $row['domain'] = '';
                        $row['server'] = '';
                        $row['user'] = '';
                        $row['encryption'] = '';
                        $row['port'] = '';
                        $row['list_unsubscribe_active'] = '';
                        $row['list_unsubscribe_email'] = '';
                    }

                    return $row;
                });

                if (!$this->hasFlag('long')) {
                    $view->show([
                        'id_newsletter_pro_smtp',
                        'name',
                    ]);
                }

                $this->out($view->render());

                break;
            case 'test':
                // daca nu are id sau nume iau default connection nu fac erroare

                if (!array_key_exists(1, $options)) {
                    throw new Exception('You did not specified the email addresses to a test.');
                }

                $email = $options[1];
                $id_smtp = 0;

                if ($this->hasFlag('id') || $this->hasFlag('name')) {
                    $id_smtp = $this->getSmtpIdByNameId();
                }

                $output = NewsletterProSendManager::sendTestTerminal($email, $id_smtp);

                if (count($output['errors']) > 0) {
                    foreach ($output['errors'] as $value) {
                        $this->out($this->outError($value), 0, false);
                    }
                } else {
                    foreach ($output['success'] as $value) {
                        $this->out($this->outSeccess($value), 0, false);
                    }
                }

                break;
            case 'set':
                if (!$this->hasFlag('id') && !$this->hasFlag('name')) {
                    throw self::throwError(self::ERROR_INVALID_OPTION_FLAG);
                }
                $id_smtp = 0;

                if ($this->hasFlag('id') || $this->hasFlag('name')) {
                    $id_smtp = $this->getSmtpIdByNameId();
                }

                if (!pqnp_config('SMTP', (int) $id_smtp)) {
                    throw new Exception('Unable to write the configuration.');
                }

                $this->out('The connection was set.');

                $this->action('set_connection', [
                    'id_smtp' => $id_smtp,
                ]);

                break;
            default:
                throw self::throwError(self::ERROR_INVALID_OPTION);
                break;
        }
    }

    private function getSmtpIdByNameId()
    {
        $id_smtp = 0;

        if ($this->hasFlag('id')) {
            $flag_option = $this->getFlagOptions('id');
            if (empty($flag_option)) {
                throw self::throwError(self::ERROR_INVALID_OPTION);
            }

            $id_smtp = (int) Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_smtp` FROM `'._DB_PREFIX_.'newsletter_pro_smtp`
				WHERE `id_newsletter_pro_smtp` = '.(int) $flag_option[0].'
			');

            if (0 == $id_smtp) {
                throw new Exception(sprintf('The connection with id "%d" does not exists.', (int) $flag_option[0]));
            }
        } elseif ($this->hasFlag('name')) {
            $flag_option = $this->getFlagOptions('name');
            if (empty($flag_option)) {
                throw self::throwError(self::ERROR_INVALID_OPTION);
            }

            $id_smtp = (int) Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_smtp` FROM `'._DB_PREFIX_.'newsletter_pro_smtp`
				WHERE `name` = "'.pSQL($flag_option[0]).'"
			');

            if (0 == $id_smtp) {
                throw new Exception(sprintf('The connection with the name "%s" does not exists.', $flag_option[0]));
            }
        }

        if (0 == $id_smtp) {
            throw new Exception('The connection does not exists.');
        }

        return $id_smtp;
    }

    private function getSendLogMatchRow($row)
    {
        if (preg_match('/((?P<date>\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\s?\[(?P<ip>[^\]]+)\]\s+\>\s+)?(?P<email>[^@]+@[^ ]+)\s+\[status\s+(?P<status>\d+)\](?:\s+\[(?P<error>[^\]]+)\])?/', $row, $match)) {
            return [
                'row' => $row,
                'date' => array_key_exists('date', $match) ? $match['date'] : false,
                'ip' => array_key_exists('ip', $match) ? $match['ip'] : false,
                'email' => $match['email'],
                'email_len' => Tools::strlen($match['email']),
                'status' => (int) $match['status'],
                'error' => array_key_exists('error', $match) ? $match['error'] : false,
            ];
        }

        return false;
    }

    private function sendLogRowToDisplay($row, $max_email_len = 0)
    {
        if ($row['status'] > 0) {
            return sprintf('<span class="np-console-success">%s</span>', htmlspecialchars($row['email'], ENT_NOQUOTES));
        } else {
            if ($max_email_len > 0) {
                $diff = $max_email_len - $row['email_len'];
                if ($diff < 0) {
                    $diff = 0;
                }
            } else {
                $diff = 0;
            }

            return sprintf('<span class="np-console-error">%s '.str_repeat(' ', $diff).' [%s]</span>', htmlspecialchars($row['email'], ENT_NOQUOTES), htmlspecialchars($row['error'], ENT_NOQUOTES));
        }
    }

    private function getIdLnagByIsoCode($lang_iso)
    {
        $id_lang = (int) Context::getContext()->language->id;

        $id = (int) Db::getInstance()->getValue('
			SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`
			WHERE `iso_code` = "'.pSQL($lang_iso).'"
		');
        if ($id > 0) {
            $id_lang = $id;
        }

        return $id_lang;
    }
}
