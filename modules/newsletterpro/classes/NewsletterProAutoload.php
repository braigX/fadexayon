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

class NewsletterProAutoload
{
    private static $instance;

    protected $classes_path;
    protected $api_path;

    protected $controllers_path;

    protected $libraries_path;

    public function __construct()
    {
        if (!isset(self::$instance)) {
            self::$instance = &$this;
        }

        $this->classes_path = _NEWSLETTER_PRO_DIR_.'/classes/';
        $this->api_path = _NEWSLETTER_PRO_DIR_.'/classes/Api/';
        $this->controllers_path = _NEWSLETTER_PRO_DIR_.'/controllers/';
        $this->libraries_path = _NEWSLETTER_PRO_DIR_.'/libraries/';
        $this->exceptions_path = _NEWSLETTER_PRO_DIR_.'/classes/exceptions/';
    }

    public static function newInstance()
    {
        return new self();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init()
    {
        spl_autoload_register([$this, 'loadClasses']);
        spl_autoload_register([$this, 'loadApi']);
        spl_autoload_register([$this, 'loadControllers']);
        spl_autoload_register([$this, 'loadLibraries']);
        spl_autoload_register([$this, 'loadExceptions']);
    }

    public function loadClasses($class)
    {
        if ($class) {
            $filename = $this->classes_path.$class.'.php';
            if (file_exists($filename)) {
                require_once $filename;
            }
        }
    }

    public function loadApi($class)
    {
        if ($class) {
            $filename = $this->api_path.$class.'.php';
            if (file_exists($filename)) {
                require_once $filename;
            }
        }
    }

    public function loadControllers($class)
    {
        if ($class) {
            $filename = $this->controllers_path.$class.'.php';
            if (file_exists($filename)) {
                require_once $filename;
            }
        }
    }

    public function loadLibraries($class)
    {
        if ($class) {
            $filename = $this->libraries_path.$class.'.php';
            if (file_exists($filename)) {
                require_once $filename;
            }
        }
    }

    public function loadExceptions($class)
    {
        if ($class) {
            $filename = $this->exceptions_path.$class.'.php';
            if (file_exists($filename)) {
                require_once $filename;
            }
        }
    }
}
