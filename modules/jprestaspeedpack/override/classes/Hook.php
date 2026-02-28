<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Hook extends HookCore
{
    public static function coreCallHook($module, $method, $params)
    {
        if (!Module::isEnabled('jprestaspeedpack') || !file_exists(_PS_MODULE_DIR_ . 'jprestaspeedpack/jprestaspeedpack.php')) {
            return parent::coreCallHook($module, $method, $params);
        } else {
            require_once _PS_MODULE_DIR_ . 'jprestaspeedpack/jprestaspeedpack.php';

            return Jprestaspeedpack::execHook(Jprestaspeedpack::HOOK_TYPE_MODULE, $module, $method, $params);
        }
    }

    public static function coreRenderWidget($module, $hook_name, $params)
    {
        if (!Module::isEnabled('jprestaspeedpack') || !file_exists(_PS_MODULE_DIR_ . 'jprestaspeedpack/jprestaspeedpack.php')) {
            return parent::coreRenderWidget($module, $hook_name, $params);
        } else {
            require_once _PS_MODULE_DIR_ . 'jprestaspeedpack/jprestaspeedpack.php';

            return Jprestaspeedpack::execHook(Jprestaspeedpack::HOOK_TYPE_WIDGET, $module, $hook_name, $params);
        }
    }
}
