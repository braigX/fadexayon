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

class NewsletterProDevModeController extends NewsletterProController
{
    private $output = [];

    private $actions = [];

    private $command_name;

    private $flags = [];

    private $allowed_commands = [
        'help',
        'clear',
        'config',
        'send',
        'info',
        'configuration',
    ];

    public function newInstance()
    {
        return new self();
    }

    public function initContent()
    {
        parent::initContent();

        $this->js_data = [
        ];
    }

    public function postProcess()
    {
        parent::postProcess();

        $action = 'submit_dev_mode_controller';

        if (Tools::isSubmit($action)) {
            @ini_set('max_execution_time', '2880');
            ob_clean();
            ob_end_clean();

            if (Tools::getValue('token') != $this->token) {
                $this->display('Invalid Token!');
            }

            try {
                switch (Tools::getValue($action)) {
                    case 'execute':
                        $command = Tools::getValue('command');
                        $this->display($this->execute($command));
                        break;

                    default:
                        exit('Invalid Action!');
                        break;
                }
            } catch (Exception $e) {
                if (NewsletterProAjaxController::isXHR()) {
                    $this->response->addError($e->getMessage());

                    return $this->display($this->response->display(), true);
                } else {
                    throw $e;
                }
            }
        }
    }

    private function validateCommand($command_name)
    {
        if (in_array($command_name, $this->allowed_commands)) {
            return true;
        }

        return false;
    }

    private function getFlagsAndModif($e_command)
    {
        unset($e_command[0]);
        $flags = [];
        $index = 0;
        $m_index = 0;
        $m_key = 0;
        $modifier = false;

        foreach ($e_command as $key => $value) {
            if (preg_match('/^-.*$/', $value)) {
                $flags[$index] = [
                    'name' => $value,
                    'options' => [],
                    'modifiers' => [],
                ];
                ++$index;
            } elseif (array_key_exists($index - 1, $flags)) {
                $val = trim($value, '\'"');
                if ('|' === $value) {
                    $m_key = $key + 1;
                    ++$m_index;
                    $modifier = true;
                    continue;
                }

                if (!$modifier) {
                    $flags[$index - 1]['options'][] = $val;
                } else {
                    if (!array_key_exists($e_command[$m_key], $flags[$index - 1]['modifiers'])) {
                        $flags[$index - 1]['modifiers'][$e_command[$m_key]] = [];
                    } else {
                        $flags[$index - 1]['modifiers'][$e_command[$m_key]][] = $val;
                    }
                }
            }
        }

        return $flags;
    }

    private function runClear()
    {
        $this->actions['clear_output'] = true;
    }

    private function run($command_name, $flags)
    {
        $this->command_name = $command_name;
        $this->flags = $flags;

        switch ($this->command_name) {
            case 'help':
                $send = NewsletterProTerminalCommandHelp::newInstance($this->flags);
                $send->run();
                $send->response($this->output, $this->actions);
                break;
            case 'clear':
                $this->runClear();
                break;
            case 'config':
                $send = NewsletterProTerminalCommandConfig::newInstance($this->flags);
                $send->run();
                $send->response($this->output, $this->actions);
                break;
            case 'send':
                $send = NewsletterProTerminalCommandSend::newInstance($this->flags);
                $send->run();
                $send->response($this->output, $this->actions);
                break;
            case 'info':
                $send = NewsletterProTerminalCommandInfo::newInstance($this->flags);
                $send->run();
                $send->response($this->output, $this->actions);
                break;
            case 'configuration':
                $send = NewsletterProTerminalCommandConfiguration::newInstance($this->flags);
                $send->run();
                $send->response($this->output, $this->actions);
                break;
        }
    }

    private function execute($command)
    {
        $response = &$this->response;
        $response->setArray([
            'output' => [],
            'command' => $command,
            'actions' => [],
        ]);

        $command = trim(preg_replace('/\s+/', ' ', $command), ' ;');
        $response->set('command', htmlspecialchars($command, ENT_NOQUOTES));

        try {
            if (!preg_match_all('/[^\s"\']+|"([^"]*)"|\'([^\']*)\'/', $command, $e_command)) {
                throw new Exception($this->l('Invalid command.'));
            }
            $e_command = $e_command[0];

            if (empty($e_command)) {
                throw new Exception($this->l('Invalid command.'));
            }

            $command_name = $e_command[0];

            if (!$this->validateCommand($command_name)) {
                throw new Exception(sprintf($this->l('Unknown command name "%s"'), $command_name));
            }

            // convert help flags into options
            if ('help' == $command_name) {
                if (count($e_command) > 1) {
                    $index = 0;
                    foreach ($e_command as $key => $value) {
                        if ($index > 0) {
                            if ('-' != $value[0]) {
                                $e_command[$key] = '-'.$value;
                            }
                        }
                        ++$index;
                    }
                }
            }

            $flags = $this->getFlagsAndModif($e_command);

            $this->run($command_name, $flags);
            $response->set('output', $this->output);
            $response->set('actions', $this->actions);
        } catch (Exception $e) {
            $response->addError('<span class="np-console-error">'.htmlspecialchars($e->getMessage(), ENT_NOQUOTES).'</span>');
        }

        return $response->display();
    }
}
