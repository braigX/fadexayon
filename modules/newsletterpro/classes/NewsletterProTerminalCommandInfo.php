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

class NewsletterProTerminalCommandInfo extends NewsletterProTerminalCommand implements NewsletterProTerminalCommandInterface
{
    public function help(&$output)
    {
        $this->out($this->outCommand('info').' [flags...]'.$this->outDescription(' - Display informations'), 0, false);
        $this->out($this->outFlag('-version').' '.$this->outDescription('- Display the Newsletter Pro module version')."\n", 1, false);

        foreach ($this->output as $line) {
            $output[] = $line;
        }
    }

    public function run()
    {
        if (!$this->hasFlags()) {
            throw self::throwError(self::ERROR_MISSING_FLAGS);
        }

        if ($this->hasFlag('version')) {
            $this->out(pqnp_module()->version);
        } else {
            throw self::throwError(self::ERROR_INVALID_FLAG);
        }
    }
}
