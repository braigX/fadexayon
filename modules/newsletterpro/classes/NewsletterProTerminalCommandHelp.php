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

class NewsletterProTerminalCommandHelp extends NewsletterProTerminalCommand implements NewsletterProTerminalCommandInterface
{
    public function help(&$output)
    {
        $this->out($this->outCommand('help').' [options...] '.$this->outDescription('- Display available commands.'), 0, false);
        $this->out($this->outOption('help').'          '.$this->outDescription('- List of the help commands.'), 1, false);
        $this->out($this->outOption('clear').'         '.$this->outDescription('- Clear the console.'), 1, false);
        $this->out($this->outOption('shortcuts').'     '.$this->outDescription('- List of terminal shortcuts.'), 1, false);
        $this->out($this->outOption('send').'          '.$this->outDescription('- List of the send commands.'), 1, false);
        $this->out($this->outOption('config').'        '.$this->outDescription('- List of the config commands.'), 1, false);
        $this->out($this->outOption('info').'          '.$this->outDescription('- List of the info commands.'), 1, false);
        $this->out($this->outOption('configuration').' '.$this->outDescription('- List of the configuration commands.')."\n", 1, false);

        foreach ($this->output as $line) {
            $output[] = $line;
        }
    }

    public function run()
    {
        if (!$this->hasFlags()) {
            $this->out('List of all the commands available:'."\n");
            $this->helpShrtcuts();
            NewsletterProTerminalCommandHelp::newInstance()->help($this->output);
            $this->helpClear();
            NewsletterProTerminalCommandConfig::newInstance()->help($this->output);
            NewsletterProTerminalCommandSend::newInstance()->help($this->output);
            NewsletterProTerminalCommandInfo::newInstance()->help($this->output);
            NewsletterProTerminalCommandConfiguration::newInstance()->help($this->output);
        } elseif ($this->hasFlag('help')) {
            NewsletterProTerminalCommandHelp::newInstance()->help($this->output);
        } elseif ($this->hasFlag('shortcuts')) {
            $this->helpShrtcuts();
        } elseif ($this->hasFlag('clear')) {
            $this->helpClear();
        } elseif ($this->hasFlag('config')) {
            NewsletterProTerminalCommandConfig::newInstance()->help($this->output);
        } elseif ($this->hasFlag('send')) {
            NewsletterProTerminalCommandSend::newInstance()->help($this->output);
        } elseif ($this->hasFlag('info')) {
            NewsletterProTerminalCommandInfo::newInstance()->help($this->output);
        } elseif ($this->hasFlag('configuration')) {
            NewsletterProTerminalCommandConfiguration::newInstance()->help($this->output);
        } else {
            throw self::throwError(self::ERROR_INVALID_OPTION);
        }
    }

    private function helpShrtcuts()
    {
        $this->out('Terminal shortcuts:'."\n");
        $this->out('up        '.$this->outDescription('- Go to the last command if the input buffer is empty.'), 0, false);
        $this->out('ctrl+up   '.$this->outDescription('- Go to the previous command.'), 0, false);
        $this->out('ctrl+down '.$this->outDescription('- Go to the next command.')."\n", 0, false);
    }

    private function helpClear()
    {
        $this->out($this->outCommand('clear').' '.$this->outDescription('- Clear the console.')."\n", 0, false);
    }
}
