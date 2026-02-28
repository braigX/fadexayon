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

use PQNP\Config;

class NewsletterProTerminalCommandConfiguration extends NewsletterProTerminalCommand implements NewsletterProTerminalCommandInterface
{
    protected static $configurationInfo = [
        'NEWSLETTER_TEMPLATE' => 'The current newsletter template.',
        'TASK_MEMORY_CHECK_ENABLED' => 'Disable this value if the task won\'t start sending.',
    ];

    public function help(&$output)
    {
        $this->out($this->outCommand('configuration').' [flags...] '.$this->outDescription('- Modify the module configuration.'), 0, false);
        $this->out($this->outFlag('-get').'   | ['.$this->outCommand('grep').' \'^test\\\\s\\\\d+$\' | '.$this->outCommand('match').' \'*value*\'] '.$this->outDescription('- Display all the configuration.'), 1, false);
        $this->out($this->outFlag('-get').'   [key] | ['.$this->outCommand('grep').' \'^key$\' | '.$this->outCommand('match').' \'*key*\'] '.$this->outDescription('- Display targeted configuration.'), 1, false);
        $this->out($this->outFlag('-set').'   [key] [value] '.$this->outDescription('- Update the existing configuation.'), 1, false);
        $this->out($this->outFlag('-write').' [key] [value] '.$this->outDescription('- Update or add configuation.')."\n", 1, false);

        foreach ($this->output as $line) {
            $output[] = $line;
        }
    }

    public function run()
    {
        $self = $this;
        if (!$this->hasFlags()) {
            throw self::throwError(self::ERROR_MISSING_FLAGS);
        }

        if ($this->hasFlag('get')) {
            $options = $this->getFlagOptions('get');

            if (count($options) > 1) {
                throw new Exception('Two many arguments');
            }
            if (0 == count($options)) {
                $dot = NewsletterProDot::create(pqnp_config());
                $dot = $this->grepRegArray($dot);

                if ($dot instanceof NewsletterProTerminalGrepDisplay) {
                    $dot->output(function ($key, $value) use ($self) {
                        $self->displayConfigurationInfo(Tools::strtoupper($key));
                    });
                } else {
                    foreach ($dot as $key => $value) {
                        $this->displayConfigurationInfo(Tools::strtoupper($key));
                        $this->out(sprintf('[%s] => %s', $key, $value), 0, false);
                    }
                }

                return;
            } else {
                $key = Tools::strtoupper($options[0]);

                $data = pqnp_config($key);
                if (is_array($data)) {
                    $dot = NewsletterProDot::create($data);
                    $dot = $this->grepRegArray($dot);

                    if ($dot instanceof NewsletterProTerminalGrepDisplay) {
                        $dot->output(function ($key, $value) use ($self) {
                            $self->displayConfigurationInfo(Tools::strtoupper($key));
                        });
                    } else {
                        foreach ($dot as $key => $value) {
                            $this->displayConfigurationInfo($key);
                            $this->out(sprintf('[%s] => %s', $key, $value), 0, false);
                        }
                    }
                } else {
                    $this->displayConfigurationInfo($key);
                    $this->out($data, 0, false);
                }

                return;
            }

            return;
        } elseif ($this->hasFlag('set')) {
            $options = $this->getFlagOptions('set');

            if (2 != count($options)) {
                throw new Exception('The flag should have 2 arguments [key] [value].');
            }

            $key = Tools::strtoupper($options[0]);
            $value = $options[1];

            $oldValue = pqnp_config($key);

            if (is_array($oldValue)) {
                throw new Exception('You cannot set array configuration.');
            }

            $dot = NewsletterProDot::create(pqnp_config());
            if (!array_key_exists($key, $dot)) {
                throw new Exception(sprintf('The configuration [%s] key does not exists.', $key));
            }

            pqnp_config($key, $value);

            $this->out(sprintf('Configuration updated: [%s] => %s', $key, pqnp_config($key)));

            return;
        } elseif ($this->hasFlag('write')) {
            $options = $this->getFlagOptions('write');

            if (2 != count($options)) {
                throw new Exception('The flag should have 2 arguments [key] [value].');
            }

            $key = Tools::strtoupper($options[0]);
            $value = $options[1];

            $oldValue = Config::get($key, null, true);

            if (is_array($oldValue)) {
                throw new Exception('You cannot set array configuration.');
            }

            pqnp_config($key, $value, true, true);

            $this->out(sprintf('Configuration updated: [%s] => %s', $key, pqnp_config($key)));

            return;
        } else {
            throw self::throwError(self::ERROR_INVALID_FLAG);
        }
    }

    private function displayConfigurationInfo($key, $displayKey = false)
    {
        if (array_key_exists($key, self::$configurationInfo)) {
            $this->out($this->outColor(' &#x2193; '.($displayKey ? '['.$key.'] ' : '').self::$configurationInfo[$key], '#6bde6b'), 0, false);
        }
    }

    /**
     * @param array $data
     *
     * @return NewsletterProTerminalGrepDisplay|array
     */
    private function grepRegArray($data)
    {
        if ($this->hasModifier('get', 'grep')) {
            $grep = $this->getModifier('get', 'grep');

            if (!empty($grep)) {
                $newData = new NewsletterProTerminalGrepDisplay($this);

                return $newData->grep($grep[0], $data);
            }
        }

        if ($this->hasModifier('get', 'match')) {
            $grep = $this->getModifier('get', 'match');

            if (!empty($grep)) {
                $newData = new NewsletterProTerminalGrepDisplay($this);

                return $newData->match($grep[0], $data);
            }
        }

        return $data;
    }
}
