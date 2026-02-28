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

include_once dirname(__FILE__) . '/../../jprestaspeedpack.php';

class jprestaspeedpackClearcacheModuleFrontController extends ModuleFrontController
{
    public $php_self;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->setTemplate('clearcache.tpl');
        } else {
            $this->setTemplate('module:jprestaspeedpack/views/templates/front/clearcache.tpl');
        }

        $result = 'Not OK';
        if (JprestaUtils::isModuleEnabled('jprestaspeedpack')) {
            $token = Tools::getValue('token', '');
            $goodToken = JprestaUtils::getSecurityToken();
            if (!$goodToken || strcmp($goodToken, $token) === 0) {
                if (Tools::getIsset('nbHourExpired')) {
                    header('HTTP/1.0 404 Not Found');
                    $result = 'Parameter nbHourExpired is not supported anymore, the cache is now reduced continuously, this CRON is not necessary anymore.';
                } else {
                    $delete_linking_pages = (bool) Tools::getValue('delete_linking_pages', true);
                    $is_specific = false;
                    $is_purge = Tools::getIsset('purge');
                    foreach (Jprestaspeedpack::getManagedControllersNames() as $controller) {
                        if (Tools::getIsset($controller)) {
                            // It's not a global reset
                            $is_specific = true;

                            if (strcmp($controller, 'index') === 0
                                || strcmp($controller, 'newproducts') === 0
                                || strcmp($controller, 'bestsales') === 0
                                || strcmp($controller, 'pricesdrop') === 0
                                || strcmp($controller, 'sitemap') === 0
                                || strcmp($controller, 'contact') === 0
                                || strcmp($controller, 'sitemap') === 0
                            ) {
                                // No ids for this controller
                                // echo "Deleting $controller <br/>";
                                PageCacheDAO::clearCacheOfObject($controller, false, false, 'from CRON');
                            } else {
                                $ids_str = Tools::getValue($controller);
                                $ids = self::parseIds($ids_str);
                                if (empty($ids)) {
                                    PageCacheDAO::clearCacheOfObject($controller, false, $delete_linking_pages, 'from CRON');
                                } else {
                                    foreach ($ids as $id) {
                                        // Delete object one after the other
                                        // echo "Deleting $controller # $id <br/>";
                                        PageCacheDAO::clearCacheOfObject($controller, $id, $delete_linking_pages, 'from CRON');
                                    }
                                }
                            }
                        }
                    }
                    $result = 'OK';
                    if ($is_purge) {
                        // Purge the cache
                        $module = Module::getInstanceByName('jprestaspeedpack');
                        $module->purgeCache(Shop::getContextShopID(), 'cron' . (Jprestaspeedpack::isCacheWarmer() ? ' CW' : ''));
                        if (Jprestaspeedpack::isCacheWarmer()) {
                            // Reduce the size of the response to the minimum (save bandwidth and time)
                            if (!headers_sent()) {
                                // Here our cache-warmer/status-checker do not care about these headers, just remove them
                                header_remove();
                                // Indicates that there is no content so it removes "Content-Length" and "Content-Type" headers
                                header('HTTP/1.1 204 PURGED');
                                // Unset PHP session (to avoid a useless cookie)
                                session_abort();
                                // Don't send any cookies
                                Context::getContext()->cookie->disallowWriting();
                            }
                            exit;
                        }
                    } elseif (!$is_specific) {
                        // Clear the whole cache
                        $module = Module::getInstanceByName('jprestaspeedpack');
                        $module->clearCache('cron');
                    }
                }
            } else {
                header('HTTP/1.0 404 Not Found');
                $result = 'Not OK: bad token ' . $token;
            }
        } else {
            $result = 'Not OK: module not active';
        }
        $this->context->smarty->assign([
            'result' => $result,
        ]);
    }

    public function getLayout()
    {
        return _PS_MODULE_DIR_ . 'jprestaspeedpack/views/templates/front/layout.tpl';
    }

    /**
     * @param string $ids Comma separated list of IDs
     *
     * @return multitype:number Array of ID
     */
    private function parseIds($ids)
    {
        $ids_array = [];
        if (!empty($ids)) {
            $ids_str = explode(',', $ids);
            foreach ($ids_str as $id_str) {
                $id = (int) $id_str;
                if ($id > 0) {
                    $ids_array[] = $id;
                }
            }
        }

        return $ids_array;
    }
}
