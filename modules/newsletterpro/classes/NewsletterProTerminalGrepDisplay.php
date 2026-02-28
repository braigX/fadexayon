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

class NewsletterProTerminalGrepDisplay
{
    protected $command;

    protected $output = [];

    protected $color = '#ef2d2d';

    /**
     * @param array $data
     *
     * @return void
     */
    public function __construct(NewsletterProTerminalCommand $command)
    {
        $this->command = $command;
    }

    /**
     * @param string $grepRegex
     *
     * @return $this
     */
    public function grep($grepRegex, array $data)
    {
        $self = $this;
        $this->output = [];
        foreach ($data as $key => $value) {
            if (preg_match('/'.$grepRegex.'/i', $key) || preg_match('/'.$grepRegex.'/i', $value)) {
                $newKey = preg_replace_callback('/'.$grepRegex.'/i', function ($match) use ($self) {
                    return $self->command->outColor($match[0], $self->color);
                }, $key);

                $newValue = preg_replace_callback('/'.$grepRegex.'/i', function ($match) use ($self) {
                    return $self->command->outColor($match[0], $self->color);
                }, $value);

                $this->output[$key] = sprintf('[%s] => %s', $newKey, $newValue);
            }
        }

        return $this;
    }

    public function match($grepRegex, array $data)
    {
        $self = $this;
        $this->output = [];

        $grepRegex = str_replace('\\.*', '.*', '^'.str_replace('*', '.*', preg_quote($grepRegex)).'$');

        foreach ($data as $key => $value) {
            if (preg_match('/'.$grepRegex.'/i', $key) || preg_match('/'.$grepRegex.'/i', $value)) {
                $newKey = preg_replace_callback('/'.$grepRegex.'/i', function ($match) use ($self) {
                    return $self->command->outColor($match[0], $self->color);
                }, $key);

                $newValue = preg_replace_callback('/'.$grepRegex.'/i', function ($match) use ($self) {
                    return $self->command->outColor($match[0], $self->color);
                }, $value);

                $this->output[$key] = sprintf('[%s] => %s', $newKey, $newValue);
            }
        }

        return $this;
    }

    public function output(callable $callback = null)
    {
        foreach ($this->output as $key => $value) {
            if (isset($callback)) {
                $callback($key, $value);
            }
            $this->command->out($value, 0, false);
        }
    }
}
