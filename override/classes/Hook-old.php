<?php
/**
 * Override for Hook class to fix product combination display issues
 */

use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

class Hook extends HookCore
{
    /**
     * Execute modules for specified hook with enhanced error handling
     */
    public static function exec(
        $hook_name,
        $hook_args = [],
        $id_module = null,
        $array_return = false,
        $check_exceptions = true,
        $use_push = false,
        $id_shop = null,
        $chain = false
    ) {
        if ($use_push) {
            Tools::displayParameterAsDeprecated('use_push');
        }

        if (defined('PS_INSTALLATION_IN_PROGRESS') || !self::getHookStatusByName($hook_name)) {
            return $array_return ? [] : null;
        }

        $hookRegistry = self::getHookRegistry();
        $isRegistryEnabled = null !== $hookRegistry;

        if ($isRegistryEnabled) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            $hookRegistry->selectHook($hook_name, $hook_args, $backtrace[0]['file'], $backtrace[0]['line']);
        }

        if (true === $chain) {
            $array_return = false;
        }

        static $disable_non_native_modules = null;
        if ($disable_non_native_modules === null) {
            $disable_non_native_modules = (bool) Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        }

        if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name)) {
            throw new PrestaShopException('Invalid id_module or hook_name');
        }

        if (!$module_list = Hook::getHookModuleExecList($hook_name)) {
            if ($isRegistryEnabled) {
                $hookRegistry->collect();
            }
            return ($array_return) ? [] : '';
        }

        if (!$id_hook = Hook::getIdByName($hook_name, false)) {
            if ($isRegistryEnabled) {
                $hookRegistry->collect();
            }
            return ($array_return) ? [] : false;
        }

        Hook::$executed_hooks[$id_hook] = $hook_name;

        $context = Context::getContext();
        if (!isset($hook_args['cookie']) || !$hook_args['cookie']) {
            $hook_args['cookie'] = $context->cookie;
        }
        if (!isset($hook_args['cart']) || !$hook_args['cart']) {
            $hook_args['cart'] = $context->cart;
        }

        $altern = 0;
        $output = ($array_return) ? [] : '';

        if ($disable_non_native_modules && !isset(Hook::$native_module)) {
            Hook::$native_module = Module::getNativeModuleList();
        }

        $different_shop = false;
        if ($id_shop !== null && Validate::isUnsignedId($id_shop) && $id_shop != $context->shop->getContextShopID()) {
            $old_context = $context->shop->getContext();
            $old_shop = clone $context->shop;
            $shop = new Shop((int) $id_shop);
            if (Validate::isLoadedObject($shop)) {
                $context->shop = $shop;
                $context->shop->setContext(Shop::CONTEXT_SHOP, $shop->id);
                $different_shop = true;
            }
        }

        foreach ($module_list as $key => $hookRegistration) {
            if ($id_module && $id_module != $hookRegistration['id_module']) {
                continue;
            }

            if ((bool) $disable_non_native_modules && Hook::$native_module && count(Hook::$native_module) && !in_array($hookRegistration['module'], Hook::$native_module)) {
                continue;
            }

            $registeredHookId = $hookRegistration['id_hook'];
            if ($registeredHookId === $id_hook) {
                $registeredHookName = $hook_name;
            } else {
                $registeredHookName = static::getNameById($hookRegistration['id_hook']);
            }

            if ($check_exceptions) {
                $exceptions = Module::getExceptionsStatic($hookRegistration['id_module'], $hookRegistration['id_hook']);
                $controller_obj = Context::getContext()->controller;
                
                if ($controller_obj === null) {
                    $controller = null;
                } else {
                    $controller = isset($controller_obj->controller_name) ?
                        $controller_obj->controller_name : $controller_obj->php_self;
                }

                if (isset($controller_obj->module) && Validate::isLoadedObject($controller_obj->module)) {
                    $controller = 'module-' . $controller_obj->module->name . '-' . $controller;
                }

                if (in_array($controller, $exceptions)) {
                    continue;
                }

                $matching_name = ['authentication' => 'auth'];
                if (isset($matching_name[$controller]) && in_array($matching_name[$controller], $exceptions)) {
                    continue;
                }
                
                if (Validate::isLoadedObject($context->employee) && !Module::getPermissionStatic($hookRegistration['id_module'], 'view', $context->employee)) {
                    continue;
                }
            }

            if (!($moduleInstance = Module::getInstanceByName($hookRegistration['module']))) {
                continue;
            }

            if ($isRegistryEnabled) {
                $hookRegistry->hookedByModule($moduleInstance);
            }

            if (Hook::isHookCallableOn($moduleInstance, $registeredHookName)) {
                $hook_args['altern'] = ++$altern;

                if (0 !== $key && true === $chain) {
                    $hook_args = $output;
                }

                try {
                    $display = static::callHookOn($moduleInstance, $registeredHookName, $hook_args);
                } catch (Exception $e) {
                    // Log error and continue without breaking
                    if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                        PrestaShopLogger::addLog(
                            sprintf(
                                'Hook Error: Module "%s", Hook "%s", Error: %s',
                                $moduleInstance->name,
                                $registeredHookName,
                                $e->getMessage()
                            ),
                            3
                        );
                    }
                    $display = '';
                }

                if ($array_return) {
                    $output[$moduleInstance->name] = $display;
                } else {
                    if (true === $chain) {
                        $output = $display;
                    } else {
                        $output .= $display;
                    }
                }
                
                if ($isRegistryEnabled) {
                    $hookRegistry->hookedByCallback($moduleInstance, $hook_args);
                }
            } elseif (Hook::isDisplayHookName($registeredHookName)) {
                if ($moduleInstance instanceof WidgetInterface) {
                    if (0 !== $key && true === $chain) {
                        $hook_args = $output;
                    }

                    try {
                        $display = Hook::coreRenderWidget($moduleInstance, $registeredHookName, $hook_args);
                    } catch (Exception $e) {
                        if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                            PrestaShopLogger::addLog(
                                sprintf(
                                    'Widget Error: Module "%s", Hook "%s", Error: %s',
                                    $moduleInstance->name,
                                    $registeredHookName,
                                    $e->getMessage()
                                ),
                                3
                            );
                        }
                        $display = '';
                    }

                    if ($array_return) {
                        $output[$moduleInstance->name] = $display;
                    } else {
                        if (true === $chain) {
                            $output = $display;
                        } else {
                            $output .= $display;
                        }
                    }
                }

                if ($isRegistryEnabled) {
                    $hookRegistry->hookedByWidget($moduleInstance, $hook_args);
                }
            }
        }

        if ($different_shop && isset($old_shop, $old_context, $shop->id)) {
            $context->shop = $old_shop;
            $context->shop->setContext($old_context, $shop->id);
        }

        if (true === $chain) {
            if (isset($output['cookie'])) {
                unset($output['cookie']);
            }
            if (isset($output['cart'])) {
                unset($output['cart']);
            }
        }

        if ($isRegistryEnabled) {
            $hookRegistry->hookWasCalled();
            $hookRegistry->collect();
        }

        return $output;
    }

    /**
     * Call a hook on a module
     */
    protected static function callHookOn(Module $module, string $hookName, array $hookArgs)
    {
        try {
            $methodName = static::getMethodName($hookName);
            if (is_callable([$module, $methodName])) {
                return static::coreCallHook($module, $methodName, $hookArgs);
            }

            foreach (static::getAllKnownNames($hookName) as $hook) {
                $methodName = static::getMethodName($hook);
                if (is_callable([$module, $methodName])) {
                    return static::coreCallHook($module, $methodName, $hookArgs);
                }
            }
        } catch (Exception $e) {
            $environment = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Environment');
            if ($environment->isDebug()) {
                throw new CoreException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return '';
    }

    /**
     * Get method name
     */
    protected static function getMethodName(string $hookName): string
    {
        return 'hook' . ucfirst($hookName);
    }
}