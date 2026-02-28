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

class NewsletterProTerminalCommand
{
    const ERROR_MISSING_FLAGS = 1;

    const ERROR_INVALID_FLAG = 2;

    const ERROR_INVALID_FLAG_NAME = 3;

    const ERROR_INVALID_OPTION = 4;

    const ERROR_NO_OPTIONS = 5;

    const ERROR_INVALID_OPTION_FLAG = 6;

    protected $flags;

    protected $output = [];

    protected $actions = [];

    public static $throw_errors = [
        self::ERROR_MISSING_FLAGS => 'You did not specified any flag.',
        self::ERROR_INVALID_FLAG => 'Invalid flag.',
        self::ERROR_INVALID_FLAG_NAME => 'Invalid flag "%s".',
        self::ERROR_INVALID_OPTION_FLAG => 'The option flag is invalid.',
        self::ERROR_INVALID_OPTION => 'The option is invalid.',
        self::ERROR_NO_OPTIONS => 'There are not options available.',
    ];

    private $classes = [
        'error' => 'np-console-error',
        'success' => 'np-console-success',
        'command' => 'np-console-dark-yellow',
        'description' => 'np-console-description',
        'flag' => 'np-console-flag',
        'option' => 'np-console-option',
    ];

    public function __construct($flags = [])
    {
        $this->flags = $flags;
    }

    public static function newInstance($flags = [])
    {
        return new static($flags);
    }

    protected function hasFlags()
    {
        return !empty($this->flags);
    }

    protected function hasFlag($flag_name)
    {
        foreach ($this->flags as $flag) {
            $name = $flag['name'];
            if ($name === '-'.$flag_name) {
                return true;
            }
        }

        return false;
    }

    protected function getFlagOptions($flag_name)
    {
        foreach ($this->flags as $flag) {
            $name = $flag['name'];
            if ($name === '-'.$flag_name) {
                return $flag['options'];
            }
        }

        return [];
    }

    protected function hasOption($flag_name, $option_name)
    {
        $options = $this->getFlagOptions($flag_name);

        if (in_array($option_name, $options)) {
            return true;
        }

        return false;
    }

    protected function getFlagModifiers($flag_name)
    {
        foreach ($this->flags as $flag) {
            $name = $flag['name'];
            if ($name === '-'.$flag_name) {
                return $flag['modifiers'];
            }
        }

        return [];
    }

    protected function hasModifier($flag_name, $modifier_name)
    {
        if (!$this->hasFlag($flag_name)) {
            return false;
        }

        $modifiers = $this->getFlagModifiers($flag_name);

        if (array_key_exists($modifier_name, $modifiers)) {
            return true;
        }

        return false;
    }

    protected function getModifier($flag_name, $modifier_name)
    {
        if (!$this->hasFlag($flag_name)) {
            return [];
        }

        $modifiers = $this->getFlagModifiers($flag_name);

        if (array_key_exists($modifier_name, $modifiers)) {
            return $modifiers[$modifier_name];
        }

        return [];
    }

    public static function throwError($error_type, $params = [])
    {
        if (empty($params)) {
            return new Exception(self::$throw_errors[$error_type]);
        } else {
            array_unshift($params, self::$throw_errors[$error_type]);

            return new Exception(call_user_func_array('sprintf', $params));
        }
    }

    public function out($value, $tabs = 0, $escape = true)
    {
        if ($tabs > 0) {
            if ($escape) {
                $this->output[] = str_repeat("\t", $tabs).htmlspecialchars($value, ENT_NOQUOTES);
            } else {
                $this->output[] = str_repeat("\t", $tabs).$value;
            }
        } else {
            if ($escape) {
                $this->output[] = htmlspecialchars($value, ENT_NOQUOTES);
            } else {
                $this->output[] = $value;
            }
        }
    }

    protected function action($name, $value)
    {
        $this->actions[$name] = $value;
    }

    public function outColor($str, $color)
    {
        return '<span style="color: '.$color.'">'.$str.'</span>';
    }

    protected function outClassName($str, $class)
    {
        return '<span class="'.$class.'">'.$str.'</span>';
    }

    protected function outClass($str, $class_key)
    {
        $class_name = '';
        if (array_key_exists($class_key, $this->classes)) {
            $class_name = $this->classes[$class_key];
        }

        return $this->outClassName($str, $class_name);
    }

    protected function outDescription($str)
    {
        return $this->outClass($str, 'description');
    }

    protected function outCommand($str)
    {
        return $this->outClass($str, 'command');
    }

    protected function outError($str)
    {
        return $this->outClass($str, 'error');
    }

    protected function outSeccess($str)
    {
        return $this->outClass($str, 'success');
    }

    protected function outFlag($str)
    {
        return $this->outClass($str, 'flag');
    }

    protected function outOption($str)
    {
        return $this->outClass($str, 'option');
    }

    public function response(&$output, &$actions)
    {
        foreach ($this->output as $line) {
            $output[] = $line;
        }

        foreach ($this->actions as $key => $value) {
            $actions[$key] = $value;
        }
    }

    public static function readConfigFile()
    {
        $filename = _NEWSLETTER_PRO_DIR_.'/config.ini';
        $content = @parse_ini_file($filename);

        if (false == $content) {
            throw new Exception(sprintf('Unable to read the file "%s"', $filename));
        }

        return $content;
    }

    public static function setConfig($key, $value)
    {
        $content = self::readConfigFile();
        $filename = _NEWSLETTER_PRO_DIR_.'/config.ini';
        if (!array_key_exists($key, $content)) {
            throw new Exception(sprintf('Invalid options "%s".', $key));
        }

        $all_content = Tools::file_get_contents($filename);
        if (false === $all_content) {
            throw new Exception(sprintf('Unable to read the file "%s"', $filename));
        }

        $all_content = preg_replace('/^('.preg_quote($key).')(?:\s+)?=(?:\s+)?.*$/m', '$1 = '.$value, $all_content);

        if (false === file_put_contents($filename, $all_content)) {
            throw new Exception('Unable to write the config.ini file.');
        }
    }
}
