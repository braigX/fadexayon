<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

namespace JPresta\SpeedPack;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class JprestaSubModule
{
    protected $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function displayHeader()
    {
    }

    public function saveConfiguration()
    {
        return '';
    }

    public function displayForm()
    {
        return '';
    }

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function enable()
    {
        return true;
    }

    public function disable()
    {
        return true;
    }

    /**
     * @note Keep the name 'l' so Prestashop can parse messages
     */
    protected function l($string, $specific, $sprintf = null, $js = false, $locale = null, $fallback = true, $escape = true)
    {
        $msg = \Translate::getModuleTranslation(
            $this->module,
            $string,
            ($specific) ? $specific : $this->module->name,
            $sprintf,
            $js,
            $locale,
            $fallback,
            $escape
        );

        return str_replace("'", '&#039;', $msg);
    }
}
