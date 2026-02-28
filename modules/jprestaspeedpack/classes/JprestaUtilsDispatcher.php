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

if (!class_exists('JprestaUtilsDispatcher')) {
    class JprestaUtilsDispatcher extends Dispatcher
    {
        /**
         * @var JprestaUtilsDispatcher
         */
        public static $pc_instance;

        public static function getPageCacheInstance()
        {
            if (!self::$pc_instance) {
                self::$pc_instance = new JprestaUtilsDispatcher();
            }

            return self::$pc_instance;
        }

        public function getControllerFromURL($url, $id_shop = null)
        {
            $controller = false;

            if (isset(Context::getContext()->shop) && $id_shop === null) {
                $id_shop = (int) Context::getContext()->shop->id;
            }

            // Try to find it in URL query string (if no URL rewritting)
            $query = parse_url($url, PHP_URL_QUERY);
            if ($query) {
                $query = html_entity_decode($query);
                $keyvaluepairs = explode('&', $query);
                if ($keyvaluepairs !== false) {
                    $is_fc_module = false;
                    $module = false;
                    foreach ($keyvaluepairs as $keyvaluepair) {
                        if (strstr($keyvaluepair, '=') !== false) {
                            list($key, $value) = explode('=', $keyvaluepair);

                            if (strcmp('controller', $key) === 0) {
                                $controller = $value;
                            } elseif (strcmp('fc', $key) === 0) {
                                $is_fc_module = strcmp('module', $value) !== false;
                            } elseif (strcmp('module', $key) === 0) {
                                $module = $value;
                            }
                        }
                    }
                    if ($is_fc_module && $module) {
                        $controller = $module . '__' . $controller;
                    }
                }
            }

            if (!Validate::isControllerName($controller)) {
                $controller = false;
            }

            // If not found, try routes (if URL rewritting)
            if (!$controller && $this->use_routes) {
                // Language removed in pagecache.php
                $url_without_lang = $url;
                if (isset($this->routes[$id_shop][Context::getContext()->language->id])) {
                    $routes = $this->routes[$id_shop][Context::getContext()->language->id];
                } else {
                    $routes = $this->routes[$id_shop];
                }
                foreach ($routes as $route) {
                    if (@preg_match($route['regexp'], $url_without_lang, $m)) {
                        // Route found!
                        $controller = $route['controller'] ? $route['controller'] : false;
                        if ($controller) {
                            $urlParams = [];
                            // Route found ! Now fill $urlParams with parameters of uri
                            foreach ($m as $k => $v) {
                                if (!is_numeric($k)) {
                                    $urlParams[$k] = $v;
                                }
                            }
                            if (array_key_exists('controller', $urlParams)) {
                                $controller = $urlParams['controller'];
                            }
                            if (array_key_exists('fc', $route['params'])
                                && $route['params']['fc'] === 'module'
                                && array_key_exists('module', $route['params'])) {
                                $controller = $route['params']['module'] . '__' . $controller;
                            } elseif (array_key_exists('module', $urlParams)) {
                                $controller = $urlParams['module'] . '__' . $controller;
                            }
                        }
                        break;
                    }
                }
                if ((!$controller && Tools::strlen($url_without_lang) == 0) || $url_without_lang === '/') {
                    $controller = 'index';
                }
            }

            return $controller;
        }
    }
}
