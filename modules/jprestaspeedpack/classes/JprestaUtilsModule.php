<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaUtilsModule')) {
    class JprestaUtilsModule
    {
        private static $_cache = [];

        public static function isModuleController($controllerFullName)
        {
            return is_string($controllerFullName) && strpos($controllerFullName, '__') > 0;
        }

        private static function getInfos($controllerFullName)
        {
            if (!isset(self::$_cache[$controllerFullName])) {
                self::$_cache[$controllerFullName]['canBeCached'] = false;
                self::$_cache[$controllerFullName]['canBeWarmed'] = false;
                self::$_cache[$controllerFullName]['instance'] = null;
                self::$_cache[$controllerFullName]['modelObjectClassName'] = null;
                self::$_cache[$controllerFullName]['moduleName'] = null;
                self::$_cache[$controllerFullName]['controllerName'] = null;

                if (self::isModuleController($controllerFullName)) {
                    try {
                        list($moduleName, $controllerName) = explode('__', $controllerFullName);
                        $module = Module::getInstanceByName($moduleName);
                        if (Validate::isLoadedObject($module) && $module->active) {
                            self::$_cache[$controllerFullName]['moduleName'] = $moduleName;
                            self::$_cache[$controllerFullName]['controllerName'] = $controllerName;

                            $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_ . "$moduleName/controllers/front/");
                            if (isset($controllers[strtolower($controllerName)])) {
                                include_once _PS_MODULE_DIR_ . "$moduleName/controllers/front/{$controllerName}.php";
                                if (file_exists(
                                    _PS_OVERRIDE_DIR_ . "modules/$moduleName/controllers/front/{$controllerName}.php"
                                )) {
                                    include_once _PS_OVERRIDE_DIR_ . "modules/$moduleName/controllers/front/{$controllerName}.php";
                                    $controllerClassName = $moduleName . $controllerName . 'ModuleFrontControllerOverride';
                                } else {
                                    $controllerClassName = $moduleName . $controllerName . 'ModuleFrontController';
                                }
                                if (method_exists($controllerClassName, 'getJprestaModelObjectClassName')) {
                                    self::$_cache[$controllerFullName]['controllerClassName'] = $controllerClassName;
                                    self::$_cache[$controllerFullName]['modelObjectClassName'] = call_user_func($controllerClassName . '::getJprestaModelObjectClassName');
                                    self::$_cache[$controllerFullName]['canBeCached'] = (bool) self::$_cache[$controllerFullName]['modelObjectClassName'];
                                    self::$_cache[$controllerFullName]['canBeWarmed'] = method_exists($controllerClassName, 'getJprestaAllURLs');
                                }
                            }
                        }
                    } catch (Exception $e) {
                        JprestaUtils::addLog("PageCache | Error in JprestaUtilsModule::getInfos($controllerFullName) : " . $e->getMessage() . ' Trace: ' . JprestaUtils::jTraceEx($e));
                    }
                }
            }

            return self::$_cache[$controllerFullName];
        }

        public static function getModelObjectClassName($controllerFullName)
        {
            return self::getInfos($controllerFullName)['modelObjectClassName'];
        }

        public static function getModuleName($controllerFullName)
        {
            return self::getInfos($controllerFullName)['moduleName'];
        }

        public static function getControllerName($controllerFullName)
        {
            return self::getInfos($controllerFullName)['controllerName'];
        }

        public static function getControllerClassName($controllerFullName)
        {
            return self::getInfos($controllerFullName)['controllerClassName'];
        }

        public static function canBeCached($controllerFullName)
        {
            return self::getInfos($controllerFullName)['canBeCached'];
        }

        public static function canBeWarmed($controllerFullName)
        {
            return self::getInfos($controllerFullName)['canBeWarmed'];
        }

        public static function getAllURLs($controllerFullName, $id_lang)
        {
            try {
                if (self::getInfos($controllerFullName)['canBeWarmed']
                    && method_exists(self::getInfos($controllerFullName)['controllerClassName'], 'getJprestaAllURLs')
                ) {
                    return call_user_func(self::getInfos($controllerFullName)['controllerClassName'] . '::getJprestaAllURLs', $id_lang);
                }
            } catch (Exception $e) {
                JprestaUtils::addLog("PageCache | Error in JprestaUtilsModule::getAllURLs($controllerFullName, $id_lang) : " . $e->getMessage() . ' Trace: ' . JprestaUtils::jTraceEx($e));
            }

            return [];
        }

        public static function getAllURLsCount($controllerFullName)
        {
            try {
                if (self::getInfos($controllerFullName)['canBeWarmed']
                    && method_exists(self::getInfos($controllerFullName)['controllerClassName'], 'getJprestaAllURLsCount')
                ) {
                    return call_user_func(self::getInfos($controllerFullName)['controllerClassName'] . '::getJprestaAllURLsCount');
                }
            } catch (Exception $e) {
                JprestaUtils::addLog("PageCache | Error in JprestaUtilsModule::getAllURLsCount($controllerFullName) : " . $e->getMessage() . ' Trace: ' . JprestaUtils::jTraceEx($e));
            }

            return null;
        }
    }
}
