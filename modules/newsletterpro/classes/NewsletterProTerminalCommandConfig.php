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

class NewsletterProTerminalCommandConfig extends NewsletterProTerminalCommand implements NewsletterProTerminalCommandInterface
{
    public function help(&$output)
    {
        $this->out($this->outCommand('config').' [flags...] '.$this->outDescription('- Modify the config.ini file.'), 0, false);
        $this->out($this->outFlag('-cat').'     '.$this->outDescription('- Display the config.ini file content.'), 1, false);
        $this->out($this->outFlag('-options').' '.$this->outDescription('- Display the config.ini file options.'), 1, false);
        $this->out($this->outFlag('-set').' [option name] [options value] '.$this->outDescription('- Setup the config file.')."\n", 1, false);

        foreach ($this->output as $line) {
            $output[] = $line;
        }
    }

    public function run()
    {
        $filename = _NEWSLETTER_PRO_DIR_.'/config.ini';

        if (!$this->hasFlags()) {
            throw self::throwError(self::ERROR_MISSING_FLAGS);
        }

        if ($this->hasFlag('cat')) {
            $content = Tools::file_get_contents($filename);
            if (false === $content) {
                throw new Exception(sprintf('Unable to read the file "%s"', $filename));
            }
            $this->out($content);
        } elseif ($this->hasFlag('options')) {
            $content = @parse_ini_file($filename);

            if (false == $content) {
                throw new Exception(sprintf('Unable to read the file "%s"', $filename));
            }

            foreach ($content as $key => $value) {
                $this->out($key.' = '.$value);
            }
        } elseif ($this->hasFlag('set')) {
            $values = $this->getFlagOptions('set');

            if (2 != count($values)) {
                throw new Exception('Invalid flag options.');
            }

            $key = $values[0];
            $value = $values[1];

            NewsletterProTerminalCommand::setConfig($key, $value);
            $this->out(sprintf('The option %s was set to %d.', $key, (int) $value));
        } else {
            throw self::throwError(self::ERROR_INVALID_FLAG);
        }
    }
}
